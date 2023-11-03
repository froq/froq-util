<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Perform a regular expression search returning a bool result.
 *
 * @param  string $pattern
 * @param  string $subject
 * @return bool
 * @since  4.0
 */
function preg_test(string $pattern, string $subject): bool
{
    $res = @preg_match($pattern, $subject);

    // Act as original.
    if ($res === false) {
        $message = preg_error_message(func: 'preg_match');
        trigger_error(sprintf('%s(): %s', __FUNCTION__, $message), E_USER_WARNING);
    }

    return (bool) $res;
}

/**
 * Perform a regular expression search & remove.
 *
 * @param  string|array $pattern
 * @param  string|array $subject
 * @param  int|null     $limit
 * @param  int|null     &$count
 * @return string|array|null
 * @since  4.0
 */
function preg_remove(string|array $pattern, string|array $subject, int $limit = null, int &$count = null): string|array|null
{
    if (is_string($pattern)) {
        $replace = '';
    } else {
        $replace = array_fill(0, count($pattern), '');
    }

    $res = @preg_replace($pattern, $replace, $subject, $limit ?? -1, $count);

    // Act as original.
    if ($res === null) {
        $message = preg_error_message(func: 'preg_replace');
        trigger_error(sprintf('%s(): %s', __FUNCTION__, $message), E_USER_WARNING);
    }

    return $res;
}

/**
 * Same as preg_match() but for only named capturing groups.
 *
 * @param  string     $pattern
 * @param  string     $subject
 * @param  array|null &$match
 * @param  int        $flags
 * @param  int        $offset
 * @return int|false
 * @since  6.0
 */
function preg_match_names(string $pattern, string $subject, array|null &$match, int $flags = 0, int $offset = 0): int|false
{
    $res = @preg_match($pattern, $subject, $match, $flags, $offset);

    // Act as original.
    if ($res === false) {
        $message = preg_error_message(func: 'preg_match');
        trigger_error(sprintf('%s(): %s', __FUNCTION__, $message), E_USER_WARNING);
    } else {
        // Select string (named) keys.
        $match = array_filter($match, 'is_string', 2);
    }

    return $res;
}

/**
 * Same as preg_match_all() but for only named capturing groups.
 *
 * @param  string     $pattern
 * @param  string     $subject
 * @param  array|null &$match
 * @param  int        $flags
 * @param  int        $offset
 * @return int|false
 * @since  6.0
 */
function preg_match_all_names(string $pattern, string $subject, array|null &$match, int $flags = 0, int $offset = 0): int|false
{
    $res = @preg_match_all($pattern, $subject, $match, $flags, $offset);

    // Act as original.
    if ($res === false) {
        $message = preg_error_message(func: 'preg_match');
        trigger_error(sprintf('%s(): %s', __FUNCTION__, $message), E_USER_WARNING);
    } else {
        // Select string (named) keys.
        $match = array_filter($match, 'is_string', 2);
    }

    return $res;
}
