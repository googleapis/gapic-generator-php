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

/** A 1-dimensional, value-only array. */
class Vector implements \IteratorAggregate, \Countable, \ArrayAccess, Equality
{
    use EqualityHelper;

    /**
     * Instantiate a new Vector.
     *
     * @param Vector|\Traversable|array $data The data from which to create this Vector;
     *     If an array, then only the values are used, the keys are ignored.
     *
     * @return Vector
     */
    public static function new($data = []): Vector
    {
        if ($data instanceof Vector) {
            return $data;
        } elseif ($data instanceof \Traversable) {
            return new Vector(iterator_to_array($data));
        } elseif (is_array($data)) {
            return new Vector(array_values($data));
        } else {
            throw new \Exception('Vector::New accepts a Traversable or an array only');
        }
    }

    /**
     * Zip two Vectors, with optional mapping function. If the vectors are not the same
     * length, then the excess elements in the longer vector will be ignored.
     *
     * @param Vector $a The first Vector to zip.
     * @param Vector $b The second Vector to zip.
     * @param ?Callable $fnMap The optional map function. This will be called with two
     *     parameters, one element from each input vector; an optional third parameter
     *     is the element index. If this function is omitted, then each element of the
     *     output vector will contain an array of length two.
     */
    public static function zip(Vector $a, Vector $b, ?Callable $fnMap = null): Vector
    {
        $count = min(count($a), count($b));
        $result = [];
        if ($fnMap) {
            for ($i = 0; $i < $count; $i++) {
                $result[] = $fnMap($a[$i], $b[$i], $i);
            }
        } else {
            for ($i = 0; $i < $count; $i++) {
                $result[] = [$a[$i], $b[$i]];
            }
        }
        return new Vector($result);
    }

    /**
     * Create a vector with the specified range, inclusive of both $start and $end.
     *
     * @param int $start The inclusive start value of the range.
     * @param int $end The inclusive end value of the range.
     *
     * @return Vector
     */
    public static function range(int $start, int $end)
    {
        return new Vector(range($start, $end));
    }

    private array $data;

    private function __construct($data)
    {
        $this->data = $data;
    }

    // IteratorAggregate methods

    /** @inheritDoc */
    public function getIterator()
    {
        return (function() {
            foreach ($this->data as $k => $v) {
                yield $k => $v;
            }
        })();
    }

    // Countable methods

    /** @inheritDoc */
    public function count(): int
    {
        return count($this->data);
    }

    // ArrayAccess methods

    /** @inheritDoc */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /** @inheritDoc */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /** @inheritDoc */
    public function offsetSet($offset, $value): void
    {
        throw new \Exception('Vector is readonly');
    }

    /** @inheritDoc */
    public function offsetUnset($offset): void
    {
        throw new \Exception('Vector is readonly');
    }

    // Equality methods

    /** @inheritDoc */
    public function getHash(): int
    {
        $hash = 1;
        foreach ($this->data as $item) {
            $hash *= 23;
            $hash ^= static::hash($item);
        }
        return $hash;
    }

    /** @inheritDoc */
    public function isEqualTo($other): bool
    {
        if (!($other instanceof Vector)) {
            return false;
        }
        if (count($this) !== count($other)) {
            return false;
        }
        foreach ($this->data as $key => $item) {
            if (!static::equal($other->data[$key], $item)) {
                return false;
            }
        }
        return true;
    }

    // Normal class methods

    /**
     * Prepend an item to the beginning of this vector.
     *
     * @param mixed $item The item to prepend.
     *
     * @return Vector
     */
    public function prepend($item): Vector
    {
        $data = $this->data;
        array_unshift($data, $item);
        return new Vector($data);
    }

    /**
     * Append an item to the end of this vector.
     *
     * @param mixed $item The item to append.
     *
     * @return Vector
     */
    public function append($item): Vector
    {
        $data = $this->data;
        $data[] = $item;
        return new Vector($data);
    }

    /**
     * Concatenate a vector on to the end of this vector.
     *
     * @param Vector $vector The vector to concatentate.
     *
     * @return Vector
     */
    public function concat(Vector $vector): Vector
    {
        return new Vector(array_merge($this->data, $vector->data));
    }

