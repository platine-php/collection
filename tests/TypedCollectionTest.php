<?php

declare(strict_types=1);

namespace Platine\Test\Collection;

use InvalidArgumentException;
use Platine\Collection\TypedCollection;
use Platine\PlatineTestCase;

/**
 * TypedCollection class tests
 *
 * @group core
 * @group collection
 */
class TypedCollectionTest extends PlatineTestCase
{

    public function testConstructorValidValue()
    {
        $e = new TypedCollection('integer', [1, 3, 5]);
        $this->assertCount(3, $e->all());
        $this->assertContains(1, $e->all());
        $this->assertContains(3, $e->all());
        $this->assertContains(5, $e->all());
    }

    public function testConstructorInvalidValue()
    {
        $this->expectException(InvalidArgumentException::class);
        $e = new TypedCollection('integer', [1, 4, true]);
    }
}
