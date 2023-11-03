<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Default HTTP protocol.
 * @since 6.0
 */
const HTTP_DEFAULT_PROTOCOL = 'HTTP/1.1';

/**
 * Date format (https://tools.ietf.org/html/rfc7231#section-7.1.1.2).
 * @since 6.0
 */
const HTTP_DATE_FORMAT = 'D, d M Y H:i:s \G\M\T';

/**
 * Parse query.
 *
 * @param  string $query
 * @param  bool   $clean
 * @return array
 * @since  4.0, 6.0
 */
function http_parse_query_string(string $query, bool $clean = false): array
{
    $query = trim($query);
    if ($query === '') {
        return [];
    }

    $data = http_parse_query($query, '&', PHP_QUERY_RFC3986);

    // Remove HTML tags.
    if ($clean) {
        $data = array_apply($data, fn($v): mixed => (
            is_string($v) ? preg_remove('~<(\w+)\b[^>]*/?>(?:.*?</\1>)?~isu', $v) : $v
        ), recursive: true);
    }

    return $data;
}

/**
 * Build query.
 *
 * @param  array $data
 * @param  bool  $clean
 * @return string
 * @since  4.0, 6.0
 */
function http_build_query_string(array $data, bool $clean = false): string
{
    if (!$data) {
        return '';
    }

    // Remove HTML tags.
    if ($clean) {
        $data = array_apply($data, fn($v): mixed => (
            is_string($v) ? preg_remove('~<(\w+)\b[^>]*/?>(?:.*?</\1>)?~isu', $v) : $v
        ), recursive: true);
    }

    // Fix skipped nulls by http_build_query() & empty strings of falses.
    $data = array_apply($data, fn($v): int|string => is_bool($v) ? (int) $v : (string) $v, true);

    $query = http_build_query($data, '', '&', PHP_QUERY_RFC3986);

    // Normalize arrays.
    if (str_contains($query, '%5D=')) {
        $query = str_replace(['%5B', '%5D'], ['[', ']'], $query);
    }

    return $query;
}

/**
 * Parse URL.
 *
 * @param  string $url
 * @return array
 * @since  4.0, 6.0
 */
function http_parse_url(string $url): array
{
    $url = trim($url);
    if ($url === '') {
        return [];
    }

    $parsed_url = parse_url($url);
    if ($parsed_url === false) {
        // Try with fake sheme & host.
        if (str_starts_with($url, '//')) {
            $parsed_url = parse_url('scheme://host/' . ltrim($url, '/'));
            if ($parsed_url) {
                array_unset($parsed_url, 'scheme', 'host');
            }
        }
    }

    if (!$parsed_url) {
        return [];
    }

    $data = array_select($parsed_url, [
        'scheme', 'host', 'port', 'user', 'pass',
        'path', 'query', 'queryParams', 'fragment'
    ], combine: true);

    $origin = null;
    if (isset($data['scheme'], $data['host'])) {
        $origin = $data['scheme'] . '://' . $data['host'];
        if (isset($data['port'])) {
            $origin .= ':' . $data['port'];
        }
    }

    $authority = null;
    isset($data['user']) && $authority = $data['user'];
    isset($data['pass']) && $authority .= ':' . $data['pass'];

    // Separate.
    if ($authority !== null) {
        $authority .= '@';
    }

    isset($data['host']) && $authority .= $data['host'];
    isset($data['port']) && $authority .= ':' . $data['port'];

    // Reducing path slashes.
    if (isset($data['path'])) {
        $data['path'] = preg_replace('~(?<!:)/{2,}~', '/', $data['path']);
    }

    // Parse query params.
    if (isset($data['query'])) {
        $data['queryParams'] = http_parse_query_string($data['query']);
    }

    return ['source' => $url, 'origin' => $origin, 'authority' => $authority] + $data;
}

/**
 * Build URL.
 *
 * @param  array $data
 * @return string
 * @since  4.0, 6.0
 */
