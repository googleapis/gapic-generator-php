<?php declare(strict_types=1);

namespace Google\Generator\Collections;

/** Interface to implement to specifify equality for Vector, Set, and Map */
interface Equality
{
    /**
     * Get the integer hash value for this instance.
     * 
     * @return int
     */
    public function getHash(): int;

    /**
     * Return whether this instance is equal to another value.
     * 
     * @param mixed $other The value for which to compare equality.
     * 
     * @return bool
     */
    public function isEqualTo($other): bool;
}
