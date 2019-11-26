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
 * Is local.
 * @return bool
 */
function is_local(): bool
{
    return (local === true);
}

/**
 * Is cli.
 * @return bool
 */
function is_cli(): bool
{
    return (PHP_SAPI === 'cli');
}

/**
 * Is cli server.
 * @return bool
 */
function is_cli_server(): bool
{
    return (PHP_SAPI === 'cli-server');
}

/**
 * Is plain object.
 * @param  any $input
 * @return bool
 */
function is_plain_object($input): bool
{
    return ($input instanceof stdClass);
}

/**
 * Is array like.
 * @param  any $input
 * @return bool
 */
function is_array_like($input): bool
{
    return is_array($input) || ($input instanceof stdClass);
}

/**
 * Is iter.
 * @param  any $input
 * @return bool
 */
function is_iter($input): bool
{
    return is_iterable($input) || ($input instanceof stdClass);
}

/**
 * Is primitive.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_primitive($input): bool
{
    return is_scalar($input);
}

/**
 * Is closure.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_closure($input): bool
{
    return ($input instanceof Closure);
}

/**
 * Is class.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_class($input): bool
{
    return is_string($input) && class_exists($input, false);
}

/**
 * Is class method.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function is_class_method($input): bool
{
    // Eg: Foo::bar (for publics only, seems not fixed @see https://bugs.php.net/bug.php?id=29210).
    return is_string($input) && strpos($input, '::') && is_callable($input);
}

/**
 * Is between.
 * @param  any $input
 * @param  any $minValue
 * @param  any $maxValue
 * @return bool
 * @since  3.0
 */
function is_between($input, $minValue, $maxValue): bool
{
    return ($input >= $minValue && $input <= $maxValue);
}

/**
 * Is true.
 * @param  any $input
 * @return bool
 * @since  3.5
 */
function is_true($input): bool
{
    return ($input === true);
}

/**
 * Is false.
 * @param  any $input
 * @return bool
 * @since  3.5
 */
function is_false($input): bool
{
    return ($input === false);
}
