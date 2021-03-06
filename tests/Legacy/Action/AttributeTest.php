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

use Thelia\Action\Attribute;
use Thelia\Core\Event\Attribute\AttributeCreateEvent;
use Thelia\Core\Event\Attribute\AttributeDeleteEvent;
use Thelia\Core\Event\Attribute\AttributeUpdateEvent;
use Thelia\Model\Attribute as AttributeModel;

/**
 * Class AttributeTest.
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class AttributeTest extends BaseAction
{
    public function testCreateSimple()
    {
        $event = new AttributeCreateEvent();

        $event
            ->setLocale('en_US')
            ->setTitle('foo');

        $action = new Attribute($this->getMockEventDispatcher());
        $action->create($event, null, $this->getMockEventDispatcher());

        $createdAttribute = $event->getAttribute();

        $this->assertInstanceOf('Thelia\Model\Attribute', $createdAttribute);
        $this->assertEquals($createdAttribute->getLocale(), 'en_US');
        $this->assertEquals($createdAttribute->getTitle(), 'foo');

        return $createdAttribute;
    }

    /**
     * @depends testCreateSimple
     *
     * @return AttributeModel
     */
    public function testUpdate(AttributeModel $attribute)
    {
        $event = new AttributeUpdateEvent($attribute->getId());

        $event
            ->setLocale($attribute->getLocale())
            ->setTitle('bar')
            ->setDescription('bar description')
            ->setChapo('bar chapo')
            ->setPostscriptum('bar postscriptum');

        $action = new Attribute();
        $action->update($event, null, $this->getMockEventDispatcher());

        $updatedAttribute = $event->getAttribute();

        $this->assertInstanceOf('Thelia\Model\Attribute', $updatedAttribute);
        $this->assertEquals('en_US', $updatedAttribute->getLocale());
        $this->assertEquals('bar', $updatedAttribute->getTitle());
        $this->assertEquals('bar description', $updatedAttribute->getDescription());
        $this->assertEquals('bar chapo', $updatedAttribute->getChapo());
        $this->assertEquals('bar postscriptum', $updatedAttribute->getPostscriptum());

        return $updatedAttribute;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(AttributeModel $attribute): void
    {
        $event = new AttributeDeleteEvent($attribute->getId());

        $action = new Attribute();
        $action->delete($event, null, $this->getMockEventDispatcher());

        $deletedAttribute = $event->getAttribute();

        $this->assertInstanceOf('Thelia\Model\Attribute', $deletedAttribute);
        $this->assertTrue($deletedAttribute->isDeleted());
    }
}
