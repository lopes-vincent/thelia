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

namespace Thelia\Model;

use Thelia\Model\Base\ModuleHook as BaseModuleHook;

class ModuleHook extends BaseModuleHook
{
    use \Thelia\Model\Tools\PositionManagementTrait;

    const MAX_POSITION = 1000;

    /**
     * Calculate next position relative to our default category
     */
    protected function addCriteriaToPositionQuery($query)
    {
        $query->filterByHookId($this->getHookId());
    }
}
