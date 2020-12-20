<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);


/**
 * Is local.
 * @return bool
 */
function is_local(): bool
{
    static $ret;
    return $ret ??= !!constant('__local__');
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
        // Also check empty objects.
        $in = is_object($in) ? get_object_vars($in) : $in;
        if (empty($in)) {
            return true;
        }
    }

    return false;
}
