<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);


/**
 * Check whether current env is local.
 * @return bool
 */
function is_local(): bool
{
    static $ret;
    return $ret ??= !!constant('__local__');
}

/**
 * Check whether current env is CLI.
 *
 * @return bool
 */
function is_cli(): bool
{
    return (PHP_SAPI === 'cli');
}

/**
 * Check whether current env is CLI Server.
 *
 * @return bool
 */
function is_cli_server(): bool
{
    return (PHP_SAPI === 'cli-server');
}

/**
 * Check whether given input is a plain object.
 *
 * @param  any $in
 * @return bool
 */
function is_plain_object($in): bool
{
    return ($in instanceof stdClass);
}

/**
 * Check whether given input is array-like.
 *
 * @param  any $in
 * @return bool
 */
function is_array_like($in): bool
{
    return is_array($in) || is_plain_object($in);
}

/**
 * Check whether given input is iterable-like.
 *
 * @param  any $in
 * @return bool
 */
function is_iterable_like($in): bool
{
    return is_iterable($in) || is_plain_object($in);
}

/**
 * Check whether given input is a closure.
 *
 * @param  any $in
 * @return bool
 * @since  3.0
 */
function is_closure($in): bool
{
    return ($in instanceof Closure);
}

/**
 * Check whether given input is between given min/max directives.
 *
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
 * Check whether given input is true.
 *
 * @param  any $in
 * @return bool
 * @since  3.5
 */
function is_true($in): bool
{
    return ($in === true);
}

/**
 * Check whether given input is false.
 *
 * @param  any $in
 * @return bool
 * @since  3.5
 */
function is_false($in): bool
{
    return ($in === false);
}

/**
 * Check whether given input is nil (null).
 *
 * @param  any $in
 * @return bool
 * @since  4.0 Added back.
 */
function is_nil($in): bool
{
    return ($in === null);
}

/**
 * Check whether given input is nils (null string).
 *
 * @param  any $in
 * @return bool
 * @since  4.0 Added back.
 */
function is_nils($in): bool
{
    return ($in === '');
}

/**
 * Check whether all given inputs are not empty.
 *
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
        // Also check empty objects.
        $in = is_object($in) ? get_object_vars($in) : $in;
        if (empty($in)) {
            return true;
        }
    }

    return false;
}
