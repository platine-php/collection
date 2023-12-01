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
 *  @file DataContainer.php
 *
 *  The Data Container class contains the raw collection data
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

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

/**
 * Class DataContainer
 * @package Platine\Collection
 * @template T
 * @implements ArrayAccess<mixed, T>
 * @implements IteratorAggregate<mixed, T>
 */
class DataContainer implements ArrayAccess, IteratorAggregate
{

    /**
     *
     * @var array<mixed, T>
     */
    protected array $data = [];

    /**
     * Create new instance
     * @param array<mixed, T> $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Return the data in the container
     * @return array<mixed, T>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set the data container
     * @param array<mixed, T> $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }


    /**
     *
     * @return ArrayIterator<mixed, T>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    /**
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset])
                ? $this->data[$offset]
                : null;
    }

    /**
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }
}
