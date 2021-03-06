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

namespace Thelia\Tests\Core\Template\Loop\Argument;

use PHPUnit\Framework\TestCase;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type;
use Thelia\Type\TypeCollection;

/**
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 */
class ArgumentCollectionTest extends TestCase
{
    public function testArgumentCollectionCreateAndWalk(): void
    {
        $collection = new ArgumentCollection(
            new Argument(
                'arg0',
                new TypeCollection(
                    new Type\AnyType()
                )
            ),
            new Argument(
                'arg1',
                new TypeCollection(
                    new Type\AnyType()
                )
            )
        );

        $collection->addArgument(
            new Argument(
                'arg2',
                new TypeCollection(
                    new Type\AnyType()
                )
            )
        );

        $this->assertTrue($collection->getCount() == 3);

        $this->assertTrue($collection->key() == 'arg0');
        $collection->next();
        $this->assertTrue($collection->key() == 'arg1');
        $collection->next();
        $this->assertTrue($collection->key() == 'arg2');
        $collection->next();

        $this->assertFalse($collection->valid());

        $collection->rewind();
        $this->assertTrue($collection->key() == 'arg0');
    }
}
