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
    static $ret; return $ret ?? $ret = (
        defined('__local__') && (__local__ == true)
    );
}

/**
 * Is cli.
 * @return bool
 */
function is_cli(): bool
{
    return (PHP_SAPI == 'cli');
}

/**
 * Is cli server.
 * @return bool
 */
function is_cli_server(): bool
{
    return (PHP_SAPI == 'cli-server');
}

/**
 * Is plain object.
 * @param  any $in
 * @return bool
 */
function is_plain_object($in): bool
{
    return ($in instanceof stdClass);
}

/**
 * Is array like.
 * @param  any $in
 * @return bool
 */
function is_array_like($in): bool
{
    return is_array($in) || is_plain_object($in);
}

/**
 * Is iterable like.
 * @param  any $in
 * @return bool
 */
function is_iterable_like($in): bool
{
    return is_iterable($in) || is_plain_object($in);
}

/**
 * Is primitive.
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_primitive($in): bool
{
    return is_scalar($in);
}

/**
 * Is closure.
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_closure($in): bool
{
    return ($in instanceof Closure);
}

/**
 * Is class.
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_class($in): bool
{
    return is_string($in) && class_exists($in, false);
}

/**
 * Is class method.
 * @param  string|object $in
 * @param  string|null   $name
 * @return bool
 * @since  3.0
 */
function is_class_method($in, string $name = null): bool
{
    // Eg: Foo::bar (for publics only, seems not fixed @see https://bugs.php.net/bug.php?id=29210).
    if ($name === null) {
        return is_string($in) && strpos($in, '::') && is_callable($in);
    }

    return (is_string($in) || is_object($in)) && method_exists($in, $name);
}

/**
 * Is class property.
 * @param  string|object $in
 * @param  string        $name
 * @return bool
 * @since  3.0 ( actually)
 */
function is_class_property($in, string $name): bool
{
    return (is_string($in) || is_object($in)) && property_exists($in, $name);
}

/**
 * Is between.
 * @param  any $in
 * @param  any $min
 * @param  any $max
 * @return bool
 * @since  3.0
 */
function is_between($in, $min, $max): bool
{
    return ($in >= $min && $in <= $max);
}

/**
 * Is true.
 * @param  any $in
 * @return bool
 * @since  3.5
 */
function is_true($in): bool
{
    return ($in === true);
}

/**
 * Is false.
 * @param  any $in
 * @return bool
 * @since  3.5
 */
function is_false($in): bool
{
    return ($in === false);
}

/**
 * Is nil.
 * @param  any $in
 * @return bool
 * @since  4.0 Added back.
 */
function is_nil($in): bool
{
    return ($in === null);
}

/**
 * Is nils.
 * @param  any $in
 * @return bool
 * @since  4.0 Added back.
 */
function is_nils($in): bool
{
    return ($in === '');
}

/**
 * Is empty.
 * @param  any $in
 * @param  ... $ins
 * @return bool
 * @since  4.0 Added back.
 */
function is_empty($in, ...$ins): bool
{
    // Require at least one argument.
    if (empty($in)) {
        return true;
    }

    foreach ($ins as $in) {
        $in = is_object($in) ? get_object_vars($in) : $in;
        if (empty($in)) {
            return true;
        }
    }

    return false;
}
