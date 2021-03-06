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
use Thelia\Model\Address;
use Thelia\Model\Lang;

/**
 * @author Franck Allimant <franck@cqfdev.fr>
 */
class StartDateTest extends TestCase
{
    public $startDate;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->startDate = time() - 2000;
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

        $address = new Address();
        $address->setCountryId(10);

        $stubFacade->expects($this->any())
            ->method('getDeliveryAddress')
            ->willReturn($address);

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

        $lang = new Lang();
        $lang->setDateFormat('d/m/Y');

        $stubSession = $this->getMockBuilder('\Thelia\Core\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $stubSession->expects($this->any())
            ->method('getLang')
            ->willReturn($lang);

        $stubRequest = $this->getMockBuilder('\Thelia\Core\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $stubRequest->expects($this->any())
            ->method('getSession')
            ->willReturn($stubSession);

        $stubFacade->expects($this->any())
            ->method('getRequest')
            ->willReturn($stubRequest);

        return $stubFacade;
    }

    /**
     * Check if validity test on BackOffice inputs are working.
     *
     * @covers \Thelia\Condition\Implementation\StartDate::setValidators
     */
    public function testInValidBackOfficeInputOperator(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateFacadeStub();

        $condition1 = new StartDate($stubFacade);

        $operators = [
            StartDate::START_DATE => 'petite licorne',
        ];
        $values = [
            StartDate::START_DATE => $this->startDate,
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
     * @covers \Thelia\Condition\Implementation\StartDate::setValidators
     */
    public function testInValidBackOfficeInputValue(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateFacadeStub();

        $condition1 = new StartDate($stubFacade);
        $operators = [
            StartDate::START_DATE => Operators::SUPERIOR_OR_EQUAL,
        ];
        $values = [
            StartDate::START_DATE => 'petit poney',
        ];

        $this->expectException(\Thelia\Exception\InvalidConditionValueException::class);
        $condition1->setValidatorsFromForm($operators, $values);
    }

    /**
     * Check if test inferior operator is working.
     *
     * @covers \Thelia\Condition\Implementation\StartDate::isMatching
     */
    public function testMatchingRule(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateFacadeStub();

        $condition1 = new StartDate($stubFacade);
        $operators = [
            StartDate::START_DATE => Operators::SUPERIOR_OR_EQUAL,
        ];
        $values = [
            StartDate::START_DATE => $this->startDate,
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
     * @covers \Thelia\Condition\Implementation\StartDate::isMatching
     */
    public function testNotMatching(): void
    {
        /** @var FacadeInterface $stubFacade */
        $stubFacade = $this->generateFacadeStub();

        $condition1 = new StartDate($stubFacade);

        $operators = [
            StartDate::START_DATE => Operators::SUPERIOR_OR_EQUAL,
        ];
        $values = [
            StartDate::START_DATE => time() + 2000,
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
        $stubFacade = $this->generateFacadeStub();

        $condition1 = new StartDate($stubFacade);

        $operators = [
            StartDate::START_DATE => Operators::SUPERIOR_OR_EQUAL,
        ];
        $values = [
            StartDate::START_DATE => $this->startDate,
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
     * Check getName i18n.
     *
     * @covers \Thelia\Condition\Implementation\StartDate::getName
     */
    public function testGetName(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Number of articles in cart');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new StartDate($stubFacade);

        $actual = $condition1->getName();
        $expected = 'Number of articles in cart';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check tooltip i18n.
     *
     * @covers \Thelia\Condition\Implementation\StartDate::getToolTip
     */
    public function testGetToolTip(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Sample coupon condition');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new StartDate($stubFacade);

        $actual = $condition1->getToolTip();
        $expected = 'Sample coupon condition';
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check validator.
     *
     * @covers \Thelia\Condition\Implementation\StartDate::generateInputs
     */
    public function testGetValidator(): void
    {
        $stubFacade = $this->generateFacadeStub(399, 'EUR', 'Price');

        /** @var FacadeInterface $stubFacade */
        $condition1 = new StartDate($stubFacade);

        $operators = [
            StartDate::START_DATE => Operators::SUPERIOR_OR_EQUAL,
        ];
        $values = [
            StartDate::START_DATE => $this->startDate,
        ];

        $condition1->setValidatorsFromForm($operators, $values);

        $actual = $condition1->getValidators();

        $validators = [
            'inputs' => [
                StartDate::START_DATE => [
                    'availableOperators' => [
                        '>=' => 'Price',
                    ],
                    'value' => '',
                    'selectedOperator' => '>=',
                ],
            ],
            'setOperators' => [
                'start_date' => '>=',
            ],
            'setValues' => [
                'start_date' => $this->startDate,
            ],
        ];
        $expected = $validators;

        $this->assertEquals($expected, $actual);
    }
}
