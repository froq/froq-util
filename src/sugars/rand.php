<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\Numbers;

/**
 * Rand int.
 * @param  int|null $min
 * @param  int|null $max
 * @return int
 */
function rand_int(int $min = null, int $max = null): int
{
    return Numbers::randomInt($min, $max);
}

/**
 * Rand float.
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
 * Rand item.
 * @param  array       $array
 * @param  int|string &$key
 * @return ?any
 * @since  4.1
 */
function rand_item(array $array, &$key = null)
{
    $key = array_rand($array);
    if ($key === null) {
        return null;
    }

    return $array[$key];
}

/**
 * Rand items.
 * @param  array              $array
 * @param  int                $limit
 * @param  array<int|string> &$keys
 * @return ?array
 * @since  4.1
 */
function rand_items(array $array, int $limit, array &$keys = null): ?array
{
    $ret = [];
    $len = count($array);

    do {
        $key = array_rand($array);
        if ($key === null) {
            return null;
        }

        $ret[$key] = $array[$key];
        $retlen    = count($ret);
    } while ($retlen < $len && $retlen < $limit);

    $keys = array_keys($ret) ?: null;

    return $ret;
}
