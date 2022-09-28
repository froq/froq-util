<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);


/**
 * Check whether current env is local.
 *
 * @return bool
 */
function is_local(): bool
{
    static $ret;
    return $ret ??= defined('__local__') && __local__;
}

/**
 * Check whether current env is CLI.
 *
 * @return bool
 */
function is_cli(): bool
{
    return (PHP_SAPI == 'cli');
}

/**
 * Check whether current env is CLI Server.
 *
 * @return bool
 */
function is_cli_server(): bool
{
    return (PHP_SAPI == 'cli-server');
}

/**
 * Check whether given variable is plain object.
 *
 * @param  mixed $var
 * @return bool
 */
function is_plain_object(mixed $var): bool
{
    return ($var && $var instanceof stdClass);
}

/**
 * Check whether given variable is array-like.
 *
 * @param  mixed $var
 * @return bool
 */
function is_array_like(mixed $var): bool
{
    return is_array($var) || is_plain_object($var);
}

/**
 * Check whether given variable is iterable-like.
 *
 * @param  mixed $var
 * @return bool
 */
function is_iterable_like(mixed $var): bool
{
    return is_iterable($var) || is_plain_object($var);
}

/**
 * Check whether given variable is closure.
 *
 * @param  mixed $var
 * @return bool
 * @since  3.0
 */
function is_closure(mixed $var): bool
{
    return ($var && $var instanceof Closure);
}

/**
 * Check whether given variable is between given min/max directives.
 *
 * @param  mixed            $var
 * @param  int|float|string $min
 * @param  int|float|string $max
 * @return bool
 * @since  3.0
 */
function is_between(mixed $var, int|float|string $min, int|float|string $max): bool
{
    return ($var >= $min && $var <= $max);
}

/**
 * Check whether given variable is nil (null).
 *
 * @param  mixed $var
 * @return bool
 * @since  4.0
 */
function is_nil(mixed $var): bool
{
    return ($var === nil);
}

/**
 * Check whether given variable is nils (null string).
 *
 * @param  mixed $var
 * @return bool
 * @since  4.0
 */
function is_nils(mixed $var): bool
{
    return ($var === nils);
}
