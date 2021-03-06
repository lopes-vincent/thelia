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

namespace Thelia\Tests\Type;

use PHPUnit\Framework\TestCase;
use Thelia\Type\JsonType;

/**
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 */
class JsonTypeTest extends TestCase
{
    public function testJsonType(): void
    {
        $jsonType = new JsonType();
        $this->assertTrue($jsonType->isValid('{"k0":"v0","k1":"v1","k2":"v2"}'));
        $this->assertFalse($jsonType->isValid('1,2,3'));
    }

    public function testFormatJsonType(): void
    {
        $jsonType = new JsonType();
        $this->assertIsArray($jsonType->getFormattedValue('{"k0":"v0","k1":"v1","k2":"v2"}'));
        $this->assertNull($jsonType->getFormattedValue('foo'));
    }
}
