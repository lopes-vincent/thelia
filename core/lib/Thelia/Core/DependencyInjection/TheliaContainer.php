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

namespace Thelia\Core\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * To override the methods of the symfony container.
 *
 * Class TheliaContainer
 *
 * @author Gilles Bourgeat <manu@raynaud.io>
 *
 * @since 2.3
 */
class TheliaContainer
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function get($serviceName)
    {
        return $this->container->get($serviceName);
    }
}
