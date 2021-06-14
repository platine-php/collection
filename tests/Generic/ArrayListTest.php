<?php

declare(strict_types=1);

namespace Platine\Test\Collection\Generic;

use OutOfRangeException;
use Platine\Collection\Collection;
use Platine\Collection\Exception\InvalidOperationException;
use Platine\Collection\Generic\ArrayList;
use Platine\Dev\PlatineTestCase;
use stdClass;

/**
 * ArrayList class tests
 *
 * @group core
 * @group collection
 */
class ArrayListTest extends PlatineTestCase
{

    public function testConstructor()
    {
        $data = [1];
        $e = new ArrayList($data);
        $this->assertCount(1, $e->all());
        $this->assertContains(1, $e->all());
    }

    public function testClear()
    {
        $data = [1];
        $e = new ArrayList($data);
        $this->assertCount(1, $e->all());
        $this->assertContains(1, $e->all());

        $e->clear();
        $this->assertEmpty($e->all());
    }

    public function testJsonSerialize()
    {
        $data = [1];
        $e = new ArrayList($data);

        $json = $e->jsonSerialize();

        $this->assertCount(1, $json);
        $this->assertContains(1, $json);
    }

    public function testContains()
    {
        $data = [1];
        $e = new ArrayList($data);
        $this->assertFalse($e->contains(10));
        $this->assertFalse($e->contains(2));
        $this->assertTrue($e->contains(1));
    }

    public function testExists()
    {
        $data = [1];
        $e = new ArrayList($data);
        $this->assertFalse($e->exists(10));
        $this->assertFalse($e->exists(2));
        $this->assertTrue($e->exists(0));
    }

    public function testFill()
    {
        $data = [1];
        $e = new ArrayList();
        $e->fill($data);
        $this->assertCount(1, $e->all());
        $this->assertContains(1, $e->all());
    }

    public function testAdd()
    {
        $e = new ArrayList();
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
        $e = new ArrayList($data);
        $this->assertCount(1, $e->all());
        $this->assertContains(1, $e->all());
        $this->assertTrue($e->update(0, 2));
        $this->assertNotContains(1, $e->all());
        $this->assertContains(2, $e->all());
    }

    public function testUpdateOutOfRange()
    {
        $this->expectException(InvalidOperationException::class);
        $e = new ArrayList();
        $e->update(0, 2);
    }

    public function testDiffNotSameArrayList()
    {
        $other = $this->getMockInstance(Collection::class);

        $this->expectException(InvalidOperationException::class);
        $e = new ArrayList();
        $e->diff($other);
    }

    public function testDiffSuccess()
    {
        $e = new ArrayList([1, 2, 3]);
        $other = new ArrayList([4, 5, 2]);

        $diff = $e->diff($other);
        $data = $diff->all();

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(3, $data[1]);
    }

    public function testDiffNotSameType()
    {
        $e = new ArrayList([1, 2, 3]);
        $other = new ArrayList(['a', 5, 2]);

        $diff = $e->diff($other);
        $data = $diff->all();

        $this->assertCount(3, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(2, $data[1]);
        $this->assertEquals(3, $data[2]);
    }

    public function testGetSuccess()
    {
        $e = new ArrayList([1, 2, 3]);

        $this->assertEquals(1, $e->get(0));
        $this->assertEquals(2, $e->get(1));
        $this->assertEquals(3, $e->get(2));
    }

    public function testFirstArrayListIsEmpty()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new ArrayList([]);
        $e->first();
    }

    public function testFirstSuccess()
    {
        $e = new ArrayList([1, 2, 3]);

        $this->assertEquals(1, $e->first());
    }

