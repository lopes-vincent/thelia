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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Action\Cache;
use Thelia\Core\Event\Cache\CacheEvent;

/**
 * Class CacheTest.
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 */
class CacheTest extends TestCase
{
    protected $dir;
    protected $dir2;

    protected function setUp(): void
    {
        $this->dir = __DIR__.'/test';
        $this->dir2 = __DIR__.'/test2';

        $fs = new Filesystem();
        $fs->mkdir($this->dir);
        $fs->mkdir($this->dir2);
    }

    public function testCacheClear(): void
    {
        $event = new CacheEvent($this->dir, false);

        $adapter = new ArrayAdapter();
        $action = new Cache($adapter);
        $action->cacheClear($event);

        $fs = new Filesystem();
        $this->assertFalse($fs->exists($this->dir));
    }

    public function testKernelTerminateCacheClear(): void
    {
        $event = new CacheEvent($this->dir2);

        $adapter = new ArrayAdapter();
        $action = new Cache($adapter);
        $action->cacheClear($event);

        $fs = new Filesystem();
        $this->assertTrue($fs->exists($this->dir2));

        $action->onTerminate();

        $this->assertFalse($fs->exists($this->dir2));
    }
}