    /**
     * Filter elements from this vector.
     *
     * @param Callable $fnPredicate Elements will be present in the returned vector
     *     for which this function returns true.
     *
     * @return Vector
     */
    public function filter(Callable $fnPredicate): Vector
    {
        $result = [];
        foreach ($this->data as $item) {
            if ($fnPredicate($item)) {
                $result[] = $item;
            }
        }
        return new Vector($result);
    }

    /**
     * Transform elements of this vector.
     *
     * @param Callable $fnMap Transformation function called for each element in this vector.
     *     Index passed as second parameter if required.
     *
     * @return Vector
     */
    public function map(Callable $fnMap): Vector
    {
        $result = [];
        foreach ($this->data as $index => $item) {
            $result[] = $fnMap($item, $index);
        }
        return new Vector($result);
    }

    /**
     * Transform and flatten elements of this vector.
     *
     * @param Callable $fnFlatMap Transformation function called for each element in this vector;
     *     must return a vector. Index passed as second parameter if required.
     *
     * @return Vector
     */
    public function flatMap(Callable $fnFlatMap): Vector
    {
        $parts = [];
        foreach ($this->data as $index => $item) {
            $mapping = $fnFlatMap($item, $index);
            if (!($mapping instanceof Vector)) {
                throw new \Exception("flatMap() function must return a Vector");
            }
            $parts[] = $mapping->data;
        }
        return new Vector(array_merge(...$parts));
    }

    /**
     * Flatten elements from this vector. Each element which is a vector is recursively flattened.
     *
     * @return Vector
     */
    public function flatten(): Vector
    {
        return $this->flatMap(fn($x) => $x instanceof Vector ? $x->flatten() : Vector::New([$x]));
    }

    /**
     * Group elements of this vector using a key function.
     *
     * @param Callable $fnKey The function to return a group key for each element.
     * @param ?Callable $fnValue Optional function to return an value for each element.
     *     If omitted, the element value is used.
     *
     * @return Map A map of Vectors.
     */
    public function groupBy(Callable $fnKey, ?Callable $fnValue = null): Map
    {
        $map = Map::New();
        foreach ($this->data as $item) {
            $key = $fnKey($item);
            $value = $fnValue ? $fnValue($item) : $item;
            $mapValue = isset($map[$key]) ? $map[$key]->append($value) : Vector::New([$value]);
            $map = $map->set($key, $mapValue);
        }
        return $map;
    }

    /**
     * Include only unique values from this vector.
     * Elements are kept in order of first occurance of each element.
     *
     * @return Vector
     */
    public function distinct(): Vector
    {
        $set = Set::New();
        $data = [];
        foreach ($this->data as $item) {
            if (!$set[$item]) {
                $set = $set->add($item);
                $data[] = $item;
            }
        }
        return new Vector($data);
    }

    /**
     * Take elements from the beginning of this vector.
     *
     * @return Vector
     */
    public function take(int $n): Vector
    {
        return $n >= count($this->data) ? $this : new Vector(array_slice($this->data, 0, $n));
    }

    /**
     * Take elements from the end of this vector.
     *
     * @return Vector
     */
    public function takeLast(int $n): Vector
    {
        return $n >= count($this->data) ? $this : new Vector(array_slice($this->data, count($this->data) - $n));
    }

    /**
     * Take elements from the beginning of this vector, whilst a predicate returns true.
     *
     * @param Callable $fnPredicate Elements taken whilst this function returns true.
     *
     * @return Vector
     */
    public function takeWhile(Callable $fnPredicate): Vector
    {
        for ($i = 0; $i < count($this->data); $i++) {
            if (!$fnPredicate($this->data[$i])) {
                return $this->take($i);
            }
        }
        return $this;
    }

    /**
     * Skip elements from the beginning of this vector.
     *
     * @return Vector
     */
    public function skip(int $n): Vector
    {
        return $n === 0 ? $this : new Vector(array_slice($this->data, $n));
    }

    /**
     * Skip elements from the end of this vector.
     *
     * @return Vector
     */
    public function skipLast(int $n) : Vector
    {
        return new Vector(array_slice($this->data, 0, max(0, count($this->data) - $n)));
    }

