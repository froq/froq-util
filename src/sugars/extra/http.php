<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Util;

/**
 * Build a query string.
 *
 * @param  array       $query
 * @param  string|null $ignored_keys
 * @param  bool        $normalize_arrays
 * @return string
 * @since  4.0
 */
function build_query_string(array $query, string $ignored_keys = null, bool $normalize_arrays = true): string
{
    return Util::buildQueryString($query, false, $ignored_keys, false, $normalize_arrays);
}

/**
 * Parse a query string.
 *
 * @param  string      $query
 * @param  string|null $ignored_keys
 * @param  bool        $dotted
 * @return array
 * @since  4.0
 */
function parse_query_string(string $query, string $ignored_keys = null, bool $dotted = false): array
{
    return Util::parseQueryString($query, false, $ignored_keys, $dotted);
}

/**
 * Build a cookie.
 *
 * @param  string      $name
 * @param  string|null $value
 * @param  array|null  $options
 * @return string|null
 * @since  4.0
 */
function build_cookie(string $name, string|null $value, array $options = null): string|null
{
    if ($name == '') {
        trigger_error('No cookie name given');
        return null;
    }

    $cookie = ['name' => $name, 'value' => $value] + array_replace(
        array_pad_keys([], ['expires', 'path', 'domain', 'secure', 'httponly', 'samesite']),
        array_map_keys($options ?? [], 'strtolower')
    );

    extract($cookie);

    $ret = rawurlencode($name) . '=';

    if ($value === null || $value === '' || $expires < 0) {
        $ret .= sprintf('n/a; Expires=%s; Max-Age=0', gmdate('D, d M Y H:i:s \G\M\T', 0));
    } else {
        $ret .= rawurlencode($value);

        // Must be given in-seconds format.
        if ($expires !== null) {
            $ret .= sprintf('; Expires=%s; Max-Age=%d', gmdate('D, d M Y H:i:s \G\M\T', time() + $expires),
                $expires);
        }
    }

    $path     && $ret .= '; Path=' . $path;
    $domain   && $ret .= '; Domain=' . $domain;
    $secure   && $ret .= '; Secure';
    $httponly && $ret .= '; HttpOnly';
    $samesite && $ret .= '; SameSite=' . $samesite;

    return $ret;
}

/**
 * Parse a cookie (from a header line).
 *
 * @param  string $cookie
 * @return array
 * @since  4.0
 */
function parse_cookie(string $cookie): array
{
    $ret = [];

    foreach (split(';', $cookie) as $i => $component) {
        $component = trim($component);
        if ($component) {
            [$name, $value] = split('=', $component, 2);
            if ($i == 0) {
                $ret['name']  = isset($name)  ? rawurldecode($name)  : $name;
                $ret['value'] = isset($value) ? rawurldecode($value) : $value;
                continue;
            }

            $name = strtolower($name ?? '');
            if ($name) {
                switch ($name) {
                    case 'secure':   $value = true;               break;
                    case 'httponly': $value = true;               break;
                    case 'samesite': $value = strtolower($value); break;
                }
                $ret[$name] = $value;
            }
        }
    }

    return $ret;
}

/**
 * Build a URL.
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
