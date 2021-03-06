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

namespace Thelia\Tests\Core\HttpFoundation;

use PHPUnit\Framework\TestCase;

/**
 * the the helpers addinf in Request class.
 *
 * Class RequestTest
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class RequestTest extends TestCase
{
    public function testGetUriAddingParameters(): void
    {
        $request = $this->createMock(
            "Thelia\Core\HttpFoundation\Request"
        );

        $request->expects($this->any())
            ->method('getUri')
            ->will($this->onConsecutiveCalls(
                'http://localhost/',
                'http://localhost/?test=fu'
            ));

        $request->expects($this->any())
            ->method('getQueryString')
            ->will($this->onConsecutiveCalls(
                '',
                'test=fu'
            ));

        $result = $request->getUriAddingParameters(['foo' => 'bar']);

        $this->assertEquals('http://localhost/?foo=bar', $result);

        $result = $request->getUriAddingParameters(['foo' => 'bar']);

        $this->assertEquals('http://localhost/?test=fu&foo=bar', $result);
    }
}
