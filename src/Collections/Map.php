<?php
/*
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
declare(strict_types=1);

namespace Google\Generator\Collections;

/** A map of key->value; Keys can be of any type that supports equality. */
class Map implements \IteratorAggregate, \Countable, \ArrayAccess
{
    use EqualityHelper;

    /**
     * Instantiate a new Map.
     *
     * @param Map|\Traversable|array $data The data from which to create this Map.
     *
     * @return Map
     */
    public static function new($data = []): Map
    {
        if ($data instanceof Map) {
            return $data;
        }
        if ($data instanceof \Traversable) {
            $data = iterator_to_array($data);
        }
        if (is_array($data)) {
            $pairs = [];
            foreach ($data as $k => $v) {
                $pairs[] = [$k, $v];
            }
            return static::fromPairs($pairs);
        }
        throw new \Exception('Map::New accepts a Traversable or an array only');
    }

    /**
     * Create a new Map from an array of key/value pair values.
     *
     * @param array $pairs An array in which each value is a length-2 array containing a key/value pair.
     *
     * @return Map
     */
    public static function fromPairs(array $pairs): Map
    {
        $data = [];
        foreach ($pairs as [$k, $v]) {
            if (static::apply($data, $k, 1, $v)[0]) {
                throw new \Exception('Cannot add two items with the same key');
            }
        }
        return new Map($data, count($pairs));
    }

    /**
     * @param array $data The map data.
     * @param mixed $k The key to lookup and/or modify.
     * @param int $action -1 to remove element $k, 0 to lookup whether an element exists, +1 to add/overwrite an element.
     * @param mixed $v The element to add/overwrite, if relevant.
     *
     * @return array Array containing whether key exists, then the value (if relevant).
     */
    private static function apply(array &$data, $k, int $action, $v): array
    {
        if (is_null($k)) {
            throw new \Exception('null keys are invalid');
        }
        $hash = static::Hash($k);
        if (isset($data[$hash])) {
            foreach ($data[$hash] as $index => [$k0, $v0]) {
                if (static::equal($k0, $k)) {
                    if ($action === 1) {
                        $data[$hash][$index] = [$k, $v];
                    } elseif ($action == -1) {
                        unset($data[$hash][$index]);
                    }
                    return [true, $v0];
                }
            }
            if ($action === 1) {
                $data[$hash][] = [$k, $v];
            }
            return [false, null];
        } else {
            if ($action === 1) {
                $data[$hash] = [[$k, $v]];
            }
            return [false, null];
        }
    }

    private array $data;
    private int $count;

    private function __construct($data, int $count)
    {
        $this->data = $data;
        $this->count = $count;
    }

    // IteratorAggregate methods

    /** @inheritDoc */
    public function getIterator()
    {
        return (function () {
            foreach ($this->data as $kvs) {
                foreach ($kvs as $kv) {
                    // Returns [<key>, <value>] pairs.
                    yield $kv;
                }
            }
        })();
    }

    // Countable methods

    /** @inheritDoc */
    public function count(): int
    {
        return $this->count;
    }

    // ArrayAccess methods

    /** @inheritDoc */
    public function offsetExists($key): bool
    {
        return static::Apply($this->data, $key, 0, null)[0];
    }

    /** @inheritDoc */
    public function offsetGet($key)
    {
        [$exists, $value] = static::Apply($this->data, $key, 0, null);
        if ($exists) {
            return $value;
        }
        throw new \Exception('Key does not exist');
    }

    /** @inheritDoc */
    public function offsetSet($offset, $value): void
    {
        throw new \Exception('Map is readonly');
    }

    /** @inheritDoc */
    public function offsetUnset($offset): void
    {
        throw new \Exception('Map is readonly');
    }

    // Normal class methods

    /**
     * Set the specified value for the specified key. Will overwrite if the key is already present.
     *
     * @param mixed $key The key at which to add or overwrite.
     * @param mixed $value The value to add or overwrite.
     *
     * @return Map
     */
    public function set($key, $value): Map
    {
        // Must copy into new var, so this map isn't changed.
        $data = $this->data;
        [$existed] = static::apply($data, $key, 1, $value);
        return new Map($data, $this->count + ($existed ? 0 : 1));
    }

    /**
     * Filter elements from this map.
     *
     * @param Callable $fnPredicate Elements will be present in the returned map
     *     for which this function returns true. This function is called with two
     *     parameters - the key, and the value.
     *
     * @return Map
     */
    public function filter(callable $fnPredicate): Map
    {
        $resultPairs = [];
        foreach ($this as [$k, $v]) {
            if ($fnPredicate($k, $v)) {
                $resultPairs[] = [$k, $v];
            }
        }
        return static::fromPairs($resultPairs);
    }

    /**
     * Transform elements of this map.
     *
     * @param Callable $fnMap Transformation function called for each element in this map.
     *     This function is called with two parameters, the key and the value, and must return
     *     a single value which will be the new value.
     *
     * @return Map
     */
    public function mapValues(callable $fnMap): Map
    {
        $resultPairs = [];
        foreach ($this as [$k, $v]) {
            $resultPairs[] = [$k, $fnMap($k, $v)];
        }
        return static::fromPairs($resultPairs);
    }

    /**
     * Return a vector of the keys of this map.
     *
     * @return Vector
     */
    public function keys(): Vector
    {
        $result = [];
        foreach ($this as [$k]) {
            $result[] = $k;
        }
        return Vector::new($result);
    }

    /**
     * Return a vector of the values of this map.
     *
     * @return Vector
     */
    public function values(): Vector
    {
        $result = [];
        foreach ($this as [$_, $v]) {
            $result[] = $v;
        }
        return Vector::new($result);
    }

    /**
     * Get a single value from this map, with a default value returned if the key does not exist.
     *
     * @param mixed $key The key of the value to retrieve.
     * @param mixed $default The default value to return if the key does not exist.
     *
     * @return mixed
     */
    public function get($key, $default)
    {
        [$exists, $value] = static::apply($this->data, $key, 0, null);
        return $exists ? $value : $default;
    }
}
