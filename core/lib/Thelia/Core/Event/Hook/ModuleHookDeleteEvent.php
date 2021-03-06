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

namespace Thelia\Core\Event\Hook;

/**
 * Class ModuleHookDeleteEvent.
 *
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class ModuleHookDeleteEvent extends ModuleHookEvent
{
    /** @var int */
    protected $module_hook_id;

    /**
     * @param int $module_hook_id
     */
    public function __construct($module_hook_id)
    {
        $this->module_hook_id = $module_hook_id;
    }

    /**
     * @return $this
     */
    public function setModuleHookId($module_hook_id)
    {
        $this->module_hook_id = $module_hook_id;

        return $this;
    }

    public function getModuleHookId()
    {
        return $this->module_hook_id;
    }
}