function http_build_url(array $data): string
{
    if (!$data) {
        return '';
    }

    $url = '';

    // Syntax: https://tools.ietf.org/html/rfc3986#section-3
    if (isset($data['scheme'])) {
        $url .= $data['scheme'] . '://';
    }

    if (isset($data['authority'])) {
        $url .= $data['authority'];
    } else {
        $authority = null;
        isset($data['user']) && $authority = $data['user'];
        isset($data['pass']) && $authority .= ':' . $data['pass'];

        // Separate.
        if ($authority !== null) {
            $authority .= '@';
        }

        isset($data['host']) && $authority .= $data['host'];
        isset($data['port']) && $authority .= ':' . $data['port'];

        $url .= $authority;
    }

    if (isset($data['queryParams'])) {
        if (isset($data['query'])) {
            // Update query with query params.
            $query = http_parse_query_string($data['query']);
            $query = array_replace_recursive($query, $data['queryParams']);
            $data['query'] = http_build_query_string($query);
        } else {
            $data['query'] = http_build_query_string($data['queryParams']);
        }
    }

    if (isset($data['query']) && is_array($data['query'])) {
        $data['query'] = http_build_query_string($data['query']);
    }

    isset($data['path'])     && $url .= $data['path'];
    isset($data['query'])    && $url .= '?' . $data['query'];
    isset($data['fragment']) && $url .= '#' . $data['fragment'];

    return $url;
}

/**
 * Parse cookie.
 *
 * @param  string $cookie
 * @return array
 * @since  4.0, 6.0
 */
function http_parse_cookie(string $cookie): array
{
    $cookie = trim($cookie);
    if ($cookie === '') {
        return [];
    }

    // Escape samesite field for split() below.
    $cookie = preg_replace('~samesite=(\w+); *(\w+)~i', 'samesite=\1%3B \2', $cookie);

    $data = [];

    foreach (split(';', $cookie) as $i => $component) {
        $component = trim($component);
        if ($component) {
            [$name, $value] = split('=', $component, 2);
            if ($i === 0) {
                $data['name'] = isset($name) ? rawurldecode($name) : $name;
                $data['value'] = isset($value) ? rawurldecode($value) : $value;
                continue;
            }

            $name = strtolower($name ?? '');

            // Skip invalid options.
            if (!$name || !in_array($name,
                ['expires', 'path', 'domain', 'secure', 'httponly', 'samesite', 'max-age'], true)) {
                continue;
            }

            switch ($name) {
                case 'secure':
                case 'httponly':
                    $data['options'][$name] = true;
                    break;
                case 'max-age':
                    $data['options'][$name] = intval($value ?? 0);
                    break;
                case 'samesite':
                    $data['options'][$name] = rawurldecode($value ?? '');
                    break;
                default:
                    $data['options'][$name] = $value;
            }
        }
    }

    return $data;
}

/**
 * Build cookie.
 *
 * @param  string      $name
 * @param  string|null $value
 * @param  array|null  $options
 * @return string
 * @since  4.0, 6.0
 */
function http_build_cookie(string $name, string|null $value, array $options = null): string
{
    $name = trim($name);
    if ($name === '') {
        trigger_error('Cookie name is empty');
        return '';
    }

    $options = array_replace(
        array_pad_keys([], ['expires', 'path', 'domain', 'secure', 'httponly', 'samesite']),
        array_map_keys('strtolower', (array) $options)
    );

    $cookie = rawurlencode($name) . '=';

    if ($value === '' || $value === null || $options['expires'] < 0) {
        $cookie .= sprintf('n/a; Expires=%s', http_date(0));
    } else {
        $cookie .= rawurlencode($value);

        // Must be given in-seconds format.
        if (isset($options['expires'])) {
            if (is_string($options['expires'])) {
                $expires = strtotime($options['expires']);
            } else {
                $expires = time() + $options['expires'];
            }

            $cookie .= sprintf('; Expires=%s', http_date($expires));
        }
    }

    isset($options['path'])     && $cookie .= '; Path=' . $options['path'];
    isset($options['domain'])   && $cookie .= '; Domain=' . $options['domain'];
    empty($options['secure'])   || $cookie .= '; Secure';
    empty($options['httponly']) || $cookie .= '; HttpOnly';
    isset($options['samesite']) && $cookie .= '; SameSite=' . $options['samesite'];

    return $cookie;
}

