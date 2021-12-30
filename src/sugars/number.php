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
 * @param  int|float|string $input
 * @param  int|null         $decimals
 * @return int|float
 * @since  3.0
 */
function number(int|float|string $input, int $decimals = null): int|float
{
    return Numbers::convert($input, $decimals);
}

/**
 * Compare two numbers.
 *
 * @param  int|float $number1
 * @param  int|float $number2
 * @param  int|null  $precision
 * @return int|null
 * @since  3.0
 */
function number_compare(int|float $number1, int|float $number2, int $precision = null): int|null
{
    return Numbers::compare($number1, $number2, $precision);
}

/**
 * Check whether given numbers are equal.
 *
 * @param  int|float $number1
 * @param  int|float $number2
 * @param  int|null  $precision
 * @return bool|null
 * @since  3.0
 */
function number_equals(int|float $number1, int|float $number2, int $precision = null): bool|null
{
    return Numbers::equals($number1, $number2, $precision);
}

/**
 * Check whether given input is digit.
 *
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_digit($input): bool
{
    return Numbers::isDigit($input);
}

/**
 * Check whether given input is an ID (useful for any (db) incremental id checking).
 *
 * anyparam  number $input
 * @return bool
 * @since  3.0
 */
function is_id($input): bool
{
    return Numbers::isId($input);
}

/**
 * Check whether given input is uint.
 *
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_uint($input): bool
{
    return Numbers::isUInt($input);
}

/**
 * Check whether given input is ufloat.
 *
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_ufloat($input): bool
{
    return Numbers::isUFloat($input);
}

/**
 * Check whether given input is signed.
 *
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_signed($input): bool
{
    return Numbers::isSigned($input);
}

/**
 * Check whether given input is unsigned.
 *
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_unsigned($input): bool
{
    return Numbers::isUnsigned($input);
}
