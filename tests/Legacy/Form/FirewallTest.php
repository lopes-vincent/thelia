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

namespace Thelia\Tests\Form;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ConfigQuery;
use Thelia\Model\FormFirewallQuery;
use Thelia\Model\Map\FormFirewallTableMap;

/**
 * Class FirewallTest.
 *
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class FirewallTest extends TestCase
{
    /** @var \Thelia\Core\HttpFoundation\Request */
    protected $request;

    /** @var MockObject */
    protected $form;

    public function setUp(): void
    {
        $session = new Session();

        $this->request = $this->createMock("\Thelia\Core\HttpFoundation\Request");
        $this->request
            ->expects($this->any())
                ->method('getClientIp')
                    ->willReturn('127.0.0.1')
        ;

        $this->request
            ->expects($this->any())
                ->method('getSession')
                    ->willReturn($session)
        ;

        $requestStack = new RequestStack();
        $requestStack->push($this->request);
        new Translator($requestStack);

        /*
         * Get an example form. We
         */
        $this->form = $this->getMockClass(
            "\Thelia\Form\FirewallForm",
            [
                'buildForm',
                'getName',
            ],
            [
                $this->request,
            ]
        );

        /*
         * Be sure that the firewall is active
         */
        ConfigQuery::write('form_firewall_active', 1);
        ConfigQuery::write('form_firewall_time_to_wait', 60);
        ConfigQuery::write('form_firewall_attempts', 6);

        /*
         * Empty the firewall blacklist between each test
         */
        FormFirewallQuery::create()->find()->delete();
    }

    public function testBlock(): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            if ($i > 6) {
                $this->assertFalse(
                    $this->form->isFirewallOk('prod')
                );
            } else {
                $this->assertTrue(
                    $this->form->isFirewallOk('prod')
                );
            }

            $attempts = FormFirewallQuery::create()
                ->select(FormFirewallTableMap::COL_ATTEMPTS)
                ->findOne()
            ;

            $this->assertEquals($i > 6 ? 6 : $i, $attempts);
        }
    }

    public function testFormatTime(): void
    {
        $this->assertEquals(
            '1 hour(s)',
            $this->form->getWaitingTime()
        );

        ConfigQuery::write('form_firewall_time_to_wait', 61);

        $this->assertEquals(
            '1 hour(s) 1 minute(s)',
            $this->form->getWaitingTime()
        );

        ConfigQuery::write('form_firewall_time_to_wait', 59);

        $this->assertEquals(
            '59 minute(s)',
            $this->form->getWaitingTime()
        );

        ConfigQuery::write('form_firewall_time_to_wait', 5);

        $this->assertEquals(
            '5 minute(s)',
            $this->form->getWaitingTime()
        );

        ConfigQuery::write('form_firewall_time_to_wait', 132);

        $this->assertEquals(
            '2 hour(s) 12 minute(s)',
            $this->form->getWaitingTime()
        );
    }

    public function testAutoDelete(): void
    {
        /* Add two rows */
        $this->form->isFirewallOk('prod');

        $this->form
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test_form_firewall_2'))
        ;

        $this->form->isFirewallOk('prod');

        /* Set the time to 1h and 1s after the limit */
        FormFirewallQuery::create()
            ->findOne()
            ->setUpdatedAt(date('Y-m-d G:i:s', time() - 3601))
            ->save()
        ;

        $this->form->isFirewallOk('prod');

        /* Assert that the table is empty */
        $this->assertEquals(
            1,
            FormFirewallQuery::create()->count()
        );
    }
}
