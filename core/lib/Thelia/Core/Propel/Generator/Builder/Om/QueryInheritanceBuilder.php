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

namespace Thelia\Core\Propel\Generator\Builder\Om;

use Propel\Generator\Builder\Om\QueryInheritanceBuilder as PropelQueryInheritanceBuilder;
use Thelia\Core\Propel\Generator\Builder\Om\Mixin\ImplementationClassTrait;

class QueryInheritanceBuilder extends PropelQueryInheritanceBuilder
{
    use ImplementationClassTrait;
}
