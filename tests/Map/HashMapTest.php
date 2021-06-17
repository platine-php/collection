<?php

declare(strict_types=1);

namespace Platine\Test\Collection\Map;

use InvalidArgumentException;
use OutOfRangeException;
use Platine\Collection\Exception\InvalidOperationException;
use Platine\Collection\Generic\ArrayList;
use Platine\Collection\Map\HashMap;
use Platine\Dev\PlatineTestCase;

/**
 * HashMap class tests
 *
 * @group core
 * @group collection
 */
class HashMapTest extends PlatineTestCase
{

    public function testConstructor()
    {
        $data = ['a' => 'b'];
        $e = new HashMap('string', 'string', $data);

        $map = $e->all();

        $this->assertCount(1, $map);
        $this->assertArrayHasKey('a', $map);
        $this->assertEquals('b', $map['a']);
    }

    public function testConstructorInvalidData()
    {
        $this->expectException(InvalidArgumentException::class);
        $e = new HashMap('string', 'string', [1]);
    }

    public function testClear()
    {
        $data = ['a' => 'b'];
        $e = new HashMap('string', 'string', $data);

        $map = $e->all();

        $this->assertCount(1, $map);
        $this->assertArrayHasKey('a', $map);
        $this->assertEquals('b', $map['a']);

        $e->clear();
        $this->assertEmpty($e->all());
    }

    public function testJsonSerialize()
    {
        $data = ['a' => 'b'];
        $e = new HashMap('string', 'string', $data);

        $json = $e->jsonSerialize();

        $this->assertCount(1, $json);
        $this->assertArrayHasKey('a', $json);
        $this->assertEquals('b', $json['a']);
    }

    public function testContains()
    {
        $data = ['a' => 'b'];
        $e = new HashMap('string', 'string', $data);
        $this->assertFalse($e->contains('a'));
        $this->assertFalse($e->contains('c'));
        $this->assertTrue($e->contains('b'));
    }

    public function testExists()
    {
        $data = ['a' => 'b'];
        $e = new HashMap('string', 'string', $data);
        $this->assertFalse($e->exists('b'));
        $this->assertFalse($e->exists('c'));
        $this->assertTrue($e->exists('a'));
    }

    public function testFill()
    {
        $data = ['a' => 'b'];
        $e = new HashMap('string', 'string');
        $e->fill($data);

        $map = $e->all();

        $this->assertCount(1, $map);
        $this->assertArrayHasKey('a', $map);
        $this->assertEquals('b', $map['a']);
    }

    public function testAdd()
    {
        $e = new HashMap('string', 'string');
        $e->add('one', '1');
        $e->add('two', '2');

        $map = $e->all();
        $this->assertCount(2, $map);

        $this->assertArrayHasKey('one', $map);
        $this->assertArrayHasKey('two', $map);
        $this->assertEquals('1', $map['one']);
        $this->assertEquals('2', $map['two']);
    }

    public function testUpdate()
    {
        $data = ['a' => 'b'];
        $e = new HashMap('string', 'string', $data);

        $map = $e->all();

        $this->assertCount(1, $data);

        $this->assertArrayHasKey('a', $map);
        $this->assertEquals('b', $map['a']);

        $this->assertTrue($e->update('a', 'c'));

        $mapBis = $e->all();
        $this->assertCount(1, $mapBis);
        $this->assertArrayHasKey('a', $mapBis);
        $this->assertEquals('c', $mapBis['a']);
    }

