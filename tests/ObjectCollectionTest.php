<?php

declare(strict_types=1);

namespace Platine\Test\Collection;

use InvalidArgumentException;
use Platine\Collection\Collection;
use Platine\Collection\ObjectCollection;
use Platine\PlatineTestCase;
use stdClass;

/**
 * ObjectCollection class tests
 *
 * @group core
 * @group collection
 */
class ObjectCollectionTest extends PlatineTestCase
{

    public function testConstructorValidValue()
    {
        $data = [];
        $data[] = new stdClass();
        $data[] = $this->getMockInstance(Collection::class);

        $e = new ObjectCollection($data);

        $all = $e->all();
        $this->assertCount(2, $all);
        $this->assertInstanceOf(stdClass::class, $all[0]);
        $this->assertInstanceOf(Collection::class, $all[1]);
    }

    public function testConstructorInvalidValue()
    {
        $this->expectException(InvalidArgumentException::class);
        $e = new ObjectCollection([new stdClass(), 4, true]);
    }
}
