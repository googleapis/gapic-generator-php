<?php declare(strict_types=1);

namespace Google\Generator\Collections;

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
    public static function new($data = []) : Set
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
                $pairs[] = [$v, TRUE];
            }
            return new Set(Map::fromPairs($pairs));
        }
        throw new \Exception('Set::new accepts a Traversable or an array only');
    }

    private Map $map;

    private function __construct(Map $map)
    {
        $this->map = $map;
    }

    // IteratorAggregate methods

    /** @inheritDoc */
    public function getIterator()
    {
        return (function() {
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
        throw new \Exception('Set is readonly');
    }

    /** @inheritDoc */
    public function offsetUnset($offset): void
    {
        throw new \Exception('Set is readonly');
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
}
