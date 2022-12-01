<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

use froq\util\Numbers;

/**
 * Get a random int, optionally with min/max directives.
 *
 * @param  int|null $min
 * @param  int|null $max
 * @return int
 */
function rand_int(int $min = null, int $max = null): int
{
    return Numbers::randomInt($min, $max);
}

/**
 * Get a random float, optionally with min/max and precision directives.
 *
 * @param  float|null $min
 * @param  float|null $max
 * @param  int|null   $precision
 * @return float
 */
function rand_float(float $min = null, float $max = null, int $precision = null): float
{
    return Numbers::randomFloat($min, $max, $precision);
}

/**
 * Get a random item from given array, filling ref'ed key with found key.
 *
 * @param  array       $array
 * @param  int|string &$key
 * @return mixed|null
 * @since  4.1
 */
function rand_item(array $array, int|string &$key = null): mixed
{
    srand();

    $key = array_rand($array);
    if ($key === null) {
        return null;
    }

    return $array[$key];
}

/**
 * Get a random items from given array by given limit, filling ref'ed key with found key.
 *
 * @param  array              $array
 * @param  int                $limit
 * @param  array<int|string> &$keys
 * @return array|null
 * @since  4.1
 */
function rand_items(array $array, int $limit, array &$keys = null): array|null
{
    srand();

    $ret  = [];
    $len  = count($array);
    $keys = null;

    do {
        $key = array_rand($array);
        if ($key === null) {
            return null;
        }

        $ret[$key] = $array[$key];
        $retlen    = count($ret);
    } while ($retlen < $limit && $retlen < $len);

    $keys = array_keys($ret);

    return $ret;
}
