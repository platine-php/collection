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
 *  @file BaseCollection.php
 *
 *  The BaseCollection class
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

use Countable;
use JsonSerializable;
use OutOfRangeException;
use Platine\Collection\Exception\InvalidOperationException;
use ArrayIterator;

/**
 * Class BaseCollection
 * @package Platine\Collection
 * @template T
 */
abstract class BaseCollection implements Countable, JsonSerializable
{
    /**
     * The data container instance
     * @var DataContainer<T>
     */
    protected DataContainer $data;

    /**
     * Create new instance
     * @param array<mixed, T> $initials
     */
    public function __construct(array $initials = [])
    {
        $this->data = new DataContainer($initials);
    }

    /**
     * Clear the collection data
     * @return void
     */
    public function clear(): void
    {
        $this->data = new DataContainer([]);
    }

    /**
     * Check whether the collection has the given element
     * @param T $needle
     * @return bool
     */
    public function contains($needle): bool
    {
        return in_array($needle, $this->all());
    }

    /**
     *
     * @param mixed $offset
     * @return bool
     */
    public function exists($offset): bool
    {
        return $this->data->offsetExists($offset);
    }

    /**
     * Return the first element of collection
     * @return T
     */
    public function first()
    {
        if ($this->isEmpty()) {
            throw new OutOfRangeException('The collection is empty');
        }
        $values = $this->all();

        return $values[0];
    }

    /**
     * Return the last element of collection
     * @return T
     */
    public function last()
    {
        if ($this->isEmpty()) {
            throw new OutOfRangeException('The collection is empty');
        }
        $values = $this->all();

        return $values[$this->count() - 1];
    }

    /**
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Return the sum of the collection element
     * @param callable $callback
     * @return float
     */
    public function sum(callable $callback): float
    {
        $sum = 0;
        foreach ($this->data as $value) {
            $val = call_user_func($callback, $value);
            if (!is_numeric($val)) {
                throw new InvalidOperationException(
                    'You cannot sum non-numeric values'
                );
            }

            $sum += $val;
        }

        return $sum;
    }

    /**
     * Return the array representation of the collection
     * @return array<mixed, T>
     */
    public function all(): array
    {
        return $this->data->getData();
    }

    /**
     * {@inheritedoc}
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * {@inheritedoc}
     * @return array<mixed, T>
     */
    public function jsonSerialize(): array
    {
        return $this->all();
    }

    /**
     * Return the different with the given collection
     * @param BaseCollection<T> $collection
     * @return $this<T>
     */
    abstract public function diff(BaseCollection $collection): self;

    /**
     * Whether two collections are equal
     * @param BaseCollection<T> $collection
     * @return bool
     */
    abstract public function equals(BaseCollection $collection): bool;

    /**
     * Returns a portion of the collection.
     * @param int $offset
     * @param int|null $length
     * @return null|$this
     */
    abstract public function slice(int $offset, ?int $length = null): ?self;

    /**
     * Fill the collection
     * @param array<mixed, T> $data
     * @return void
     */
    abstract public function fill(array $data): void;

    /**
     *
     * @return void
     */
    protected function repopulate(): void
    {
        $oldData = array_values($this->all());
        $this->data->setData($oldData);
    }
}
