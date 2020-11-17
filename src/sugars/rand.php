<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
