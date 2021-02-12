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

namespace Thelia\Condition\Implementation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Thelia\Condition\ConditionEvaluator;
use Thelia\Condition\Operators;
use Thelia\Condition\SerializableCondition;
use Thelia\Coupon\FacadeInterface;

/**
 * Unit Test MatchForXArticles Class.
 *
 * @author  Guillaume MOREL <gmorel@openstudio.fr>
 */
class MatchForXArticlesTest extends TestCase
{
    /**
     * Check if validity test on BackOffice inputs are working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::setValidators
     */
    public function testInValidBackOfficeInputOperator(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var FacadeInterface $stubFacade */
        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::IN,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 5,
        ];

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
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::setValidators
     */
    public function testInValidBackOfficeInputValue(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::SUPERIOR,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 'X',
        ];

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
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testMatchingRuleInferior(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::INFERIOR,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 5,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testNotMatchingRuleInferior(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::INFERIOR,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 4,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testMatchingRuleInferiorEquals(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::INFERIOR_OR_EQUAL,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 5,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testMatchingRuleInferiorEquals2(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::INFERIOR_OR_EQUAL,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 4,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testNotMatchingRuleInferiorEquals(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::INFERIOR_OR_EQUAL,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 3,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test equals operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testMatchingRuleEqual(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::EQUAL,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 4,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test equals operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testNotMatchingRuleEqual(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::EQUAL,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 5,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testMatchingRuleSuperiorEquals(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::SUPERIOR_OR_EQUAL,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 4,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testMatchingRuleSuperiorEquals2(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::SUPERIOR_OR_EQUAL,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 3,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testNotMatchingRuleSuperiorEquals(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::SUPERIOR_OR_EQUAL,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 5,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testMatchingRuleSuperior(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::SUPERIOR,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 3,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if test superior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::isMatching
     */
    public function testNotMatchingRuleSuperior(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::SUPERIOR,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 4,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = false;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    public function testGetSerializableRule(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->getMockBuilder('\Thelia\Coupon\BaseFacade')
            ->disableOriginalConstructor()
            ->getMock();

        $stubFacade->expects($this->any())
            ->method('getNbArticlesInCart')
            ->willReturn(4);
        $stubFacade->expects($this->any())
            ->method('getConditionEvaluator')
            ->willReturn(new ConditionEvaluator());

        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::SUPERIOR,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 4,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $serializableRule = $condition1->getSerializableCondition();

        $expected = new SerializableCondition();
        $expected->conditionServiceId = $condition1->getServiceId();
        $expected->operators = $operators;
        $expected->values = $values;

        $actual = $serializableRule;

        $this->assertEquals($expected, $actual);
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

        return $stubFacade;
    }

    /**
     * Check getName i18n.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::getName
     */
    public function testGetName(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Number of articles in cart');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForXArticles($stubFacade);

        $actual = $condition1->getName();
        $expected = 'Number of articles in cart';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check tooltip i18n.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::getToolTip
     */
    public function testGetToolTip(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'If cart products quantity is <strong>superior to</strong> 4');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::SUPERIOR,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 4,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $actual = $condition1->getToolTip();
        $expected = 'If cart products quantity is <strong>superior to</strong> 4';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check validator.
     *
     * @covers \Thelia\Condition\Implementation\MatchForXArticles::generateInputs
     */
    public function testGetValidator(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Price');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForXArticles($stubFacade);
        $operators = [
            MatchForXArticles::CART_QUANTITY => Operators::SUPERIOR,
        ];
        $values = [
            MatchForXArticles::CART_QUANTITY => 4,
        ];
        $condition1->setValidatorsFromForm($operators, $values);

        $actual = $condition1->getValidators();

        $validators = [
            'inputs' => [
                MatchForXArticles::CART_QUANTITY => [
                    'availableOperators' => [
                        '<' => 'Price',
                        '<=' => 'Price',
                        '==' => 'Price',
                        '>=' => 'Price',
                        '>' => 'Price',
                    ],
                    'value' => '',
                    'selectedOperator' => '',
                ],
            ],
            'setOperators' => [
                'quantity' => '>',
            ],
            'setValues' => [
                'quantity' => 4,
            ],
        ];
        $expected = $validators;

        $this->assertEquals($expected, $actual);
    }
}
