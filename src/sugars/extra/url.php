<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

use froq\util\Util;

/**
 * Get current URL (from server environment).
 *
 * @param  string|null $url
 * @return string|null
 * @since  4.0
 */
function get_url(): string|null
{
    static $ret;
    return $ret ??= Util::getCurrentUrl();
}

/**
 * Get scheme part from current or given URL.
 *
 * @param  string|null $url
 * @return string|null
 * @since  4.0
 */
function get_url_scheme(string $url = null): string|null
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_SCHEME) ?: null;
}

/**
 * Get host part from current or given URL.
 *
 * @param  string|null $url
 * @return string|null
 * @since  4.0
 */
function get_url_host(string $url = null): string|null
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_HOST) ?: null;
}

/**
 * Get port part from current or given URL.
 *
 * @param  string|null $url
 * @return int|null
 * @since  4.0
 */
function get_url_port(string $url = null): int|null
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_PORT) ?: null;
}

/**
 * Get path part from current or given URL.
 *
 * @param  string|null $url
 * @return string|null
 * @since  4.0
 */
function get_url_path(string $url = null): string|null
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_PATH) ?: null;
}

/**
 * Get query part from current or given URL.
 *
 * @param  string|null $url
 * @return string|null
 * @since  4.0
 */
function get_url_query(string $url = null): string|null
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_QUERY) ?: null;
}

/**
 * Get fragment part from current or given URL.
 *
 * @param  string|null $url
 * @return string|null
 * @since  4.0
 */
function get_url_fragment(string $url = null): string|null
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_FRAGMENT) ?: null;
}

/**
 * Get a segment by index from current or given URL.
 *
 * @param  int         $i
 * @param  string|null $url
 * @return string|null
 * @since  4.0
 */
function get_url_segment(int $i, string $url = null): string|null
{
    return (
        func_num_args() === 1
            ? get_url_segments()[$i] ?? null
            : get_url_segments($url)[$i] ?? null
    );
}

/**
 * Get all segments from current or given URL.
 *
 * @param  string|null $url
 * @return array|null
 * @since  4.0
 */
function get_url_segments(string $url = null): array|null
{
    $path = func_num_args() ? get_url_path($url) : get_url_path();

    if (!$path) {
        return null;
    }

    $ret = [];
    $tmp = preg_split('~/+~', $path, -1, 1);

    foreach ($tmp as $i => $cur) {
        // Push index next (skip 0), so provide a (1,2,3) like array.
        $ret[$i + 1] = $cur;
    }

    return $ret;
}