    public function testLastArrayListIsEmpty()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new ArrayList([]);
        $e->last();
    }

    public function testLastSuccess()
    {
        $e = new ArrayList([1, 2, 3]);

        $this->assertEquals(3, $e->last());
    }

    public function testEqualsNotSameArrayList()
    {
        $other = $this->getMockInstance(Collection::class);

        $this->expectException(InvalidOperationException::class);
        $e = new ArrayList();
        $e->equals($other);
    }

    public function testEqualsSuccess()
    {
        $e = new ArrayList([1, 2, 3]);
        $other1 = new ArrayList([3, 2, 1]);
        $other2 = new ArrayList([2, 3]);
        $other3 = new ArrayList([1, 2, 3]);

        $this->assertFalse($e->equals($other1));
        $this->assertFalse($e->equals($other2));
        $this->assertTrue($e->equals($other3));
    }

    public function testFilterNull()
    {
        $e = new ArrayList([1, 2, 3]);

        $res = $e->filter(function ($v) {
            return $v > 5;
        });

        $this->assertNull($res);
    }

    public function testFilterSuccess()
    {
        $e = new ArrayList([1, 2, 3]);

        $res = $e->filter(function ($v) {
            return $v < 3;
        });
        $data = $res->all();

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(2, $data[1]);
    }

    public function testForEachSuccess()
    {
        $e = new ArrayList([1, 2, 3]);

        $sum = 0;
        $res = $e->forEach(function ($v, $k) use (&$sum) {
            $sum += $v;
        });

        $this->assertEquals(6, $sum);
    }

    public function testMapNull()
    {
        $e = new ArrayList();

        $res = $e->map(function ($v) {
            return [];
        });

        $this->assertNull($res);
    }

    public function testMapSuccess()
    {
        $e = new ArrayList([1, 2, 3]);

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
        $e = new ArrayList([1, 2, 3]);
        $other = new ArrayList([4, 5]);

        $merge = $e->merge($other);
        $data = $merge->all();

        $this->assertCount(5, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(2, $data[1]);
        $this->assertEquals(3, $data[2]);
        $this->assertEquals(4, $data[3]);
        $this->assertEquals(5, $data[4]);
    }

    public function testRandArrayListIsEmpty()
    {
        $this->expectException(InvalidOperationException::class);
        $e = new ArrayList([]);
        $e->rand();
    }

    public function testRandSuccess()
    {
        $e = new ArrayList([1, 2, 3]);

        $val = $e->rand();

        $this->assertContains($val, $e->all());
    }

    public function testRemoveArrayListIsEmpty()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new ArrayList([]);
        $e->remove(0);
    }

    public function testRemoveInvalidIndex()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new ArrayList([1]);
        $e->remove(1);
    }

    public function testRemoveSuccess()
    {
        $e = new ArrayList([1, 2, 3]);

        $e->remove(1);

        $data = $e->all();

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(3, $data[1]);
    }

    public function testSliceNull()
    {
        $e = new ArrayList([1, 2, 3]);

        $res = $e->slice(4);

        $this->assertNull($res);
    }

    public function testSliceDefaultLength()
    {
        $e = new ArrayList([1, 2, 3]);

        $res = $e->slice(2);

        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertEquals(3, $data[0]);
    }

    public function testSliceCustomLength()
    {
        $e = new ArrayList([1, 2, 3]);

        $res = $e->slice(2, 1);

        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertEquals(3, $data[0]);
    }

    public function testSliceNegativeOffset()
    {
        $e = new ArrayList([1, 2, 3]);

        $res = $e->slice(-2);

        $data = $res->all();

        $this->assertCount(2, $data);
        $this->assertEquals(2, $data[0]);
        $this->assertEquals(3, $data[1]);
    }

    public function testSliceCustomNegativeLength()
    {
        $e = new ArrayList([1, 2, 3]);

        $res = $e->slice(1, -1);

        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertEquals(2, $data[0]);
    }

    public function testSortNull()
    {
        global $mock_usort_to_false;

        $mock_usort_to_false = true;

        $e = new ArrayList([1, 2, 3]);

        $res = $e->sort(function ($a, $b) {
            return $a <=> $b;
        });

        $this->assertNull($res);
    }

    public function testSortSuccess()
    {
        $e = new ArrayList([1, 3, 2]);

        $res = $e->sort(function ($a, $b) {
            return $a <=> $b;
        });

        $data = $res->all();

        $this->assertCount(3, $data);
        $this->assertEquals(1, $data[0]);
        $this->assertEquals(2, $data[1]);
        $this->assertEquals(3, $data[2]);
    }

    public function testReverseArrayListIsEmpty()
    {
        $this->expectException(InvalidOperationException::class);
        $e = new ArrayList([]);
        $e->reverse();
    }

    public function testReverseSuccess()
    {
        $e = new ArrayList([1, 3, 2]);

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
        $e = new ArrayList([1, 2, 3]);
        $e->sum(function ($v) {
            return 'foo';
        });
    }

    public function testSumSuccess()
    {
        $e = new ArrayList([1, 2, 3]);

        $sum = $e->sum(function ($v) {
            return $v * 2;
        });

        $this->assertEquals(12, $sum);
    }
}
