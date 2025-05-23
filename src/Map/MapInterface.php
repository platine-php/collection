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
 *  @file MapInterface.php
 *
 *  The MapInterface class
 *
 *  @package    Platine\Collection\Map
 *  @author Platine Developers Team
 *  @copyright  Copyright (c) 2020
 *  @license    http://opensource.org/licenses/MIT  MIT License
 *  @link   https://www.platine-php.com
 *  @version 1.0.0
 *  @filesource
 */

declare(strict_types=1);

namespace Platine\Collection\Map;

/**
 * @class MapInterface
 * @package Platine\Collection\Map
 */
interface MapInterface
{
    /**
     * Add element to the collection
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function add(mixed $key, mixed $value): void;

    /**
     * Get the element for the given key
     * @param mixed $key
     * @return mixed
     */
    public function get(mixed $key): mixed;

    /**
     * Remove the element for the given key
     * @param mixed $key
     * @return void
     */
    public function remove(mixed $key): void;

    /**
     * Update the element
     * @param mixed $key
     * @param mixed $value
     * @return bool
     */
    public function update(mixed $key, mixed $value): bool;
}
