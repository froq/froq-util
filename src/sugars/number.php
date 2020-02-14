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
 * @param  numeric  $input
 * @param  int|null $decimals
 * @return int|float|null
 * @since  3.0
 */
function number($input, int $decimals = null)
{
    return Numbers::convert($input, $decimals);
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
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_number($input): bool
{
    return Numbers::isNumber($input);
}

/**
 * Is digit.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_digit($input): bool
{
    return Numbers::isDigit($input);
}

/**
 * Is id (useful for any (db) incremental id checking).
 * anyparam  number $input
 * @return bool
 * @since  3.0
 */
function is_id($input): bool
{
    return Numbers::isId($input);
}

/**
 * Is uint.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_uint($input): bool
{
    return Numbers::isUInt($input);
}

/**
 * Is ufloat.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_ufloat($input): bool
{
    return Numbers::isUFloat($input);
}

/**
 * Is signed.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_signed($input): bool
{
    return Numbers::isSigned($input);
}

/**
 * Is unsigned.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_unsigned($input): bool
{
    return Numbers::isUnsigned($input);
}
