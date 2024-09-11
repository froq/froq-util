<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Int div (as intdiv() not allows floats).
 *
 * @param  int|float $a
 * @param  int|float $b
 * @return int
 * @causes DivisionByZeroError
 */
function idiv(int|float $a, int|float $b): int
{
    return intval($a / $b);
}

/**
 * Int mod (as % gives "Implicit conversion from float .. to int loses precision").
 *
 * @param  int|float $a
 * @param  int|float $b
 * @return int
 * @causes DivisionByZeroError
 */
function imod(int|float $a, int|float $b): int {
    return @intval($a % $b);
}

/**
 * Calculate sum of given numbers.
 *
 * @param  mixed ...$nums
 * @return int|float
 */
function sum(mixed ...$nums): int|float {
    $res = 0;

    if ($nums) {
        $first = first($nums);
        if (is_array($first)) {
            $res = array_sum($first);
        } else {
            $res = array_sum($nums);
        }
    }

    return $res;
}

/**
 * Calculate avg of given numbers.
 *
 * @param  mixed ...$nums
 * @return int|float
 */
function avg(mixed ...$nums): float {
    $res = 0.0;

    if ($nums) {
        $first = first($nums);
        // No NAN for empty arrays.
        if ($first && is_array($first)) {
            $res = fdiv(sum($first), count($first));
        } else {
            $res = fdiv(sum($nums), count($nums));
        }
    }

    return $res;
}
