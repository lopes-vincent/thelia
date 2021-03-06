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

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Thelia\Action\Module;
use Thelia\Command\ModuleActivateCommand;
use Thelia\Core\Application;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;
use Thelia\Tests\ContainerAwareTestCase;

/**
 * Class ModuleActivateCommandTest.
 *
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 */
class ModuleActivateCommandTest extends ContainerAwareTestCase
{
    public function testModuleActivateCommand(): void
    {
        $module = ModuleQuery::create()->findOne();

        if (null !== $module) {
            $prev_activation_status = $module->getActivate();

            $application = new Application($this->getKernel());

            $module->setActivate(BaseModule::IS_NOT_ACTIVATED);
            $module->save();

            $request = new Request();
            $requestStack = new RequestStack();
            $requestStack->push($request);
            $request->setSession(new Session(new MockArraySessionStorage()));

            $container = $this->getContainer();
            $tanslator = new Translator($requestStack);
            $container->set('thelia.translator', $tanslator);

            $moduleActivate = new ModuleActivateCommand();
            $moduleActivate->setContainer($container);

            $application->add($moduleActivate);

            $command = $application->find('module:activate');
            $commandTester = new CommandTester($command);
            $commandTester->execute([
                'command' => $command->getName(),
                'module' => $module->getCode(),
            ]);

            $activated = ModuleQuery::create()->findPk($module->getId())->getActivate();

            // Restore activation status
            $module->setActivate($prev_activation_status)->save();

            $this->assertEquals(BaseModule::IS_ACTIVATED, $activated);
        }
    }

    public function testModuleActivateCommandUnknownModule(): void
    {
        $testedModule = ModuleQuery::create()->findOneByCode('Letshopethismoduledoesnotexists');

        if (null == $testedModule) {
            $application = new Application($this->getKernel());

            $moduleActivate = new ModuleActivateCommand();
            $moduleActivate->setContainer($this->getContainer());

            $application->add($moduleActivate);

            $command = $application->find('module:activate');
            $commandTester = new CommandTester($command);

            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage('module Letshopethismoduledoesnotexists not found');
            $commandTester->execute([
                'command' => $command->getName(),
                'module' => 'letshopethismoduledoesnotexists',
            ]);
        }
    }

    /**
     * Use this method to build the container with the services that you need.
     */
    protected function buildContainer(ContainerBuilder $container): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new Module($container, $eventDispatcher));

        $container->set('event_dispatcher', $eventDispatcher);

        $container->setParameter('kernel.cache_dir', THELIA_CACHE_DIR.'dev');
    }
}
