<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\Numbers;

/**
 * Make a number from given a numeric input.
 *
 * @param  int|float|string $input
 * @param  int|true         $precision
 * @return int|float
 * @since  3.0
 */
function number(int|float|string $input, int|true $precision = true): int|float
{
    return Numbers::convert($input, $precision);
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
 * @param  mixed $input
 * @return bool
 * @since  3.0
 */
function is_digit(mixed $input): bool
{
    return Numbers::isDigit($input);
}

/**
 * Check whether given input is an ID (useful for any (db) incremental id checking).
 *
 * @param  mixed $input
 * @return bool
 * @since  3.0
 */
function is_id(mixed $input): bool
{
    return Numbers::isId($input);
}

/**
 * Check whether given input is uint.
 *
 * @param  mixed $input
 * @return bool
 * @since  3.0
 */
function is_uint(mixed $input): bool
{
    return Numbers::isUInt($input);
}

/**
 * Check whether given input is ufloat.
 *
 * @param  mixed $input
 * @return bool
 * @since  3.0
 */
function is_ufloat(mixed $input): bool
{
    return Numbers::isUFloat($input);
}

/**
 * Check whether given input is signed.
 *
 * @param  mixed $input
 * @return bool
 * @since  3.0
 */
function is_signed(mixed $input): bool
{
    return Numbers::isSigned($input);
}

/**
 * Check whether given input is unsigned.
 *
 * @param  mixed $input
 * @return bool
 * @since  3.0
 */
function is_unsigned(mixed $input): bool
{
    return Numbers::isUnsigned($input);
}
