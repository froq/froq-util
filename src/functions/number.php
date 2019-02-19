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
 * @param  numeric $input
 * @return number|null
 * @since  3.0
 */
function number($input)
{
    if (is_numeric($input)) {
        return is_string($input) && strpos($input, '.') !== false
            ? (float) $input : (int) $input;
    }

    return null; // not numeric input
}

/**
 * Number compare.
 * @param  number   $a
 * @param  number   $b
 * @param  int|null $precision
 * @return ?int
 * @since  3.0
 */
function number_compare($a, $b, int $precision = null): ?int
{
    return Numbers::compare($a, $b, $precision);
}

/**
 * Number equals.
 * @param  number   $a
 * @param  number   $b
 * @param  int|null $precision
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
 * @param  any  $input
 * @param  bool $complex
 * @return bool
 * @since  3.0
 */
function is_digit($input, bool $complex = true): bool
{
    return Numbers::isDigit($input, $complex);
}

/**
 * Is id (useful for any (db) object id checking).
 * @param  number $input
 * @return bool
 * @since  3.0
 */
function is_id($input): bool
{
    return Numbers::isId($input);
}

/**
 * Is uint.
 * @param  number $input
 * @return bool
 * @since  3.0
 */
function is_uint($input): bool
{
    return Numbers::isUInt($input);
}

/**
 * Is ufloat.
 * @param  number $input
 * @return bool
 * @since  3.0
 */
function is_ufloat($input): bool
{
    return Numbers::isUFloat($input);
}

/**
 * Is signed.
 * @param  number $input
 * @return bool
 * @since  3.0
 */
function is_signed($input): bool
{
    return Numbers::isSigned($input);
}

/**
 * Is unsigned.
 * @param  number $input
 * @return bool
 * @since  3.0
 */
function is_unsigned($input): bool
{
    return Numbers::isUnsigned($input);
}

/**
 * Rand int.
 * @param  bool $signed
 * @return int
 */
function rand_int(bool $signed = false): int
{
    return ($ret = random_int(0, PHP_INT_MAX)) && $signed ? -$ret : $ret;
}

/**
 * Rand float.
 * @param  bool $signed
 * @return float
 */
function rand_float(bool $signed = false): float
{
    return ($ret = lcg_value()) && $signed ? -$ret : $ret;
}
