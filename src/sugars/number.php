<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Numbers;

/**
 * Make a number from given a numeric input.
 *
 * @param  numeric  $in
 * @param  int|null $decimals
 * @return int|float|null
 * @since  3.0
 */
function number($in, int $decimals = null): int|float|null
{
    return Numbers::convert($in, $decimals);
}

/**
 * Compare two numbers.
 *
 * @param  string|number $a
 * @param  string|number $b
 * @param  int|null      $precision
 * @return int|null
 * @since  3.0
 */
function number_compare($a, $b, int $precision = null): int|null
{
    return Numbers::compare($a, $b, $precision);
}

/**
 * Check whether given numbers are equal.
 *
 * @param  string|number $a
 * @param  string|number $b
 * @param  int|null      $precision
 * @return bool|null
 * @since  3.0
 */
function number_equals($a, $b, int $precision = null): bool|null
{
    return Numbers::equals($a, $b, $precision);
}

/**
 * Check whether given input is digit.
 *
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_digit($in): bool
{
    return Numbers::isDigit($in);
}

/**
 * Check whether given input is an ID (useful for any (db) incremental id checking).
 *
 * anyparam  number $in
 * @return bool
 * @since  3.0
 */
function is_id($in): bool
{
    return Numbers::isId($in);
}

/**
 * Check whether given input is uint.
 *
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_uint($in): bool
{
    return Numbers::isUInt($in);
}

/**
 * Check whether given input is ufloat.
 *
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_ufloat($in): bool
{
    return Numbers::isUFloat($in);
}

/**
 * Check whether given input is signed.
 *
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_signed($in): bool
{
    return Numbers::isSigned($in);
}

/**
 * Check whether given input is unsigned.
 *
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_unsigned($in): bool
{
    return Numbers::isUnsigned($in);
}
