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

namespace Thelia\Tests\Action;

use Thelia\Action\Newsletter;
use Thelia\Core\Event\Newsletter\NewsletterEvent;
use Thelia\Model\Newsletter as NewsletterModel;
use Thelia\Model\NewsletterQuery;

/**
 * Class NewsletterTest.
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class NewsletterTest extends BaseAction
{
    protected $mailerFactory;
    protected $dispatcher;

    protected function setUp(): void
    {
        $this->mailerFactory = $this->getMockBuilder('Thelia\\Mailer\\MailerFactory')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->dispatcher = $this->getMockEventDispatcher();
    }

    public static function setUpBeforeClass(): void
    {
        NewsletterQuery::create()
            ->filterByEmail('test@foo.com')
            ->delete();
    }

    public function testSubscribe()
    {
        $event = new NewsletterEvent('test@foo.com', 'en_US');
        $event
            ->setFirstname('foo')
            ->setLastname('bar')
        ;

        $action = new Newsletter($this->mailerFactory, $this->dispatcher);
        $action->subscribe($event);

        $subscribedNewsletter = $event->getNewsletter();

        $this->assertInstanceOf('Thelia\Model\Newsletter', $subscribedNewsletter);
        $this->assertFalse($subscribedNewsletter->isNew());

        $this->assertEquals('test@foo.com', $subscribedNewsletter->getEmail());
        $this->assertEquals('en_US', $subscribedNewsletter->getLocale());
        $this->assertEquals('foo', $subscribedNewsletter->getFirstname());
        $this->assertEquals('bar', $subscribedNewsletter->getLastname());

        return $subscribedNewsletter;
    }

    /**
     * @depends testSubscribe
     *
     * @return NewsletterModel
     */
    public function testUpdate(NewsletterModel $newsletter)
    {
        $event = new NewsletterEvent('test@foo.com', 'en_US');
        $event
            ->setId($newsletter->getId())
            ->setFirstname('foo update')
            ->setLastname('bar update')
        ;

        $action = new Newsletter($this->mailerFactory, $this->dispatcher);
        $action->update($event);

        $updatedNewsletter = $event->getNewsletter();

        $this->assertInstanceOf('Thelia\Model\Newsletter', $updatedNewsletter);

        $this->assertEquals('test@foo.com', $updatedNewsletter->getEmail());
        $this->assertEquals('en_US', $updatedNewsletter->getLocale());
        $this->assertEquals('foo update', $updatedNewsletter->getFirstname());
        $this->assertEquals('bar update', $updatedNewsletter->getLastname());

        return $updatedNewsletter;
    }

    /**
     * @depends testUpdate
     */
    public function testUnsubscribe(NewsletterModel $newsletter): void
    {
        $event = new NewsletterEvent('test@foo.com', 'en_US');
        $event->setId($newsletter->getId());

        $action = new Newsletter($this->mailerFactory, $this->dispatcher);
        $action->unsubscribe($event);

        $deletedNewsletter = $event->getNewsletter();

        $this->assertInstanceOf('Thelia\Model\Newsletter', $deletedNewsletter);
        $this->assertEquals(1, NewsletterQuery::create()->filterByEmail('test@foo.com')->filterByUnsubscribed(true)->count());
    }
}
