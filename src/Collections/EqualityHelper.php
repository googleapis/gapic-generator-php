<?php
declare(strict_types=1);

namespace Google\Generator\Collections;

trait EqualityHelper
{
    private static function hash($k): int
    {
        if (is_int($k)) {
            return $k;
        } elseif (is_string($k)) {
            return crc32($k);
        } elseif (is_object($k)) {
            if ($k instanceof Equality) {
                return $k->getHash();
            } else {
                return spl_object_id($k);
            }
        }
        throw new \Exception("Cannot use a map key of type: '" . gettype($k) . "'");
    }

    private static function equal($a, $b): bool
    {
        if ($a === $b) {
            // Handles int, string
            return true;
        } elseif (is_object($a) && is_object($b) && get_class($a) === get_class($b) && $a instanceof Equality) {
            return $a->isEqualTo($b);
        }
        return false;
    }
}
