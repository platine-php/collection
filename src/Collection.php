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
 *  @file Collection.php
 *
 *  The Collection class
 *
 *  @package    Platine\Collection
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Collection;

use OutOfRangeException;
use Platine\Collection\Exception\InvalidOperationException;

/**
 * Class Collection
 * @package Platine\Collection
 * @template T
 * @extends BaseCollection<T>
 * @implements MergeableInterface<T>
 * @implements SortableInterface<T>
 */
class Collection extends BaseCollection implements
    IterableInterface,
    MergeableInterface,
    SortableInterface
{

    /**
     * The type of this collection elements
     * @var string
     */
    protected string $type = '';

    /**
     * Create new instance
     * @param array<mixed, mixed> $data
     * @param string $type
     */
    public function __construct(array $data = [], string $type = '')
    {
        $this->type = $type;

        foreach ($data as $value) {
            $this->validateEntry($value);
        }

        parent::__construct($data);
    }

    /**
     * Fill the collection
     * @param array<mixed, mixed> $data
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
     * @param mixed $value
     * @return void
     */
    public function add($value): void
    {
        $this->validateEntry($value);

        $data = $this->all();
        array_push($data, $value);
        $this->data->setData($data);
    }

    /**
     *
     * @param int $offset
     * @param mixed $value
     * @return bool
     * @throws InvalidOperationException
     */
    public function update(int $offset, $value): bool
    {
        $this->validateEntry($value);

        if (!$this->exists($offset)) {
            throw new InvalidOperationException(sprintf(
                'The collection index [%d] does not exists',
                $offset
            ));
        }

        $this->data[$offset] = $value;

        return $this->data[$offset] === $value;
    }

    /**
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     *
     * @param Collection<T> $collection
     * @return $this<T>
     * @throws InvalidOperationException
     */
    public function diff(BaseCollection $collection): self
    {
        if (!$collection instanceof self) {
            throw new InvalidOperationException(
                'You should only compare a collection of same type'
            );
        }

        if ($this->type !== $collection->getType()) {
            throw new InvalidOperationException(sprintf(
                'This is a collection of type [%s], you '
                    . 'cannot pass a collection of type [%s]',
                $this->type,
                $collection->getType()
            ));
        }

        $diffValues = array_udiff(
            $this->all(),
            $collection->all(),
            function ($a, $b) {
                return $a <=> $b;
            }
        );

        return new $this(array_values($diffValues));
    }

    /**
     *
     * @param int $offset
     * @return mixed
     * @throws OutOfRangeException
     */
    public function get(int $offset)
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

        return $this->data->offsetGet($offset);
    }

    /**
     * {@inheritedoc}
     */
    public function equals(BaseCollection $collection): bool
    {
        if (!$collection instanceof self) {
            throw new InvalidOperationException(
                'You should only compare a collection of the same type'
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

        foreach ($this->data as $key => $value) {
            $val = call_user_func($callback, $key, $value);
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
     * {@inheritedoc}
     */
    public function merge(BaseCollection $collection): BaseCollection
    {
        return new $this(array_merge($this->all(), $collection->all()));
    }

    /**
     * Return a random element of the collection
     * @return mixed
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
     * Remove the element at the given index
     * @param int $offset
     * @return void
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
     * @param int $offset
     * @param int|null $length
     * @return $this<T>|null
     */
    public function slice(int $offset, ?int $length = null): ?self
    {
        $newData = array_slice($this->all(), $offset, $length);

        return count($newData) > 0
                ? new $this($newData)
                : null;
    }

    /**
     * Sort the collection
     * @param callable $callback
     * @return Collection<T>|null
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
     * Validate the collection value type
     * @param mixed $value
     * @return bool
     */
    protected function validateEntry($value): bool
    {
        if (!empty($this->type)) {
            TypeCheck::isValueOf(
                $value,
                $this->type,
                sprintf(
                    'The type specified for this collection is '
                        . '[%s], you cannot pass a value of type [%s]',
                    $this->type,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }
        return true;
    }
}
