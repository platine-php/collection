<?php

/**
 * Platine Collection
 *
 * Platine Collection provides a flexible and simple PHP collection implementation.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2020 Platine Collection
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 *  @file ArrayList.php
 *
 *  The Array List class is like a PHP native array
 *
 *  @package    Platine\Collection\Generic
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Collection\Generic;

use OutOfRangeException;
use Platine\Collection\BaseCollection;
use Platine\Collection\CollectionInterface;
use Platine\Collection\Exception\InvalidOperationException;
use Platine\Collection\IterableInterface;
use Platine\Collection\MergeableInterface;
use Platine\Collection\SortableInterface;

/**
 * Class ArrayList
 * @package Platine\Collection\Generic
 * @template T
 * @extends BaseCollection<T>
 * @implements MergeableInterface<T>
 * @implements SortableInterface<T>
 */
class ArrayList extends BaseCollection implements
    CollectionInterface,
    IterableInterface,
    MergeableInterface,
    SortableInterface
{
    /**
     *
     * @param T $value
     * @return void
     */
    public function add($value): void
    {
        $data = $this->all();

        array_push($data, $value);
        $this->data->setData($data);
    }

    /**
     * Fill the collection
     * @param array<int, T> $data
     * @return void
     */
    public function fill(array $data): void
    {
        foreach ($data as $value) {
            $this->add($value);
        }
    }

    /**
     *
     * @param ArrayList<T> $collection
     * @return ArrayList<T>
     * @throws InvalidOperationException
     */
    public function diff(BaseCollection $collection): BaseCollection
    {
        if (!$collection instanceof self) {
            throw new InvalidOperationException(
                'You should only compare an ArrayList against another ArrayList'
            );
        }

        $diffValues = array_udiff(
            $this->all(),
            $collection->all(),
            function ($a, $b) {
                if (gettype($a) !== gettype($b)) {
                    return -1;
                }

                return $a <=> $b;
            }
        );

        return new $this(array_values($diffValues));
    }

    /**
     *
     * @param ArrayList<T> $collection
     * @return bool
     * @throws InvalidOperationException
     */
    public function equals(BaseCollection $collection): bool
    {
        if (!$collection instanceof self) {
            throw new InvalidOperationException(
                'You should only compare an ArrayList against another ArrayList'
            );
        }

        return $this->all() == $collection->all();
    }

    /**
     *
     * @param callable $callback
     * @return $this|null
     */
    public function filter(callable $callback): ?self
    {
        $matches = [];

        foreach ($this->data as $value) {
            $val = call_user_func($callback, $value);
            if ($val === true) {
                $matches[] = $value;
            }
        }

        return count($matches) > 0
                ? new $this(array_values($matches))
                : null;
    }

    /**
     * {@inheritedoc}
     */
    public function forEach(callable $callback): void
    {
        $data = $this->all();
        array_walk($data, $callback);

        $this->data->setData($data);
    }

    /**
     *
     * @param int $offset
     * @return T|null
     */
    public function get(int $offset)
    {
        return $this->data->offsetGet($offset);
    }

    /**
     *
     * @param callable $callback
     * @return $this|null
     */
    public function map(callable $callback): ?self
    {
        $matches = array_map($callback, $this->all());

        return count($matches) > 0
                ? new $this(array_values($matches))
                : null;
    }

    /**
     *
     * @param ArrayList<T> $collection
     * @return ArrayList<T>
     */
    public function merge(BaseCollection $collection): BaseCollection
    {
        return new $this(array_merge($this->all(), $collection->all()));
    }

    /**
     * Return a random element of the collection
     * @return T|null
     * @throws InvalidOperationException
     */
    public function rand()
    {
        if ($this->isEmpty()) {
            throw new InvalidOperationException('The collection is empty');
        }

        /** @var int $offset */
        $offset = array_rand($this->all());

        return $this->get($offset);
    }

    /**
     * {@inheritedoc}
     */
    public function remove(int $offset): void
    {
        if ($this->isEmpty()) {
            throw new OutOfRangeException('The collection is empty');
        }

        if (!$this->data->offsetExists($offset)) {
            throw new OutOfRangeException(sprintf(
                'The collection index [%d] does not exists',
                $offset
            ));
        }

        $this->data->offsetUnset($offset);
        $this->repopulate();
    }

    /**
     *
     * @return $this
     */
    public function reverse(): self
    {
        if ($this->isEmpty()) {
            throw new InvalidOperationException('The collection is empty');
        }

        return new $this(array_reverse($this->all()));
    }

    /**
     *
     * @param int $offset
     * @param int|null $length
     * @return ArrayList<T>|null
     */
    public function slice(int $offset, ?int $length = null): ?BaseCollection
    {
        $newData = array_slice($this->all(), $offset, $length);

        return count($newData) > 0
                ? new $this($newData)
                : null;
    }

    /**
     *
     * @param callable $callback
     * @return ArrayList<T>|null
     */
    public function sort(callable $callback): ?BaseCollection
    {
        $data = $this->all();

        return usort($data, $callback)
                ? new $this($data)
                : null;
    }

    /**
     *
     * @param int $offset
     * @param T $value
     * @return bool
     * @throws InvalidOperationException
     */
    public function update(int $offset, $value): bool
    {
        if (!$this->exists($offset)) {
            throw new InvalidOperationException(sprintf(
                'The collection index [%d] does not exists',
                $offset
            ));
        }

        $this->data->offsetSet($offset, $value);

        return $this->data->offsetGet($offset) === $value;
    }
}
