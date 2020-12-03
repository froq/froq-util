<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\Numbers;

/**
 * Number.
 * @param  numeric  $in
 * @param  int|null $decimals
 * @return int|float|null
 * @since  3.0
 */
function number($in, int $decimals = null)
{
    return Numbers::convert($in, $decimals);
}

/**
 * Number compare.
 * @param  string|number $a
 * @param  string|number $b
 * @param  int|null      $precision
 * @return ?int
 * @since  3.0
 */
function number_compare($a, $b, int $precision = null): ?int
{
    return Numbers::compare($a, $b, $precision);
}

/**
 * Number equals.
 * @param  string|number $a
 * @param  string|number $b
 * @param  int|null      $precision
 * @return ?bool
 * @since  3.0
 */
function number_equals($a, $b, int $precision = null): ?bool
{
    return Numbers::equals($a, $b, $precision);
}

/**
 * Is digit.
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_digit($in): bool
{
    return Numbers::isDigit($in);
}

/**
 * Is id (useful for any (db) incremental id checking).
 * anyparam  number $in
 * @return bool
 * @since  3.0
 */
function is_id($in): bool
{
    return Numbers::isId($in);
}

/**
 * Is uint.
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_uint($in): bool
{
    return Numbers::isUInt($in);
}

/**
 * Is ufloat.
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_ufloat($in): bool
{
    return Numbers::isUFloat($in);
}

/**
 * Is signed.
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_signed($in): bool
{
    return Numbers::isSigned($in);
}

/**
 * Is unsigned.
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_unsigned($in): bool
{
    return Numbers::isUnsigned($in);
}
