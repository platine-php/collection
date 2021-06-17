<?php

declare(strict_types=1);

namespace Platine\Test\Collection\Stack;

use InvalidArgumentException;
use Platine\Collection\Exception\InvalidOperationException;
use Platine\Collection\Stack\Stack;
use Platine\Dev\PlatineTestCase;

/**
 * Stack class tests
 *
 * @group core
 * @group collection
 */
class StackTest extends PlatineTestCase
{

    public function testConstructor()
    {
        $e = new Stack('string');

        $this->assertEquals('string', $this->getPropertyValue(Stack::class, $e, 'type'));
    }

    public function testClearAndEmpty()
    {
        $e = new Stack('string');
        $e->push('tnh');
        $this->assertEquals(1, $e->count());
        $this->assertFalse($e->isEmpty());

        $e->clear();
        $this->assertEquals(0, $e->count());
        $this->assertTrue($e->isEmpty());
    }

    public function testPeekCollectionIsEmpty()
    {
        $this->expectException(InvalidOperationException::class);

        $e = new Stack('string');

        $e->peek();
    }

    public function testPeekSuccess()
    {
        $e = new Stack('string');
        $e->push('one');
        $e->push('two');

        $this->assertEquals(2, $e->count());

        $res = $e->peek();

        $this->assertEquals(2, $e->count());
        $this->assertEquals('two', $res);
    }

    public function testPopNull()
    {
        $e = new Stack('string');
        $e->push('one');
        $this->assertEquals(1, $e->count());

        $res = $e->pop();

        $this->assertEquals(0, $e->count());
        $this->assertEquals('one', $res);
        $this->assertNull($e->pop());
    }

    public function testPopSuccess()
    {
        $e = new Stack('string');
        $e->push('one');
        $e->push('two');

        $this->assertEquals(2, $e->count());

        $res = $e->pop();

        $this->assertEquals(1, $e->count());
        $this->assertEquals('two', $res);
    }

    public function testPushInvalidData()
    {
        $e = new Stack('string');
        $e->push('one');
        $this->assertEquals(1, $e->count());

        $this->expectException(InvalidArgumentException::class);
        $e->push(1);
    }
}