/**
 * Parse headers.
 *
 * @param  string     $headers
 * @param  string|int $case
 * @return array
 * @since  4.0, 6.0
 */
function http_parse_headers(string $headers, string|int $case = null): array
{
    $headers = trim($headers);
    if (!$headers) {
        return [];
    }

    $data = [];

    $first_line = null;

    // Pull request/status line.
    if (preg_test('~^([A-Z]+.*HTTP/[0-9]+|HTTP/[0-9]+.*)~', $headers)) {
        $first_line = substr($headers, 0, strpos($headers, "\r\n") ?: null);
    }

    if ($first_line !== null) {
        $data[0] = $first_line;
        $headers = substr($headers, strlen($first_line) + 1);
    }

    // // run(1.000)#1: 0.123914, mem: 1688 (1120288-1118600) @cancel
    // if (function_exists('iconv_mime_decode_headers___')) {
    //     $data = array_merge($data, iconv_mime_decode_headers($headers, 0, 'UTF-8') ?: []);
    // }
    // // run(1.000)#1: 0.029666, mem: 1736 (1071520-1069784)
    // else {
        foreach (explode("\r\n", $headers) as $header) {
            $temp = explode(':', $header, 2);
            if (!isset($temp[0])) {
                continue;
            }

            $name  = trim($temp[0]);
            $value = trim(preg_replace('~\s+~', ' ', $temp[1] ?? ''));

            // Handle multi-headers.
            if (isset($data[$name])) {
                $data[$name] = array_merge((array) $data[$name], [$value]);
            } else {
                $data[$name] = $value;
            }
        }
    // }

    // Apply case conversion.
    if ($case !== null) {
        $data = array_convert_keys($data, $case, '-');
    }

    return $data;
}

/**
 * Build headers.
 *
 * @param  array      $data
 * @param  string|int $case
 * @return string
 * @since  4.0, 6.0
 */
function http_build_headers(array $data, string|int $case = null): string
{
    if (!$data) {
        return '';
    }

    $headers = '';

    if (isset($data[0])) {
        // Pull request/status line.
        if (preg_test('~^([A-Z]+.+HTTP/[0-9]+|HTTP/[0-9]+.*)~', $data[0])) {
            $headers .= array_shift($data) . "\r\n";
        }
    }

    // Apply case conversion.
    if ($case !== null) {
        $data = array_convert_keys($data, $case, '-');
    }

    foreach ($data as $name => $value) {
        $name = (string) $name;
        if (is_array($value)) {
            foreach ($value as $value) {
                $headers .= http_build_header($name, $value) . "\r\n";
            }
        } else {
            $headers .= http_build_header($name, $value) . "\r\n";
        }
    }

    $headers = trim($headers);

    return $headers;
}

/**
 * Parse header.
 *
 * @param  string     $header
 * @param  string|int $case
 * @param  bool       $verbose
 * @return array
 * @since  6.0
 */
function http_parse_header(string $header, string|int $case = null, bool $verbose = false): array
{
    if (!function_exists('httpx_parse_header')) {
        require 'internal/httpx.php';
    }

    return httpx_parse_header($header, $case, $verbose);
}

/**
 * Build header.
 *
 * @param  string          $name
 * @param  string|null     $value
 * @param  string|int|null $case
 * @return string
 * @since  6.0
 */
function http_build_header(string $name, string|null $value, string|int $case = null): string
{
    $name = trim($name);
    if ($name === '') {
        return '';
    }

    // Apply case conversion.
    if ($case !== null) {
        $name = convert_case($name, $case, '-');
    }

    return $name . ': ' . $value;
}

/**
 * Parse request line.
 *
 * @param  string $line
 * @return array
 * @since  6.0
 */