    /**
     * Skip elements from the beginning of this vector, whilst a predicate returns true.
     *
     * @param Callable $fnPredicate Elements skipped whilst this function returns true.
     *
     * @return Vector
     */
    public function skipWhile(Callable $fnPredicate): Vector
    {
        for ($i = 0; $i < count($this->data); $i++) {
            if (!$fnPredicate($this->data[$i])) {
                return $this->skip($i);
            }
        }
        return new Vector([]);
    }

    /**
     * Skip elements from the end of this vector, whilst a predicate returns true.
     *
     * @param Callable $fnPredicate Elements skipped whilst this function returns true.
     *
     * @return Vector
     */
    public function skipLastWhile(Callable $fnPredicate): Vector
    {
        for ($i = count($this->data); $i > 0; $i--) {
            if (!$fnPredicate($this->data[$i - 1])) {
                return $this->take($i);
            }
        }
        return new Vector([]);
    }

    /**
     * Return the first element from this vector, or null if this vector is empty.
     *
     * @return mixed
     */
    public function firstOrNull()
    {
        return count($this->data) === 0 ? null : $this->data[0];
    }

    /**
     * Return the last element from this vector.
     *
     * @return mixed
     */
    public function last()
    {
        return $this->data[count($this->data) - 1];
    }

    /**
     * Returns true if there are any elements in this vector for which the predicate returns true.
     * If the optional predicate is omitted, then returns true if this vector contains any elements.
     *
     * @param ?Callable $fnPredicate Optional predicate, to select elements from this vector
     *
     * @return bool
     */
    public function any(?Callable $fnPredicate = null): bool
    {
        foreach ($this->data as $item) {
            if (!$fnPredicate || $fnPredicate($item)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Join elements into a string, with the specified joiner string.
     * All elements must be convertable to string.
     *
     * @param string $joiner String used between elements, defaults to an empty string.
     *
     * @return string
     */
    public function join(string $joiner = ''): string
    {
        return implode($joiner, $this->data);
    }

    /**
     * Return whether this vector contains the specified item.
     *
     * @param mixed $item Item to look for.
     *
     * @return bool
     */
    public function contains($item): bool
    {
        foreach ($this->data as $dataItem) {
            if (static::equal($item, $dataItem)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Converts this vector to a map, using the key function, and optional value function.
     *
     * @param Callable $fnKey Return a key for an element.
     * @param ?Callable $fnValue Return a value for an element; if omitted, the element itself is used.
     *
     * @return Map
     */
    public function toMap(Callable $fnKey, ?Callable $fnValue = null): Map
    {
        $pairs = [];
        foreach ($this->data as $item) {
            $pairs[] = [$fnKey($item), $fnValue ? $fnValue($item) : $item];
        }
        return Map::FromPairs($pairs);
    }

    /**
     * Converts this vector to a set.
     *
     * @return Set
     */
    public function toSet(): Set
    {
        return Set::new($this);
    }

    /**
     * Converts this vector to a standard PHP array, with incrementing numeric keys or custom keys.
     *
     * @param ?Callable $fnKey Optional. Key function to create an associative array.
     * @param ?Callable $fnValue Optional. Value function to create an associative array.
     *
     * @return array
     */
    public function toArray(?Callable $fnKey = null, ?Callable $fnValue = null): array
    {
        if (is_null($fnKey)) {
            return $this->data;
        } else {
            $result = [];
            foreach ($this as $item) {
                $result[$fnKey($item)] = is_null($fnValue) ? $item : $fnValue($item);
            }
            return $result;
        }
    }

    /**
     * Return the maximum value form this vector. All elements must be numeric.
     *
     * @param mixed $defaultValue The default return value if this vector is empty, defaults to null.
     *
     * @return mixed
     */
    public function max($defaultValue = null)
    {
        return count($this->data) === 0 ? $defaultValue : max($this->data);
    }

    /** @inheritDoc */
    public function __toString(): string
    {
        if (count($this->data) <= 20) {
            $s = $this->join(', ');
        } else {
            $s = "{$this->take(10)->join(', ')} ... {$this->takeLast(10)->join(', ')}";
        }
        return "[{$s}]";
    }
}
