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

use Exception;
use Traversable;

/** A set of values; elements can be of any type that supports equality. */
class Set implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * Instantiate a new Set.
     *
     * @param Set|\Traversable|array $data The data from which to create this Set.
     *
     * @return Set
     */
    public static function new($data = []): Set
    {
        if ($data instanceof Set) {
            return $data;
        }
        if ($data instanceof \Traversable) {
            $data = iterator_to_array($data);
        }
        if (is_array($data)) {
            $pairs = [];
            foreach ($data as $v) {
                $pairs[] = [$v, true];
            }
            return new Set(Map::fromPairs($pairs));
        }
        throw new Exception('Set::new accepts a Traversable or an array only');
    }

    private Map $map;

    private function __construct(Map $map)
    {
        $this->map = $map;
    }

    // IteratorAggregate methods

    /** @inheritDoc */
    public function getIterator(): Traversable
    {
        return (function () {
            foreach ($this->map as [$k]) {
                yield $k;
            }
        })();
    }

    // Countable methods

    /** @inheritDoc */
    public function count(): int
    {
        return count($this->map);
    }

    // ArrayAccess methods

    /** @inheritDoc */
    public function offsetExists($key): bool
    {
        return isset($this->map[$key]);
    }

    /** @inheritDoc */
    public function offsetGet($key): bool
    {
        return isset($this->map[$key]);
    }

    /** @inheritDoc */
    public function offsetSet($offset, $value): void
    {
        throw new Exception('Set is readonly');
    }

    /** @inheritDoc */
    public function offsetUnset($offset): void
    {
        throw new Exception('Set is readonly');
    }

    // Normal class methods

    /**
     * Add the specified element to this set.
     *
     * @param $key The value to add to the set.
     *
     * @return Set
     */
    public function add($key): Set
    {
        if (isset($this->map[$key])) {
            return $this;
        } else {
            $map = $this->map->set($key, true);
            return new Set($map);
        }
    }

    /**
     * Convert this set to a vector. Order of elements is non-deterministic.
     *
     * @return Vector
     */
    public function toVector(): Vector
    {
        return Vector::new($this);
    }
}
