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

use froq\util\Util;

/**
 * Get url (gets current URL).
 *
 * @param  string|null $url
 * @return ?string
 * @since  4.0
 */
function get_url(): ?string
{
    static $url; return (
        $url ?? $url = Util::getCurrentUrl()
    );
}

/**
 * Get url scheme (gets scheme part from current or given URL).
 *
 * @param  string|null $url
 * @return ?string
 * @since  4.0
 */
function get_url_scheme(string $url = null): ?string
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_SCHEME) ?: null;
}

/**
 * Get url host (gets host part from current or given URL).
 *
 * @param  string|null $url
 * @return ?string
 * @since  4.0
 */
function get_url_host(string $url = null): ?string
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_HOST) ?: null;
}

/**
 * Get url port (gets port part from current or given URL).
 *
 * @param  string|null $url
 * @return ?int
 * @since  4.0
 */
function get_url_port(string $url = null): ?int
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_PORT) ?: null;
}

/**
 * Get url path (gets path part from current or given URL).
 *
 * @param  string|null $url
 * @return ?string
 * @since  4.0
 */
function get_url_path(string $url = null): ?string
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_PATH) ?: null;
}

/**
 * Get url query (gets query part from current or given URL).
 *
 * @param  string|null $url
 * @return ?string
 * @since  4.0
 */
function get_url_query(string $url = null): ?string
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_QUERY) ?: null;
}

/**
 * Get url fragment (gets fragment part from current or given URL).
 *
 * @param  string|null $url
 * @return ?string
 * @since  4.0
 */
function get_url_fragment(string $url = null): ?string
{
    $url = ''. (func_num_args() ? $url : get_url());

    return parse_url($url, PHP_URL_FRAGMENT) ?: null;
}

/**
 * Get url segments (gets a segment by index from current or given URL).
 *
 * @param  string|null $url
 * @return ?string
 * @since  4.0
 */
function get_url_segment(int $i, string $url = null): ?string
{
    return get_url_segments($url)[$i] ?? null;
}

/**
 * Get url segments (gets all segments from current or given URL).
 *
 * @param  string|null $url
 * @return ?array
 * @since  4.0
 */
function get_url_segments(string $url = null): ?array
{
    $path = get_url_path($url);
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