    public function testUpdateOutOfRange()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new HashMap('string', 'string');
        $e->update('foo', 'bar');
    }

    public function testGetKeyAndValueType()
    {
        $e = new HashMap('string', 'string');
        $this->assertEquals('string', $e->getKeyType());
        $this->assertEquals('string', $e->getValueType());
    }

    public function testDiffNotSameHashMap()
    {
        $other = $this->getMockInstance(ArrayList::class);

        $this->expectException(InvalidOperationException::class);
        $e = new HashMap('string', 'string');
        $e->diff($other);
    }

    public function testDiffNotSameKeyType()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);
        $other = new HashMap('integer', 'string', [0 => 'f', 1 => 'g']);

        $this->expectException(InvalidOperationException::class);
        $e->diff($other);
    }

    public function testDiffNotSameValueType()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);
        $other = new HashMap('string', 'integer', ['f' => 5, 'z' => 34]);

        $this->expectException(InvalidOperationException::class);
        $e->diff($other);
    }

    public function testDiffSuccess()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);
        $other = new HashMap('string', 'string', ['e' => 'f', 'b' => 'g']);

        $diff = $e->diff($other);
        $data = $diff->all();

        $this->assertCount(2, $data);
        $this->assertArrayHasKey('a', $data);
        $this->assertArrayHasKey('c', $data);
        $this->assertEquals('b', $data['a']);
        $this->assertEquals('d', $data['c']);
    }

    public function testGetNull()
    {
        $e = new HashMap('string', 'string', []);
        $this->assertNull($e->get('foo'));
    }

    public function testGetSuccess()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $this->assertEquals('b', $e->get('a'));
        $this->assertEquals('d', $e->get('c'));
    }

    public function testFirst()
    {
        $this->expectException(InvalidOperationException::class);
        $e = new HashMap('string', 'string', []);

        $e->first();
    }

    public function testLast()
    {
        $this->expectException(InvalidOperationException::class);
        $e = new HashMap('string', 'string', []);

        $e->last();
    }

    public function testEqualsNotSameHashMap()
    {
        $other = $this->getMockInstance(ArrayList::class);

        $this->expectException(InvalidOperationException::class);
        $e = new HashMap('string', 'string');
        $e->equals($other);
    }

    public function testEqualsSuccess()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);
        $other1 = new HashMap('string', 'string', ['a' => 'b']);
        $other2 = new HashMap('string', 'string', ['a' => 'c', 'd' => 'e']);
        $other3 = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $this->assertFalse($e->equals($other1));
        $this->assertFalse($e->equals($other2));
        $this->assertTrue($e->equals($other3));
    }

    public function testFilterNull()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $res = $e->filter(function ($k, $v) {
            return $k === 'abc';
        });

        $this->assertNull($res);
    }

    public function testFilterSuccess()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $res = $e->filter(function ($k, $v) {
            return $k === 'a';
        });
        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('a', $data);
        $this->assertEquals('b', $data['a']);
    }

    public function testForEachSuccess()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $concat = '';
        $res = $e->forEach(function ($k, $v) use (&$concat) {
            $concat .= $k . ':' . $v;
        });

        $this->assertEquals('b:ad:c', $concat);
    }

    public function testMapNull()
    {
        $e = new HashMap('string', 'string');

        $res = $e->map(function ($v) {
            return [];
        });

        $this->assertNull($res);
    }

    public function testMapSuccess()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $res = $e->map(function ($v) {
            return $v . ':';
        });
        $data = $res->all();

        $this->assertCount(2, $data);
        $this->assertArrayHasKey('a', $data);
        $this->assertArrayHasKey('c', $data);
        $this->assertEquals('b', $data['a']);
        $this->assertEquals('d', $data['c']);
    }

    public function testMerge()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);
        $other = new HashMap('string', 'string', ['e' => 'f']);

        $merge = $e->merge($other);
        $data = $merge->all();

        $this->assertCount(3, $data);

        $this->assertArrayHasKey('a', $data);
        $this->assertArrayHasKey('c', $data);
        $this->assertArrayHasKey('e', $data);
        $this->assertEquals('b', $data['a']);
        $this->assertEquals('d', $data['c']);
        $this->assertEquals('f', $data['e']);
    }

    public function testMergeNotSameKeyType()
    {
        $this->expectException(InvalidArgumentException::class);
        $e = new HashMap('integer', 'string', [1 => 'b', 2 => 'd']);
        $other = new HashMap('string', 'string', ['e' => 'f']);

        $e->merge($other);
    }

    public function testMergeNotSameValueType()
    {
        $this->expectException(InvalidArgumentException::class);
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);
        $other = new HashMap('string', 'integer', ['e' => 3]);

        $e->merge($other);
    }

    public function testRemoveHashMapIsEmpty()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new HashMap('string', 'string', []);
        $e->remove(0);
    }

    public function testRemoveInvalidIndex()
    {
        $this->expectException(OutOfRangeException::class);
        $e = new HashMap('string', 'string', ['a' => 'b']);
        $e->remove('b');
    }

    public function testRemoveSuccess()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $e->remove('a');

        $data = $e->all();

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('c', $data);
        $this->assertEquals('d', $data['c']);
    }

    public function testSliceNull()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $res = $e->slice(4);

        $this->assertNull($res);
    }

    public function testSliceDefaultLength()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $res = $e->slice(1);

        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('c', $data);
        $this->assertEquals('d', $data['c']);
    }

    public function testSliceCustomLength()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $res = $e->slice(1, 1);

        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('c', $data);
        $this->assertEquals('d', $data['c']);
    }

    public function testSliceNegativeOffset()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $res = $e->slice(-2);

        $data = $res->all();

        $this->assertCount(2, $data);
        $this->assertArrayHasKey('a', $data);
        $this->assertArrayHasKey('c', $data);
        $this->assertEquals('b', $data['a']);
        $this->assertEquals('d', $data['c']);
    }

    public function testSliceCustomNegativeLength()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $res = $e->slice(0, -1);

        $data = $res->all();

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('a', $data);
        $this->assertEquals('b', $data['a']);
    }

    public function testSortNull()
    {
        global $mock_uasort_to_false;

        $mock_uasort_to_false = true;

        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $res = $e->sort(function ($a, $b) {
            return $a <=> $b;
        });

        $this->assertNull($res);
    }

    public function testSortSuccess()
    {
        $e = new HashMap('string', 'string', ['c' => 'd', 'a' => 'b']);

        $res = $e->sort(function ($a, $b) {
            return $a <=> $b;
        });

        $data = $res->all();

        $this->assertCount(2, $data);
        $this->assertArrayHasKey('a', $data);
        $this->assertArrayHasKey('c', $data);
        $this->assertEquals('b', $data['a']);
        $this->assertEquals('d', $data['c']);
    }

    public function testToJsonEmpty()
    {
        global $mock_json_encode_to_false;

        $mock_json_encode_to_false = true;

        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $this->assertEmpty($e->toJson());
    }

    public function testToJsonSuccess()
    {
        $e = new HashMap('string', 'string', ['a' => 'b', 'c' => 'd']);

        $this->assertEquals('{"a":"b","c":"d"}', $e->toJson());
    }
}
