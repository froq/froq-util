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

/**
 * Int.
 * @param  any $input
 * @return int
 * @since  3.0
 */
function int($input): int
{
    return is_numeric($input) ? intval($input) : 0;
}

/**
 * Float.
 * @param  any $input
 * @return float
 * @since  3.0
 */
function float($input): float
{
    return is_numeric($input) ? floatval($input) : 0.0;
}

/**
 * String.
 * @param  any $input
 * @return string
 * @since  3.0
 */
function string($input): string
{
    return strval($input);
}

/**
 * Bool.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function bool($input): bool
{
    $input .= ''; // to string
    return ($input === '1' || $input === '0') ? boolval($input) : false;
}
