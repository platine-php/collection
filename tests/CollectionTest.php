<?php

declare(strict_types=1);

namespace Platine\Test\Collection;

use InvalidArgumentException;
use OutOfRangeException;
use Platine\Collection\Collection;
use Platine\Collection\Exception\InvalidOperationException;
use Platine\Collection\Generic\ArrayList;
use Platine\Collection\TypedCollection;
use Platine\Dev\PlatineTestCase;
use stdClass;

/**
 * Collection class tests
 *
 * @group core
 * @group collection
 */
class CollectionTest extends PlatineTestCase
{

    public function testConstructor()
    {
        $data = [1];
        $e = new Collection($data);
        $this->assertCount(1, $e->all());
        $this->assertContains(1, $e->all());
    }

    public function testConstructorInvalidData()
    {
        $this->expectException(InvalidArgumentException::class);
        $e = new Collection([1], 'bool');
    }

    public function testClear()
    {
        $data = [1];
        $e = new Collection($data);
        $this->assertCount(1, $e->all());
        $this->assertContains(1, $e->all());

        $e->clear();
        $this->assertEmpty($e->all());
    }

    public function testJsonSerialize()
    {
        $data = [1];
        $e = new Collection($data);

        $json = $e->jsonSerialize();

        $this->assertCount(1, $json);
        $this->assertContains(1, $json);
    }

    public function testContains()
    {
        $data = [1];
        $e = new Collection($data);
        $this->assertFalse($e->contains(10));
        $this->assertFalse($e->contains(2));
        $this->assertTrue($e->contains(1));
    }

    public function testExists()
    {
        $data = [1];
        $e = new Collection($data);
        $this->assertFalse($e->exists(10));
        $this->assertFalse($e->exists(2));
        $this->assertTrue($e->exists(0));
    }

    public function testFill()
    {
        $data = [1];
        $e = new Collection();
        $e->fill($data);
        $this->assertCount(1, $e->all());
        $this->assertContains(1, $e->all());
    }

    public function testAdd()
    {
        $e = new Collection();
        $e->add(1); //int
        $e->add(1.5); //float
        $e->add(true); //bool
        $e->add([1]); //array
        $e->add(new stdClass()); //object

        $data = $e->all();
        $this->assertCount(5, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(1.5, $data[1]);
        $this->assertEquals(true, $data[2]);
        $this->assertEquals([1], $data[3]);
        $this->assertInstanceOf(stdClass::class, $data[4]);
    }

    public function testUpdate()
    {
        $data = [1];
        $e = new Collection($data);
        $this->assertCount(1, $e->all());
        $this->assertContains(1, $e->all());
        $this->assertTrue($e->update(0, 2));
        $this->assertNotContains(1, $e->all());
        $this->assertContains(2, $e->all());
    }

    public function testUpdateOutOfRange()
    {
        $this->expectException(InvalidOperationException::class);
        $e = new Collection();
        $e->update(0, 2);
    }

    public function testGetType()
    {
        $e = new Collection();
        $this->assertEmpty($e->getType());
        $et = new Collection([], 'bool');
        $this->assertEquals('bool', $et->getType());
    }

    public function testDiffNotSameCollection()
    {
        $other = $this->getMockInstance(ArrayList::class);

        $this->expectException(InvalidOperationException::class);
        $e = new Collection();
        $e->diff($other);
    }

    public function testDiffNotSameType()
    {
        $other = $this->getMockInstance(TypedCollection::class, ['getType' => 'string']);

        $this->expectException(InvalidOperationException::class);
        $e = new Collection([], 'bool');
        $e->diff($other);
    }

    public function testDiffSuccess()
    {
        $e = new Collection([1, 2, 3]);
        $other = new Collection([4, 5, 2]);

        $diff = $e->diff($other);
        $data = $diff->all();

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(3, $data[1]);
    }

    public function testGetCollectionIsEmpty()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new Collection([]);
        $e->get(0);
    }