function http_parse_request_line(string $line): array
{
    $line = trim($line);

    if ($line && sscanf($line, "%s %s %[^\r\n]", $method, $uri, $protocol) === 3) {
        $protocol = trim($protocol);
        $version  = (float) substr($protocol, 5, 3);

        return [
            'method' => $method, 'uri' => $uri,
            'protocol' => $protocol, 'version' => $version
        ];
    }

    return [];
}

/**
 * Parse response line.
 *
 * @param  string $line
 * @return array
 * @since  6.0
 */
function http_parse_response_line(string $line): array
{
    $line = trim($line);

    if ($line && sscanf($line, '%s %d', $protocol, $status) === 2) {
        $protocol = trim($protocol);
        $version  = (float) substr($protocol, 5, 3);

        return [
            'status' => $status,
            'protocol' => $protocol, 'version' => $version
        ];
    }

    return [];
}

/**
 * Get HTTP protocol.
 *
 * @return string
 * @since  5.0, 6.0
 */
function http_protocol(): string
{
    return $_SERVER['SERVER_PROTOCOL'] ?? HTTP_DEFAULT_PROTOCOL;
}

/**
 * Get HTTP version.
 *
 * @return string
 * @since  5.0, 6.0
 */
function http_version(): float
{
    return (float) substr(http_protocol(), 5, 3);
}

/**
 * Create an HTTP date.
 *
 * @param  int|string|null $time
 * @return string
 * @since  4.0, 6.0
 */
function http_date(int|string $time = null): string
{
    $time ??= time();
    if (is_string($time)) {
        $time = strtotime($time);
    }

    return gmdate(HTTP_DATE_FORMAT, $time);
}

/**
 * Verify an HTTP date.
 *
 * @param  int|string|null $time
 * @return string
 * @since  4.0, 6.0
 */
function http_date_verify(string $date): bool
{
    return ($d = date_create_from_format(HTTP_DATE_FORMAT, $date))
        && ($d->format(HTTP_DATE_FORMAT) === $date);
}

/**
 * Parse a HTTP query (string) as array.
 *
 * @param  string $query
 * @param  string $separator
 * @param  int    $decoding
 * @return array
 * @since  6.0
 */
function http_parse_query(string $query, string $separator = '&', int $decoding = PHP_QUERY_RFC3986): array
{
    $query = trim($query);
    if ($query === '') {
        return [];
    }

    $data = [];

    /** @thanks http://php.net/parse_str#119484 */
    foreach (explode($separator, $query) as $tmp) {
        @[$key, $value] = explode('=', $tmp, 2);

        $key = (string) $key;
        if ($key === '') {
            continue;
        }

        if ($decoding === PHP_QUERY_RFC3986) {
            $key   = rawurldecode($key);
            $value = rawurldecode($value ?? '');
        } else {
            // All others as PHP_QUERY_RFC1738.
            $key   = urldecode($key);
            $value = urldecode($value ?? '');
        }

        if (preg_match_all('~\[([^\]]*)\]~m', $key, $match)) {
            $key  = substr($key, 0, strpos($key, '['));
            $keys = [$key, ...$match[1]];
        } else {
            $keys = [$key];
        }

        $target = &$data;

        foreach ($keys as $index) {
            if ($index === '') {
                if (isset($target)) {
                    if (is_array($target)) {
                        $ikeys  = array_filter(array_keys($target), 'is_int');
                        $index  = $ikeys ? max($ikeys) + 1 : 0;
                    } else {
                        $index  = 1;
                        $target = [$target];
                    }
                } else {
                    $index  = 0;
                    $target = [];
                }
            } elseif (isset($target[$index]) && !is_array($target[$index])) {
                $target[$index] = [$target[$index]];
            }

            $target = &$target[$index];
        }

        if (is_array($target)) {
            $target[] = $value;
        } else {
            $target = $value;
        }
    }

    return $data;
}

/**
 * @alias http_build_url()
 * @since 4.0, 6.0
 */
function build_url(array $data): string
{
    return http_build_url($data);
}
