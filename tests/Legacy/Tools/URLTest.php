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

namespace Thelia\Tests\Tools;

use PHPUnit\Framework\TestCase;
use Thelia\Tools\URL;

/**
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 * @author Franck Allimant <eroudeix@openstudio.fr>
 */
class URLTest extends TestCase
{
    protected $context;

    protected function setUp(): void
    {
        $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();

        $router = $this->getMockBuilder("Symfony\Component\Routing\Router")
            ->disableOriginalConstructor()
            ->getMock();

        $this->context = new \Symfony\Component\Routing\RequestContext(
            '/thelia/index.php',
            'GET',
            'localhost',
            'http',
            80,
            443,
            '/path/to/action'
        );

        $router->expects($this->any())
            ->method('getContext')
            ->willReturn($this->context);

        $container->set('router.admin', $router);

        new URL($router);
    }

    public function testGetIndexPage(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->getIndexPage();
        $this->assertEquals('http://localhost/thelia/index.php', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->getIndexPage();
        $this->assertEquals('http://localhost/thelia/', $url);

        $this->context->setBaseUrl('/thelia');
        $url = URL::getInstance()->getIndexPage();
        $this->assertEquals('http://localhost/thelia', $url);

        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->getIndexPage();
        $this->assertEquals('http://localhost/', $url);
    }

    public function testGetBaseUrl(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->getBaseUrl();
        $this->assertEquals('http://localhost/thelia/index.php', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->getBaseUrl();
        $this->assertEquals('http://localhost/thelia/', $url);

        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->getBaseUrl();
        $this->assertEquals('http://localhost/', $url);
    }

    public function testAbsoluteUrl(): void
    {
        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->absoluteUrl('/path/to/action');
        $this->assertEquals('http://localhost/path/to/action', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->absoluteUrl('/path/to/action');
        $this->assertEquals('http://localhost/thelia/path/to/action', $url);

        $this->context->setBaseUrl('/thelia');
        $url = URL::getInstance()->absoluteUrl('/path/to/action');
        $this->assertEquals('http://localhost/thelia/path/to/action', $url);

        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('/path/to/action');
        $this->assertEquals('http://localhost/thelia/index.php/path/to/action', $url);
    }

    public function testAbsoluteUrlAlternateBase(): void
    {
        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', null, URL::WITH_INDEX_PAGE, 'https://mycdn.myshop.tld/');
        $this->assertEquals('https://mycdn.myshop.tld/path/to/action', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', null, URL::WITH_INDEX_PAGE, 'https://mycdn.myshop.tld/');
        $this->assertEquals('https://mycdn.myshop.tld/path/to/action', $url);

        $this->context->setBaseUrl('/thelia');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', null, URL::WITH_INDEX_PAGE, 'https://mycdn.myshop.tld/');
        $this->assertEquals('https://mycdn.myshop.tld/path/to/action', $url);

        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', null, URL::WITH_INDEX_PAGE, 'https://mycdn.myshop.tld/');
        $this->assertEquals('https://mycdn.myshop.tld/path/to/action', $url);
    }

    public function testAbsoluteUrlOnAbsolutePath(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('http://myhost/path/to/action');
        $this->assertEquals('http://myhost/path/to/action', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->absoluteUrl('http://myhost/path/to/action');
        $this->assertEquals('http://myhost/path/to/action', $url);

        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->absoluteUrl('http://myhost/path/to/action');
        $this->assertEquals('http://myhost/path/to/action', $url);
    }

    public function testAbsoluteUrlOnAbsolutePathWithParameters(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('http://myhost/path/to/action', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://myhost/path/to/action?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->absoluteUrl('http://myhost/path/to/action', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://myhost/path/to/action?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->absoluteUrl('http://myhost/path/to/action', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://myhost/path/to/action?p1=v1&p2=v2', $url);
    }

    public function testAbsoluteUrlOnAbsolutePathWithParametersAddParameters(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('http://myhost/path/to/action?p0=v0', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://myhost/path/to/action?p0=v0&p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->absoluteUrl('http://myhost/path/to/action?p0=v0', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://myhost/path/to/action?p0=v0&p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->absoluteUrl('http://myhost/path/to/action?p0=v0', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://myhost/path/to/action?p0=v0&p1=v1&p2=v2', $url);
    }

    public function testAbsoluteUrlWithParameters(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://localhost/thelia/index.php/path/to/action?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://localhost/thelia/path/to/action?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/thelia');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://localhost/thelia/path/to/action?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/thelia');
        $url = URL::getInstance()->absoluteUrl('path/to/action', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://localhost/thelia/path/to/action?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://localhost/path/to/action?p1=v1&p2=v2', $url);
    }

    public function testAbsoluteUrlPathOnly(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', [], URL::PATH_TO_FILE);
        $this->assertEquals('http://localhost/thelia/path/to/action', $url);
    }

    public function testAbsoluteUrlPathOnlyWithParameters(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', ['p1' => 'v1', 'p2' => 'v2'], URL::PATH_TO_FILE);
        $this->assertEquals('http://localhost/thelia/path/to/action?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', ['p1' => 'v1', 'p2' => 'v2'], URL::PATH_TO_FILE);
        $this->assertEquals('http://localhost/thelia/path/to/action?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->absoluteUrl('/path/to/action', ['p1' => 'v1', 'p2' => 'v2'], URL::PATH_TO_FILE);
        $this->assertEquals('http://localhost/path/to/action?p1=v1&p2=v2', $url);
    }

    public function testAbsoluteUrlFromIndexWithParameters(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('/', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://localhost/thelia/index.php/?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->absoluteUrl('/', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://localhost/thelia/?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->absoluteUrl('/', ['p1' => 'v1', 'p2' => 'v2']);
        $this->assertEquals('http://localhost/?p1=v1&p2=v2', $url);
    }

    public function testAbsoluteUrlPathOnlyFromIndexWithParameters(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl('/', ['p1' => 'v1', 'p2' => 'v2'], URL::PATH_TO_FILE);
        $this->assertEquals('http://localhost/thelia/?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/thelia/');
        $url = URL::getInstance()->absoluteUrl('/', ['p1' => 'v1', 'p2' => 'v2'], URL::PATH_TO_FILE);
        $this->assertEquals('http://localhost/thelia/?p1=v1&p2=v2', $url);

        $this->context->setBaseUrl('/');
        $url = URL::getInstance()->absoluteUrl('/', ['p1' => 'v1', 'p2' => 'v2'], URL::PATH_TO_FILE);
        $this->assertEquals('http://localhost/?p1=v1&p2=v2', $url);
    }

    public function testAbsoluteUrlPathWithParameterReplacement(): void
    {
        $this->context->setBaseUrl('/thelia/index.php');
        $url = URL::getInstance()->absoluteUrl(
            'http://localhost/index_dev.php/admin/categories/update?category_id=1&current_tab=general&folder_id=0',
            ['category_id' => '2', 'edit_language_id' => '1', 'current_tab' => 'general'],
            URL::PATH_TO_FILE
        );
        $this->assertEquals(
            'http://localhost/index_dev.php/admin/categories/update?folder_id=0&category_id=2&edit_language_id=1&current_tab=general',
            $url
        );

        $url = URL::getInstance()->absoluteUrl(
            'http://localhost/index_dev.php/admin/categories/update?category_id=1&current_tab=general&folder_id=0',
            ['edit_language_id' => '1'],
            URL::PATH_TO_FILE
        );
        $this->assertEquals(
            'http://localhost/index_dev.php/admin/categories/update?category_id=1&current_tab=general&folder_id=0&edit_language_id=1',
            $url
        );

        $url = URL::getInstance()->absoluteUrl(
            'http://localhost/index_dev.php/admin/categories/update?category_id=1&current_tab=general&folder_id=0',
            [],
            URL::PATH_TO_FILE
        );
        $this->assertEquals(
            'http://localhost/index_dev.php/admin/categories/update?category_id=1&current_tab=general&folder_id=0',
            $url
        );

        $url = URL::getInstance()->absoluteUrl(
            'http://localhost/index_dev.php/admin/categories/update?category_id=1',
            ['category_id' => '2', 'edit_language_id' => '1', 'current_tab' => 'general'],
            URL::PATH_TO_FILE
        );
        $this->assertEquals(
            'http://localhost/index_dev.php/admin/categories/update?category_id=2&edit_language_id=1&current_tab=general',
            $url
        );
    }

    public function testRetrieve(): void
    {
    }
}