    public function testGetInvalidIndex()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new Collection([1]);
        $e->get(1);
    }

    public function testGetSuccess()
    {
        $e = new Collection([1, 2, 3]);

        $this->assertEquals(1, $e->get(0));
        $this->assertEquals(2, $e->get(1));
        $this->assertEquals(3, $e->get(2));
    }

    public function testFirstCollectionIsEmpty()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new Collection([]);
        $e->first();
    }

    public function testFirstSuccess()
    {
        $e = new Collection([1, 2, 3]);

        $this->assertEquals(1, $e->first());
    }

    public function testLastCollectionIsEmpty()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new Collection([]);
        $e->last();
    }

    public function testLastSuccess()
    {
        $e = new Collection([1, 2, 3]);

        $this->assertEquals(3, $e->last());
    }

    public function testEqualsNotSameCollection()
    {
        $other = $this->getMockInstance(ArrayList::class);

        $this->expectException(InvalidOperationException::class);
        $e = new Collection();
        $e->equals($other);
    }

    public function testEqualsSuccess()
    {
        $e = new Collection([1, 2, 3]);
        $other1 = new Collection([3, 2, 1]);
        $other2 = new Collection([2, 3]);
        $other3 = new Collection([1, 2, 3]);

        $this->assertFalse($e->equals($other1));
        $this->assertFalse($e->equals($other2));
        $this->assertTrue($e->equals($other3));
    }

    public function testFilterNull()
    {
        $e = new Collection([1, 2, 3]);

        $res = $e->filter(function ($k, $v) {
            return $v > 5;
        });

        $this->assertNull($res);
    }

    public function testFilterSuccess()
    {
        $e = new Collection([1, 2, 3]);

        $res = $e->filter(function ($k, $v) {
            return $v < 3;
        });
        $data = $res->all();

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(2, $data[1]);
    }

    public function testForEachSuccess()
    {
        $e = new Collection([1, 2, 3]);

        $sum = 0;
        $res = $e->forEach(function ($v, $k) use (&$sum) {
            $sum += $v;
        });

        $this->assertEquals(6, $sum);
    }

    public function testMapNull()
    {
        $e = new Collection();

        $res = $e->map(function ($v) {
            return [];
        });

        $this->assertNull($res);
    }

    public function testMapSuccess()
    {
        $e = new Collection([1, 2, 3]);

        $res = $e->map(function ($v) {
            return $v * 2;
        });
        $data = $res->all();

        $this->assertCount(3, $data);
        $this->assertEquals(2, $data[0]);
        $this->assertEquals(4, $data[1]);
        $this->assertEquals(6, $data[2]);
    }

    public function testMerge()
    {
        $e = new Collection([1, 2, 3]);
        $other = new Collection([4, 5]);

        $merge = $e->merge($other);
        $data = $merge->all();

        $this->assertCount(5, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(2, $data[1]);
        $this->assertEquals(3, $data[2]);
        $this->assertEquals(4, $data[3]);
        $this->assertEquals(5, $data[4]);
    }

    public function testRandCollectionIsEmpty()
    {
        $this->expectException(InvalidOperationException::class);
        $e = new Collection([]);
        $e->rand();
    }

    public function testRandSuccess()
    {
        $e = new Collection([1, 2, 3]);

        $val = $e->rand();

        $this->assertContains($val, $e->all());
    }

    public function testRemoveCollectionIsEmpty()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new Collection([]);
        $e->remove(0);
    }

    public function testRemoveInvalidIndex()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new Collection([1]);
        $e->remove(1);
    }

    public function testRemoveSuccess()
    {
        $e = new Collection([1, 2, 3]);

        $e->remove(1);

        $data = $e->all();

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(3, $data[1]);
    }

    public function testSliceNull()
    {
        $e = new Collection([1, 2, 3]);

        $res = $e->slice(4);

        $this->assertNull($res);
    }

    public function testSliceDefaultLength()
    {
        $e = new Collection([1, 2, 3]);

        $res = $e->slice(2);

        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertEquals(3, $data[0]);
    }

    public function testSliceCustomLength()
    {
        $e = new Collection([1, 2, 3]);

        $res = $e->slice(2, 1);

        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertEquals(3, $data[0]);
    }

    public function testSliceNegativeOffset()
    {
        $e = new Collection([1, 2, 3]);

        $res = $e->slice(-2);

        $data = $res->all();

        $this->assertCount(2, $data);
        $this->assertEquals(2, $data[0]);
        $this->assertEquals(3, $data[1]);
    }

    public function testSliceCustomNegativeLength()
    {
        $e = new Collection([1, 2, 3]);

        $res = $e->slice(1, -1);

        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertEquals(2, $data[0]);
    }

    public function testSortNull()
    {
        global $mock_usort_to_false;

        $mock_usort_to_false = true;

        $e = new Collection([1, 2, 3]);

        $res = $e->sort(function ($a, $b) {
            return $a <=> $b;
        });

        $this->assertNull($res);
    }

    public function testSortSuccess()
    {
        $e = new Collection([1, 3, 2]);

        $res = $e->sort(function ($a, $b) {
            return $a <=> $b;
        });

        $data = $res->all();

        $this->assertCount(3, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(2, $data[1]);
        $this->assertEquals(3, $data[2]);
    }

    public function testReverseCollectionIsEmpty()
    {
        $this->expectException(InvalidOperationException::class);
        $e = new Collection([]);
        $e->reverse();
    }

    public function testReverseSuccess()
    {
        $e = new Collection([1, 3, 2]);

        $res = $e->reverse();

        $data = $res->all();

        $this->assertCount(3, $data);
        $this->assertEquals(2, $data[0]);
        $this->assertEquals(3, $data[1]);
        $this->assertEquals(1, $data[2]);
    }

    public function testSumReturnNonNumericValue()
    {
        $this->expectException(InvalidOperationException::class);
        $e = new Collection([1, 2, 3]);
        $e->sum(function ($v) {
            return 'foo';
        });
    }

    public function testSumSuccess()
    {
        $e = new Collection([1, 2, 3]);

        $sum = $e->sum(function ($v) {
            return $v * 2;
        });

        $this->assertEquals(12, $sum);
    }
}
