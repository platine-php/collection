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
 *  @file HashMap.php
 *
 *  The hash map class is like an associative array
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

use OutOfRangeException;
use Platine\Collection\BaseCollection;
use Platine\Collection\Exception\InvalidOperationException;
use Platine\Collection\MergeableInterface;
use Platine\Collection\SortableInterface;
use Platine\Collection\TypeCheck;

/**
 * Class HashMap
 * @package Platine\Collection\Map
 * @template T
 * @extends BaseCollection<T>
 * @implements MergeableInterface<T>
 * @implements SortableInterface<T>
 */
class HashMap extends BaseCollection implements
    MapInterface,
    MergeableInterface,
    SortableInterface
{
    /**
     * The type of the key
     * @var mixed
     */
    protected $keyType;

    /**
     * The type of the value
     * @var mixed
     */
    protected $valueType;

    /**
     * Create new instance
     * @param mixed $keyType
     * @param mixed $valueType
     * @param array<mixed, T> $initials
     */
    public function __construct($keyType, $valueType, array $initials = [])
    {
        $this->keyType = $keyType;
        $this->valueType = $valueType;

        foreach ($initials as $key => $value) {
            $this->validateEntry($key, $value);
        }

        parent::__construct($initials);
        $this->initializePairs($initials);
    }

    /**
     *
     * @param mixed $key
     * @param T $value
     * @return void
     */
    public function add($key, $value): void
    {
        $this->validateEntry($key, $value);
        $this->data->offsetSet($key, new Pair($key, $value));
    }

    /**
     *
     * @param HashMap<T> $collection
     * @return HashMap<T>
     * @throws InvalidOperationException
     */
    public function diff(BaseCollection $collection): BaseCollection
    {
        if (!$collection instanceof self) {
            throw new InvalidOperationException(
                'You should only compare a Map against another Map'
            );
        }

        if ($this->keyType !== $collection->getKeyType()) {
            throw new InvalidOperationException(sprintf(
                'The key type for this map is [%s], you cannot pass a map with [%s] as key type',
                $this->keyType,
                $collection->getKeyType()
            ));
        }

        if ($this->valueType !== $collection->getValueType()) {
            throw new InvalidOperationException(sprintf(
                'The value type for this map is [%s], you cannot pass a map with [%s] as value type',
                $this->keyType,
                $collection->getKeyType()
            ));
        }

        $diffValues = array_udiff_uassoc(
            $this->all(),
            $collection->all(),
            function ($a, $b) {
                return $a <=> $b;
            },
            function ($c, $d) {
                return $c <=> $d;
            }
        );

        return new self($this->keyType, $this->valueType, $diffValues);
    }

    /**
     * Return the type of the key
     * @return mixed
     */
    public function getKeyType()
    {
        return $this->keyType;
    }

    /**
     * Return the type of the value
     * @return mixed
     */
    public function getValueType()
    {
        return $this->valueType;
    }

    /**
     *
     * @param array<mixed, T> $data
     * @return void
     */
    public function fill(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->add($key, $value);
        }
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
            $val = call_user_func($callback, $value->getKey(), $value->getValue());
            if ($val === true) {
                $matches[$value->getKey()] = $value->getValue();
            }
        }

        return count($matches) > 0
                ? new $this($this->keyType, $this->valueType, $matches)
                : null;
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
                ? new $this($this->keyType, $this->valueType, $this->all())
                : null;
    }

     /**
      *
      * @param HashMap<T> $collection
      * @return bool
      * @throws InvalidOperationException
      */
    public function equals(BaseCollection $collection): bool
    {
        if (!$collection instanceof self) {
            throw new InvalidOperationException(
                'You should only compare an map against another map'
            );
        }

        return $this->all() == $collection->all();
    }

    /**
     *
     * @param callable $callback
     * @return void
     */
    public function forEach(callable $callback): void
    {
        $data = $this->all();
        array_walk($data, $callback);

        $this->initializePairs($data);
    }

     /**
      * Return the value for given key
      * @param mixed $key
      * @return T|null
      */
    public function get($key)
    {
        return $this->data->offsetExists($key)
               ? $this->data->offsetGet($key)->getValue()
               : null;
    }

     /**
     * {@inheritedoc}
      * @param HashMap<T> $collection
      * @return HashMap<T>
     */
    public function merge(BaseCollection $collection): BaseCollection
    {
        TypeCheck::isEqual(
            $this->getKeyType(),
            $collection->getKeyType(),
            sprintf(
                'The new map key should be of type %s',
                $this->keyType
            )
        );

        TypeCheck::isEqual(
            $this->getValueType(),
            $collection->getValueType(),
            sprintf(
                'The new map value should be of type %s',
                $this->valueType
            )
        );

        return new $this(
            $this->keyType,
            $this->valueType,
            array_merge($this->all(), $collection->all())
        );
    }

    /**
     * {@inheritedoc}
     */
    public function first()
    {
        throw new InvalidOperationException('Can not call this method in map');
    }

    /**
     * {@inheritedoc}
     */
    public function last()
    {
        throw new InvalidOperationException('Can not call this method in map');
    }

     /**
     * {@inheritedoc}
     */
    public function remove($key): void
    {
        if ($this->isEmpty()) {
            throw new OutOfRangeException('The collection is empty');
        }

        if (!$this->data->offsetExists($key)) {
            throw new OutOfRangeException(sprintf(
                'The collection key [%s] does not exists',
                $key
            ));
        }

        $this->data->offsetUnset($key);
    }

     /**
      *
      * @param int $offset
      * @param int|null $length
      * @return HashMap<T>|null
      */
    public function slice(int $offset, ?int $length = null): ?BaseCollection
    {
        $newData = array_slice($this->all(), $offset, $length, true);

        return count($newData) > 0
            ? new $this(
                $this->keyType,
                $this->valueType,
                $newData
            )
            : null;
    }

     /**
      *
      * @param callable $callback
      * @return HashMap<T>|null
      */
    public function sort(callable $callback): ?BaseCollection
    {
        $data = $this->all();

        return uasort($data, $callback)
                ? new $this(
                    $this->keyType,
                    $this->valueType,
                    $data
                )
                : null;
    }

    /**
     *
     * @param mixed $key
     * @param T $value
     * @return bool
     * @throws OutOfRangeException
     */
    public function update($key, $value): bool
    {
        $this->validateEntry($key, $value);

        if (!$this->data->offsetExists($key)) {
            throw new OutOfRangeException(sprintf(
                'The collection key [%s] does not exists',
                $key
            ));
        }

        $this->data[$key]->setValue($value);

        return $this->data[$key]->getValue() === $value;
    }

    /**
     *
     * @return array<mixed, T>
     */
    public function all(): array
    {
        $data = [];
        foreach ($this->data as $pair) {
            $data[$pair->getKey()] = $pair->getValue();
        }

        return $data;
    }

    /**
     *
     * @return string
     */
    public function toJson(): string
    {
        /* Thank to interface JsonSerializable */
        $json = json_encode($this);
        return $json === false ? '' : $json;
    }


    /**
     * Validate the type of key and value
     * @param mixed $key
     * @param mixed $value
     *
     * @return bool
     */
    protected function validateEntry($key, $value): bool
    {
        TypeCheck::isValueOf(
            $key,
            $this->keyType,
            sprintf(
                'The key type specified for this map is [%s], you cannot pass [%s]',
                $this->keyType,
                gettype($key)
            )
        );

        TypeCheck::isValueOf(
            $value,
            $this->valueType,
            sprintf(
                'The value type specified for this map is [%s], you cannot pass [%s]',
                $this->valueType,
                gettype($value)
            )
        );

        return false;
    }

    /**
     * Initialize the pair values
     * @param array<mixed, mixed> $data
     * @return void
     */
    protected function initializePairs(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = new Pair($key, $value);
        }
    }
}
