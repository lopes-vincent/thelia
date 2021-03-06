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
use Thelia\Model\CurrencyQuery;
use Thelia\Model\Product;

/**
 * @author Franck Allimant <franck@cqfdev.fr>
 */
class RemoveAmountOnProductsTest extends TestCase
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

        $currencies = CurrencyQuery::create();
        $currencies = $currencies->find();
        $stubFacade->expects($this->any())
            ->method('getAvailableCurrencies')
            ->willReturn($currencies);

        $stubFacade->expects($this->any())
            ->method('getCartTotalPrice')
            ->willReturn($cartTotalPrice);

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

    public function generateMatchingCart(MockObject $stubFacade, $count): void
    {
        $product1 = new Product();
        $product1->setId(10);

        $product2 = new Product();
        $product2->setId(20);

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

        $cartItem2Stub = $this->getMockBuilder('\Thelia\Model\CartItem')
            ->disableOriginalConstructor()
            ->getMock();

        $cartItem2Stub
            ->expects($this->any())
            ->method('getProduct')
            ->willReturn($product2);

        $cartItem2Stub
            ->expects($this->any())
            ->method('getQuantity')
            ->willReturn(2)
        ;

        $cartStub = $this->getMockBuilder('\Thelia\Model\Cart')
            ->disableOriginalConstructor()
            ->getMock();

        if ($count == 1) {
            $ret = [$cartItem1Stub];
        } else {
            $ret = [$cartItem1Stub, $cartItem2Stub];
        }

        $cartStub
            ->expects($this->any())
            ->method('getCartItems')
            ->willReturn($ret);

        $stubFacade->expects($this->any())
            ->method('getCart')
            ->willReturn($cartStub);
    }

    public function generateNoMatchingCart(MockObject $stubFacade): void
    {
        $product2 = new Product();
        $product2->setId(30);

        $cartItem2Stub = $this->getMockBuilder('\Thelia\Model\CartItem')
            ->disableOriginalConstructor()
            ->getMock();

        $cartItem2Stub->expects($this->any())
            ->method('getProduct')
            ->willReturn($product2);

        $cartItem2Stub->expects($this->any())
            ->method('getQuantity')
            ->willReturn(2);

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

        $coupon = new RemoveAmountOnProducts($stubFacade);

        $date = new \DateTime();

        $coupon->set(
            $stubFacade,
            'TEST',
            'TEST Coupon',
            'This is a test coupon title',
            'This is a test coupon description',
            ['amount' => 10.00, 'products' => [10, 20]],
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

    public function testMatchOne(): void
    {
        $stubFacade = $this->generateFacadeStub();

        $coupon = new RemoveAmountOnProducts($stubFacade);

        $date = new \DateTime();

        $coupon->set(
            $stubFacade,
            'TEST',
            'TEST Coupon',
            'This is a test coupon title',
            'This is a test coupon description',
            ['amount' => 10.00, 'products' => [10, 20]],
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

        $this->generateMatchingCart($stubFacade, 1);

        $this->assertEquals(10.00, $coupon->exec());
    }

    public function testMatchSeveral(): void
    {
        $stubFacade = $this->generateFacadeStub();

        $coupon = new RemoveAmountOnProducts($stubFacade);

        $date = new \DateTime();

        $coupon->set(
            $stubFacade,
            'TEST',
            'TEST Coupon',
            'This is a test coupon title',
            'This is a test coupon description',
            ['amount' => 10.00, 'products' => [10, 20]],
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

        $this->generateMatchingCart($stubFacade, 2);

        $this->assertEquals(30.00, $coupon->exec());
    }

    public function testNoMatch(): void
    {
        $stubFacade = $this->generateFacadeStub();

        $coupon = new RemoveAmountOnProducts($stubFacade);

        $date = new \DateTime();

        $coupon->set(
            $stubFacade,
            'TEST',
            'TEST Coupon',
            'This is a test coupon title',
            'This is a test coupon description',
            ['amount' => 10.00, 'products' => [10, 20]],
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

    public function testGetName(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Coupon test name');

        /** @var FacadeInterface $stubFacade */
        $coupon = new RemoveAmountOnProducts($stubFacade);

        $actual = $coupon->getName();
        $expected = 'Coupon test name';
        $this->assertEquals($expected, $actual);
    }

    public function testGetToolTip(): void
    {
        $tooltip = 'Coupon test tooltip';
        $stubFacade = $this->generateFacadeStub(399, 'EUR', $tooltip);

        /** @var FacadeInterface $stubFacade */
        $coupon = new RemoveAmountOnProducts($stubFacade);

        $actual = $coupon->getToolTip();
        $expected = $tooltip;
        $this->assertEquals($expected, $actual);
    }
}
