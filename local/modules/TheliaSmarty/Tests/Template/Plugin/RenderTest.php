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

namespace TheliaSmarty\Tests\Template\Plugin;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Thelia\Core\Controller\ControllerResolver;
use TheliaSmarty\Template\Plugins\Render;

/**
 * Class RenderTest.
 *
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class RenderTest extends SmartyPluginTestCase
{
    public function testRenderWithoutParams()
    {
        $data = $this->render('test.html');

        $this->assertEquals('Hello, world!', $data);
    }

    public function testRenderWithParams()
    {
        $data = $this->render('testParams.html');

        $this->assertEquals('Hello, world!', $data);
    }

    public function testMethodParameter()
    {
        $data = $this->render('testMethod.html');

        $this->assertEquals('PUT', $data);
    }

    public function testQueryArrayParamater()
    {
        $this->smarty->assign('query', ['foo' => 'bar']);
        $data = $this->render('testQueryArray.html');

        $this->assertEquals('bar', $data);
    }

    public function testQueryStringParamater()
    {
        $data = $this->render('testQueryString.html');

        $this->assertEquals('bar', $data);
    }

    public function testRequestParamater()
    {
        $data = $this->render('testRequest.html');

        $this->assertEquals('barPOSTbazPUT', $data);
    }

    /**
     * @return \TheliaSmarty\Template\AbstractSmartyPlugin
     */
    protected function getPlugin(ContainerBuilder $container)
    {
        return new Render(
            new ControllerResolver($container),
            $container->get('request_stack'),
            $container
        );
    }
}
