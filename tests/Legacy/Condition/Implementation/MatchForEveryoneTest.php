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
use Thelia\Coupon\FacadeInterface;
use Thelia\Model\Currency;

/**
 * Unit Test MatchForEveryone Class.
 *
 * @author  Guillaume MOREL <gmorel@openstudio.fr>
 */
class MatchForEveryoneTest extends TestCase
{
    /** @var FacadeInterface $stubTheliaAdapter */
    protected $stubTheliaAdapter;

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
     * Check if validity test on BackOffice inputs are working.
     *
     * @covers \Thelia\Condition\Implementation\MatchForEveryone::setValidators
     */
    public function testValidBackOfficeInputOperator(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForEveryone($stubFacade);
        $operators = [];
        $values = [];
        $condition1->setValidatorsFromForm($operators, $values);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if condition is always matching.
     *
     * @covers \Thelia\Condition\Implementation\MatchForEveryone::isMatching
     */
    public function testIsMatching(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForEveryone($stubFacade);

        $isValid = $condition1->isMatching();

        $expected = true;
        $actual = $isValid;
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check getName i18n.
     *
     * @covers \Thelia\Condition\Implementation\MatchForEveryone::getName
     */
    public function testGetName(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Everybody can use it (no condition)');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForEveryone($stubFacade);

        $actual = $condition1->getName();
        $expected = 'Everybody can use it (no condition)';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check tooltip i18n.
     *
     * @covers \Thelia\Condition\Implementation\MatchForEveryone::getToolTip
     */
    public function testGetToolTip(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Will return always true');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForEveryone($stubFacade);

        $actual = $condition1->getToolTip();
        $expected = 'Will return always true';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check validator.
     *
     * @covers \Thelia\Condition\Implementation\MatchForEveryone::generateInputs
     * @covers \Thelia\Condition\Implementation\MatchForEveryone::setValidatorsFromForm
     */
    public function testGetValidator(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new MatchForEveryone($stubFacade);
        $actual1 = $condition1->setValidatorsFromForm([], []);
        $expected1 = $condition1;
        $actual2 = $condition1->getValidators();

        $validators = [];
        $validators['inputs'] = [];
        $validators['setOperators'] = [];
        $validators['setValues'] = [];
        $expected2 = $validators;

        $this->assertEquals($expected1, $actual1);
        $this->assertEquals($expected2, $actual2);
    }
}
