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
 *  @file Stack.php
 *
 *  The Stack class
 *
 *  @package    Platine\Collection\Stack
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Collection\Stack;

use Countable;
use Platine\Collection\Exception\InvalidOperationException;
use Platine\Collection\TypeCheck;

/**
 * @class Stack
 * @package Platine\Collection\Stack
 */
class Stack implements Countable
{
    /**
     *
     * @var array<int, mixed>
     */
    protected array $data = [];

    /**
     * Create new instance
     * @param string $type
     */
    public function __construct(protected string $type)
    {
    }

    /**
     *
     * @return void
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * {@inheritedoc}
     */
    public function count(): int
    {
        return count($this->data);
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
     * @return mixed
     */
    public function peek(): mixed
    {
        if ($this->isEmpty()) {
            throw new InvalidOperationException('The collection is empty');
        }

        return $this->data[$this->count() - 1];
    }

    /**
     *
     * @return mixed
     */
    public function pop(): mixed
    {
        return array_pop($this->data);
    }


    /**
     *
     * @param mixed $value
     * @return mixed
     */
    public function push(mixed $value): mixed
    {
        TypeCheck::isValueOf(
            $value,
            $this->type,
            sprintf(
                'The type specified for this collection is [%s], '
                    . 'you cannot pass a value of type [%s]',
                $this->type,
                gettype($value)
            )
        );

        $this->data[] = $value;

        return $value;
    }
}
