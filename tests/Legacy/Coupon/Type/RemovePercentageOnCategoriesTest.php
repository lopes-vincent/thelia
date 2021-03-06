<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Coupon\Type;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Propel\Runtime\Collection\ObjectCollection;
use Thelia\Condition\ConditionCollection;
use Thelia\Condition\ConditionEvaluator;
use Thelia\Condition\Implementation\MatchForTotalAmount;
use Thelia\Condition\Operators;
use Thelia\Coupon\FacadeInterface;
use Thelia\Model\Category;
use Thelia\Model\CountryQuery;
use Thelia\Model\CurrencyQuery;
use Thelia\Model\Product;

/**
 * @author Franck Allimant <franck@cqfdev.fr>
 */
class RemovePercentageOnCategoriesTest extends TestCase
{
    /**
     * Generate adapter stub.
     *
     * @param int    $cartTotalPrice   Cart total price
     * @param string $checkoutCurrency Checkout currency
     * @param string $i18nOutput       Output from each translation
     *
     * @return MockObject
     */
    public function generateFacadeStub($cartTotalPrice = 400, $checkoutCurrency = 'EUR', $i18nOutput = '')
    {
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $defaultCountry = CountryQuery::create()
            ->filterByByDefault(1)
            ->findOne();

        $currencies = CurrencyQuery::create();
        $currencies = $currencies->find();
        $stubFacade->expects($this->any())
            ->method('getAvailableCurrencies')
            ->willReturn($currencies);

        $stubFacade->expects($this->any())
            ->method('getCartTotalPrice')
            ->willReturn($cartTotalPrice);

        $stubFacade->expects($this->any())
            ->method('getDeliveryCountry')
            ->willReturn($defaultCountry);

        $stubFacade->expects($this->any())
            ->method('getCheckoutCurrency')
            ->willReturn($checkoutCurrency);

        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $stubTranslator = $this->getMockBuilder('\Thelia\Core\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();
        $stubTranslator->expects($this->any())
            ->method('trans')
            ->willReturn($i18nOutput);

        $stubFacade->expects($this->any())
            ->method('getTranslator')
            ->willReturn($stubTranslator);

        return $stubFacade;
    }

    public function generateMatchingCart(MockObject $stubFacade): void
    {
        $category1 = new Category();
        $category1->setId(10);

        $category2 = new Category();
        $category2->setId(20);

        $category3 = new Category();
        $category3->setId(30);

        $product1 = new Product();
        $product1->addCategory($category1)->addCategory($category2);

        $product2 = new Product();
        $product2->addCategory($category3);

        $cartItem1Stub = $this->getMockBuilder('\Thelia\Model\CartItem')
            ->disableOriginalConstructor()
            ->getMock();

        $cartItem1Stub
            ->expects($this->any())
            ->method('getProduct')
            ->willReturn($product1)
        ;
        $cartItem1Stub
            ->expects($this->any())
            ->method('getQuantity')
            ->willReturn(1)
        ;
        $cartItem1Stub
            ->expects($this->any())
            ->method('getRealTaxedPrice')
            ->willReturn(100)
        ;
        $cartItem1Stub
            ->expects($this->any())
            ->method('getTotalRealTaxedPrice')
            ->willReturn(100)
        ;

        $cartItem2Stub = $this->getMockBuilder('\Thelia\Model\CartItem')
            ->disableOriginalConstructor()
            ->getMock();

        $cartItem2Stub
            ->expects($this->any())
            ->method('getProduct')
            ->willReturn($product2)
        ;
        $cartItem2Stub
            ->expects($this->any())
            ->method('getQuantity')
            ->willReturn(2)
        ;
        $cartItem2Stub
            ->expects($this->any())
            ->method('getRealTaxedPrice')
            ->willReturn(200)
        ;
        $cartItem2Stub
            ->expects($this->any())
            ->method('getTotalRealTaxedPrice')
            ->willReturn(400)
        ;

        $cartStub = $this->getMockBuilder('\Thelia\Model\Cart')
            ->disableOriginalConstructor()
            ->getMock();

        $cartStub
            ->expects($this->any())
            ->method('getCartItems')
            ->willReturn([$cartItem1Stub, $cartItem2Stub]);

        $stubFacade->expects($this->any())
            ->method('getCart')
            ->willReturn($cartStub);
    }

    public function generateNoMatchingCart(MockObject $stubFacade): void
    {
        $category3 = new Category();
        $category3->setId(30);

        $product2 = new Product();
        $product2->addCategory($category3);

        $cartItem2Stub = $this->getMockBuilder('\Thelia\Model\CartItem')
            ->disableOriginalConstructor()
            ->getMock();

        $cartItem2Stub->expects($this->any())
            ->method('getProduct')
            ->willReturn($product2);

        $cartItem2Stub->expects($this->any())
            ->method('getQuantity')
            ->willReturn(2);

        $cartItem2Stub
            ->expects($this->any())
            ->method('getRealTaxedPrice')
            ->willReturn(200000)
        ;
        $cartItem2Stub
            ->expects($this->any())
            ->method('getTotalRealTaxedPrice')
            ->willReturn(400000)
        ;

        $cartStub = $this->getMockBuilder('\Thelia\Model\Cart')
            ->disableOriginalConstructor()
            ->getMock();

        $cartStub
            ->expects($this->any())
            ->method('getCartItems')
            ->willReturn([$cartItem2Stub]);

        $stubFacade->expects($this->any())
            ->method('getCart')
            ->willReturn($cartStub);
    }

    public function testSet(): void
    {
        $stubFacade = $this->generateFacadeStub();

        $coupon = new RemovePercentageOnCategories($stubFacade);

        $date = new \DateTime();

        $coupon->set(
            $stubFacade,
            'TEST',
            'TEST Coupon',
            'This is a test coupon title',
            'This is a test coupon description',
            ['percentage' => 10, 'categories' => [10, 20]],
            true,
            true,
            true,
            true,
            254,
            $date->setTimestamp(strtotime('today + 3 months')),
            new ObjectCollection(),
            new ObjectCollection(),
            false
        );

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::SUPERIOR,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 40.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR',
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $condition2 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::INFERIOR,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR',
        ];
        $condition2->setValidatorsFromForm($operators, $values);

        $conditions = new ConditionCollection();
        $conditions[] = $condition1;
        $conditions[] = $condition2;
        $coupon->setConditions($conditions);

        $this->assertEquals('TEST', $coupon->getCode());
        $this->assertEquals('TEST Coupon', $coupon->getTitle());
        $this->assertEquals('This is a test coupon title', $coupon->getShortDescription());
        $this->assertEquals('This is a test coupon description', $coupon->getDescription());

        $this->assertTrue($coupon->isCumulative());
        $this->assertTrue($coupon->isRemovingPostage());
        $this->assertTrue($coupon->isAvailableOnSpecialOffers());
        $this->assertTrue($coupon->isEnabled());

        $this->assertEquals(254, $coupon->getMaxUsage());
        $this->assertEquals($date, $coupon->getExpirationDate());
    }

