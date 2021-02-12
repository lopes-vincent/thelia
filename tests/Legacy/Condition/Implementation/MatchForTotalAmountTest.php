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

namespace Thelia\Tests\Condition\Implementation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Thelia\Condition\ConditionCollection;
use Thelia\Condition\ConditionEvaluator;
use Thelia\Condition\ConditionFactory;
use Thelia\Condition\Implementation\MatchForTotalAmount;
use Thelia\Condition\Operators;
use Thelia\Coupon\FacadeInterface;
use Thelia\Model\Currency;
use Thelia\Model\CurrencyQuery;

/**
 * Unit Test MatchForTotalAmount Class.
 *
 * @author  Guillaume MOREL <gmorel@openstudio.fr>
 */
class MatchForTotalAmountTest extends TestCase
{
    /** @var FacadeInterface $stubTheliaAdapter */
    protected $stubTheliaAdapter;

    /**
     * Generate adapter stub.
     *
     * @param int    $cartTotalPrice   Cart total price
     * @param string $checkoutCurrency Checkout currency
     *
     * @return MockObject
     */
    public function generateAdapterStub($cartTotalPrice = 400, $checkoutCurrency = 'EUR')
    {
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getCartTotalTaxPrice')
            ->willReturn($cartTotalPrice);

        $stubFacade->expects($this->any())
            ->method('getCheckoutCurrency')
            ->willReturn($checkoutCurrency);

        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $currency1 = new Currency();
        $currency1->setCode('EUR');
        $currency2 = new Currency();
        $currency2->setCode('USD');
        $stubFacade->expects($this->any())
            ->method('getAvailableCurrencies')
            ->willReturn([$currency1, $currency2]);

        return $stubFacade;
    }

    /**
     * Check if validity test on BackOffice inputs are working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::setValidators
     */
    public function testInValidBackOfficeInputOperator(): void
    {
        $stubFacade = $this->generateAdapterStub(399, 'EUR');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::IN,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => '400',
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];

        $this->expectException(\Thelia\Exception\InvalidConditionOperatorException::class);
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if validity test on BackOffice inputs are working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::setValidators
     */
    public function testInValidBackOfficeInputOperator2(): void
    {
        $stubFacade = $this->generateAdapterStub(399, 'EUR');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::SUPERIOR,
            MatchForTotalAmount::CART_CURRENCY => Operators::INFERIOR,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => '400',
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];

