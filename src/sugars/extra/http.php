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
 * Parse query string.
 *
 * @param  string $query
 * @return array
 * @since  4.0
 */
function parse_query_string(string $query): array
{
    return Util::parseQueryString($query, false);
}

/**
 * Build query string.
 *
 * @param  array  $query
 * @param  bool   $normalize_arrays
 * @return string
 * @since  4.0
 */
function build_query_string(array $query, bool $normalize_arrays = true): string
{
    return Util::buildQueryString($query, false, null, false, $normalize_arrays);
}

/**
 * Parse cookie.
 *
 * @param  string $cookie
 * @return array
 * @since  4.0
 */
function parse_cookie(string $cookie): array
{
    $ret = [];

    foreach (explode(';', $cookie) as $i => $component) {
        $component = trim($component);
        if ($component) {
            @ [$name, $value] = explode('=', $component, 2);
            if ($i == 0) {
                $ret['name']  = ($name !== null) ? rawurldecode($name) : $name;
                $ret['value'] = ($value !== null) ? rawurldecode($value) : $value;
                continue;
            }

            $name = strtolower($name ?? '');
            if ($name) {
                switch ($name) {
                    case 'secure': $value = true; break;
                    case 'httponly': $name = 'httpOnly'; $value = true; break;
                    case 'samesite': $name  = 'sameSite'; $value = true; break;
                }
                $ret[$name] = $value;
            }
        }
    }

    return $ret;
}

/**
 * Build cookie.
 *
 * @param  array  $cookie
 * @return string
 * @since  4.0
 */
function build_cookie(array $cookie): string
{
    if (empty($cookie['name']) || !array_key_exists('value', $cookie)) {
        trigger_error('Both cookie name & value required');
        return '';
    }

    $ret = rawurlencode($cookie['name']) .'=';

    if ($cookie['value'] === null || $cookie['value'] === ''
        || (isset($cookie['expires']) && $cookie['expires'] < 0)) {
        $ret .= sprintf('NULL; Expires=%s; Max-Age=0', gmdate('D, d M Y H:i:s \G\M\T'));
    } else {
        // String, bool, int or float.
        switch (gettype($cookie['value'])) {
            case 'string':
                $ret .= rawurlencode($cookie['value']);
                break;
            case 'boolean':
                $ret .= $cookie['value'] ? 'true' : 'false';
                break;
            default:
                $ret .= strval($cookie['value']);
        }

        // Must be given in-seconds format.
        if (isset($cookie['expires'])) {
            $ret .= sprintf('; Expires=%s; Max-Age=%s', gmdate('D, d M Y H:i:s \G\M\T',
                time() + $cookie['expires']), $cookie['expires']);
        }
    }

    isset($cookie['path'])     && $ret .= '; Path='. $cookie['path'];
    isset($cookie['domain'])   && $ret .= '; Domain='. $cookie['domain'];
    isset($cookie['secure'])   && $ret .= '; Secure';
    isset($cookie['httpOnly']) && $ret .= '; HttpOnly';
    isset($cookie['sameSite']) && $ret .= '; SameSite='. $cookie['sameSite'];

    return $ret;
}

/**
 * Build url.
 *
 * @param  array $url
 * @return string
 * @since  4.0
 */
function build_url(array $url): string
{
    $authority = null;
    if (!isset($url['authority'])) {
        isset($url['user']) && $authority .= $url['user'];
        isset($url['pass']) && $authority .= ':'. $url['pass'];

        if ($authority) {
            $authority .= '@'; // Separate.
        }

        isset($url['host']) && $authority .= $url['host'];
        isset($url['port']) && $authority .= ':'. $url['port'];
    }

    $ret = '';

    // Syntax url: https://tools.ietf.org/html/rfc3986#section-3
    if (isset($url['scheme'])) {
        $ret .= $url['scheme'];
        $ret .= $authority ? '://'. $authority : ':';
    } elseif (isset($url['authority'])) {
        $ret .= $url['authority'];
    } elseif ($authority) {
        $ret .= $authority;
    }

    if (isset($url['queryParams'])) {
        $query = Util::buildQueryString($url['queryParams']);
    }

    isset($url['path'])     && $ret .= $url['path'];
    isset($url['query'])    && $ret .= '?'. $url['query'];
    isset($url['fragment']) && $ret .= '#'. $url['fragment'];

    return $ret;
}
