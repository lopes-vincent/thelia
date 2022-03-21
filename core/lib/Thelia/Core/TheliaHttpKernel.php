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

namespace Thelia\Core;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Thelia\Core\DependencyInjection\TheliaContainer;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Tools\URL;

/**
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class TheliaHttpKernel extends HttpKernel
{
    protected static $session;

    protected $container;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TheliaContainer $container,
        ControllerResolverInterface $controllerResolver,
        RequestStack $requestStack,
        ArgumentResolverInterface $argumentResolver,
        URL $urlManager
    ) {
        parent::__construct($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
        $this->container = $container;
    }
}
