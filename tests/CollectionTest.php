<?php

declare(strict_types=1);

namespace Platine\Test\Collection;

use Platine\Collection\Collection;
use Platine\PlatineTestCase;

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
        $e = new Collection();
        $this->assertTrue(true);
    }
}