    public function testMatch(): void
    {
        $stubFacade = $this->generateFacadeStub();

        $coupon = new RemovePercentageOnCategories($stubFacade);

        $date = new \DateTime();

        $coupon->set(
            $stubFacade,
            'TEST',
            'TEST Coupon',
            'This is a test coupon title',
            'This is a test coupon description',
            ['percentage' => 10, 'categories' => [10, 20]],
            true,
            true,
            true,
            true,
            254,
            $date->setTimestamp(strtotime('today + 3 months')),
            new ObjectCollection(),
            new ObjectCollection(),
            false
        );

        $this->generateMatchingCart($stubFacade);

        $this->assertEquals(10.00, $coupon->exec());
    }

    public function testNoMatch(): void
    {
        $stubFacade = $this->generateFacadeStub();

        $coupon = new RemovePercentageOnCategories($stubFacade);

        $date = new \DateTime();

        $coupon->set(
            $stubFacade,
            'TEST',
            'TEST Coupon',
            'This is a test coupon title',
            'This is a test coupon description',
            ['percentage' => 10, 'categories' => [10, 20]],
            true,
            true,
            true,
            true,
            254,
            $date->setTimestamp(strtotime('today + 3 months')),
            new ObjectCollection(),
            new ObjectCollection(),
            false
        );

        $this->generateNoMatchingCart($stubFacade);

        $this->assertEquals(0.00, $coupon->exec());
    }

    /**
     * @covers \Thelia\Coupon\Type\RemoveXAmount::getName
     */
    public function testGetName(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Coupon test name');

        /** @var FacadeInterface $stubFacade */
        $coupon = new RemovePercentageOnCategories($stubFacade);

        $actual = $coupon->getName();
        $expected = 'Coupon test name';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \Thelia\Coupon\Type\RemoveXAmount::getToolTip
     */
    public function testGetToolTip(): void
    {
        $tooltip = 'Coupon test tooltip';
        $stubFacade = $this->generateFacadeStub(399, 'EUR', $tooltip);

        /** @var FacadeInterface $stubFacade */
        $coupon = new RemovePercentageOnCategories($stubFacade);

        $actual = $coupon->getToolTip();
        $expected = $tooltip;
        $this->assertEquals($expected, $actual);
    }
}