        $this->expectException(\Thelia\Exception\InvalidConditionOperatorException::class);
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if validity test on BackOffice inputs are working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::setValidators
     */
    public function testInValidBackOfficeInputValue(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(399, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::SUPERIOR,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 'X',
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];

        $this->expectException(\Thelia\Exception\InvalidConditionValueException::class);
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if validity test on BackOffice inputs are working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::setValidators
     */
    public function testInValidBackOfficeInputValue2(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(399, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::SUPERIOR,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400,
            MatchForTotalAmount::CART_CURRENCY => 'FLA', ];
        $this->expectException(\Thelia\Exception\InvalidConditionValueException::class);
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testMatchingConditionInferior(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(399, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::INFERIOR,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testNotMatchingConditionInferior(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(400, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::INFERIOR,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testMatchingConditionInferiorEquals(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(400, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::INFERIOR_OR_EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testMatchingConditionInferiorEquals2(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(399, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::INFERIOR_OR_EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testNotMatchingConditionInferiorEquals(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(401, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::INFERIOR_OR_EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test equals operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testMatchingConditionEqual(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(400, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test equals operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testNotMatchingConditionEqual(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(399, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testMatchingConditionSuperiorEquals(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(401, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::SUPERIOR_OR_EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testMatchingConditionSuperiorEquals2(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(400, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::SUPERIOR_OR_EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testNotMatchingConditionSuperiorEquals(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(399, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::SUPERIOR_OR_EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testMatchingConditionSuperior(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(401, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::SUPERIOR,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testNotMatchingConditionSuperior(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(399, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::SUPERIOR,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check currency is checked.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testMatchingConditionCurrency(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(400, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check currency is checked.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::isMatching
     */
    public function testNotMatchingConditionCurrency(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateAdapterStub(400.00, 'EUR');

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'USD', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check unknown currency.
     *
     * @covers \Thelia\Condition\Implementation\ConditionAbstract::isCurrencyValid
     */
    public function testUnknownCurrencyCode(): void
    {
        $stubTranslator = $this->getMockBuilder('\Thelia\Core\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getTranslator')
            ->willReturn($stubTranslator);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $currencies = CurrencyQuery::create();
        $currencies = $currencies->find();
        $stubFacade->expects($this->any())
            ->method('getAvailableCurrencies')
            ->willReturn($currencies);

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'UNK', ];

        $this->expectException(\Thelia\Exception\InvalidConditionValueException::class);
        $condition1->setValidatorsFromForm($operators, $values);

        $stubContainer = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();
        $stubContainer->expects($this->any())
            ->method('get')
            ->willReturn($condition1);

        $stubContainer->expects($this->any())
            ->method('has')
            ->willReturn(true);

        $stubFacade->expects($this->any())
            ->method('getContainer')
            ->willReturn($stubContainer);

        $conditionFactory = new ConditionFactory($stubContainer);

        $collection = new ConditionCollection();
        $collection[] = $condition1;

        $serialized = $conditionFactory->serializeConditionCollection($collection);
        $conditionFactory->unserializeConditionCollection($serialized);
    }

    /**
     * Check invalid currency.
     *
     * @covers \Thelia\Condition\Implementation\ConditionAbstract::isPriceValid
     */
    public function testInvalidCurrencyValue(): void
    {
        $stubTranslator = $this->getMockBuilder('\Thelia\Core\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getTranslator')
            ->willReturn($stubTranslator);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $currencies = CurrencyQuery::create();
        $currencies = $currencies->find();
        $stubFacade->expects($this->any())
            ->method('getAvailableCurrencies')
            ->willReturn($currencies);

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 'notfloat',
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];

        $this->expectException(\Thelia\Exception\InvalidConditionValueException::class);
        $condition1->setValidatorsFromForm($operators, $values);

        $stubContainer = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();
        $stubContainer->expects($this->any())
            ->method('get')
            ->willReturn($condition1);

        $stubContainer->expects($this->any())
            ->method('has')
            ->willReturn(true);

        $stubFacade->expects($this->any())
            ->method('getContainer')
            ->willReturn($stubContainer);

        $conditionFactory = new ConditionFactory($stubContainer);

        $collection = new ConditionCollection();
        $collection[] = $condition1;

        $serialized = $conditionFactory->serializeConditionCollection($collection);
        $conditionFactory->unserializeConditionCollection($serialized);
    }

    /**
     * Check invalid currency.
     *
     * @covers \Thelia\Condition\Implementation\ConditionAbstract::isPriceValid
     */
    public function testPriceAsZero(): void
    {
        $stubTranslator = $this->getMockBuilder('\Thelia\Core\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getTranslator')
            ->willReturn($stubTranslator);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $currencies = CurrencyQuery::create();
        $currencies = $currencies->find();
        $stubFacade->expects($this->any())
            ->method('getAvailableCurrencies')
            ->willReturn($currencies);

        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 0.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];

        $this->expectException(\Thelia\Exception\InvalidConditionValueException::class);
        $condition1->setValidatorsFromForm($operators, $values);

        $stubContainer = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();
        $stubContainer->expects($this->any())
            ->method('get')
            ->willReturn($condition1);

        $stubContainer->expects($this->any())
            ->method('has')
            ->willReturn(true);

        $stubFacade->expects($this->any())
            ->method('getContainer')
            ->willReturn($stubContainer);

        $conditionFactory = new ConditionFactory($stubContainer);

        $collection = new ConditionCollection();
        $collection[] = $condition1;

        $serialized = $conditionFactory->serializeConditionCollection($collection);
        $conditionFactory->unserializeConditionCollection($serialized);
    }

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

        $currency1 = new Currency();
        $currency1->setCode('EUR');
        $currency2 = new Currency();
        $currency2->setCode('USD');
        $stubFacade->expects($this->any())
            ->method('getAvailableCurrencies')
            ->willReturn([$currency1, $currency2]);

        return $stubFacade;
    }

    /**
     * Check getName i18n.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::getName
     */
    public function testGetName(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Cart total amount');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForTotalAmount($stubFacade);

        $actual = $condition1->getName();
        $expected = 'Cart total amount';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check tooltip i18n.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::getToolTip
     */
    public function testGetToolTip(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'If cart total amount is <strong>%operator%</strong> %amount% %currency%');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $actual = $condition1->getToolTip();
        $expected = 'If cart total amount is <strong>%operator%</strong> %amount% %currency%';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check validator.
     *
     * @covers \Thelia\Condition\Implementation\MatchForTotalAmount::generateInputs
     */
    public function testGetValidator(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Price');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForTotalAmount($stubFacade);
        $operators = [
            MatchForTotalAmount::CART_TOTAL => Operators::EQUAL,
            MatchForTotalAmount::CART_CURRENCY => Operators::EQUAL,
        ];
        $values = [
            MatchForTotalAmount::CART_TOTAL => 400.00,
            MatchForTotalAmount::CART_CURRENCY => 'EUR', ];
        $condition1->setValidatorsFromForm($operators, $values);

        $actual = $condition1->getValidators();

        $validators = [
            'inputs' => [
                MatchForTotalAmount::CART_TOTAL => [
                    'availableOperators' => [
                        '<' => 'Price',
                        '<=' => 'Price',
                        '==' => 'Price',
                        '>=' => 'Price',
                        '>' => 'Price',
                    ],
                    'availableValues' => '',
                    'value' => '',
                    'selectedOperator' => '',
                ],
                MatchForTotalAmount::CART_CURRENCY => [
                    'availableOperators' => ['==' => 'Price'],
                    'availableValues' => [
                        'EUR' => '€',
                        'USD' => '$',
                        'GBP' => '£',
                    ],
                    'value' => '',
                    'selectedOperator' => Operators::EQUAL,
                ],
            ],
            'setOperators' => [
                'price' => '==',
                'currency' => '==',
            ],
            'setValues' => [
                'price' => 400,
                'currency' => 'EUR',
            ],
        ];
        $expected = $validators;

        $this->assertEquals($expected, $actual);
    }
}
