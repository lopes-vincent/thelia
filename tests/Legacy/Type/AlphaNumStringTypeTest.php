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
use Thelia\Type\AlphaNumStringType;

/**
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 */
class AlphaNumStringTypeTest extends TestCase
{
    public function testAlphaNumStringType(): void
    {
        $type = new AlphaNumStringType();
        $this->assertTrue($type->isValid('azs_qs-0-9ds'));
        $this->assertTrue($type->isValid('3.3'));
        $this->assertFalse($type->isValid('3 3'));
        $this->assertFalse($type->isValid('3€3'));
    }
}
