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

namespace Thelia\Tests\Command;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Action\Cache;
use Thelia\Command\CacheClear;
use Thelia\Core\Application;
use Thelia\Tests\ContainerAwareTestCase;

/**
 * test the cache:clear command.
 *
 * Class CacheClearTest
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class CacheClearTest extends ContainerAwareTestCase
{
    public $cache_dir;

    protected function setUp(): void
    {
        $this->cache_dir = THELIA_CACHE_DIR.'test_cache';

        $fs = new Filesystem();

        $fs->mkdir($this->cache_dir);
        $fs->mkdir(THELIA_WEB_DIR.'/assets');
    }

    protected function tearDown(): void
    {
        $fs = new Filesystem();

        if ($fs->exists($this->cache_dir)) {
            $fs->chmod($this->cache_dir, 0700);
            $fs->remove($this->cache_dir);
        }
    }

    public function testCacheClear(): void
    {
        // Fails on windows - do not execute this test on windows
        if (strtoupper(substr(\PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Fails on windows');
        }

        $application = new Application($this->getKernel());

        $cacheClear = new CacheClear();
        $cacheClear->setContainer($this->getContainer());

        $application->add($cacheClear);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--env' => 'test',
        ]);

        $fs = new Filesystem();

        $this->assertFalse($fs->exists($this->cache_dir));
    }

    public function testCacheClearWithoutWritePermission(): void
    {
        // Fails on windows - mock this test on windows
        if (strtoupper(substr(\PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Fails on windows');
        }

        $fs = new Filesystem();
        $fs->chmod($this->cache_dir, 0100);

        $application = new Application($this->getKernel());

        $cacheClear = new CacheClear();
        $cacheClear->setContainer($this->getContainer());

        $application->add($cacheClear);

        $command = $application->find('cache:clear');
        $commandTester = new CommandTester($command);

        $this->expectException(\RuntimeException::class);
        $commandTester->execute([
            'command' => $command->getName(),
            '--env' => 'test',
        ]);
    }

    /**
     * Use this method to build the container with the services that you need.
     */
    protected function buildContainer(ContainerBuilder $container): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new Cache(new ArrayAdapter()));

        $container->set('event_dispatcher', $eventDispatcher);

        $container->setParameter('kernel.cache_dir', $this->cache_dir);
    }
}
