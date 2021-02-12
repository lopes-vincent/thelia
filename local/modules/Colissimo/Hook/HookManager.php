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

namespace Colissimo\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class HookManager.
 *
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class HookManager extends BaseHook
{
    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $module_id = self::getModule()->getModuleId();

        $event->add($this->render('module_configuration.html', ['module_id' => $module_id]));
    }
}
