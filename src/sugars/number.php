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
 * Is number.
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_number($in): bool
{
    return Numbers::isNumber($in);
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
