<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;


/**
 * Util.
 *
 * @package froq\util
 * @object  froq\util\Util
 * @author  Kerem Güneş
 * @since   1.0
 * @static
 */
final /* fuckic static */ class Util extends \StaticClass
{
    /**
     * Load sugar.
     *
     * @param  string|array<string> $name
     * @return void
     * @throws froq\util\UtilException
     */
    public static function loadSugar(string|array $name): void
    {
        // Name list given.
        if (is_array($name)) {
            self::loadSugar($name);
            return;
        }

        $file = __dir__ . '/sugars/' . $name . '.php';

        if (!is_file($file)) {
            $files = glob(__dir__ . '/sugars/{*.php,extra/*.php}', GLOB_BRACE);
            $names = array_map(
                fn($file) => strsrc($file, 'extra/')
                    ? 'extra/'. pathinfo($file, PATHINFO_FILENAME)
                    : pathinfo($file, PATHINFO_FILENAME)
            , $files);

            throw new UtilException(
                'Invalid sugar name %s, valids are: %s',
                [$name, join(', ', $names)]
            );
        }

        include_once $file;
    }

    /**
     * Load sugars.
     *
     * @param  array<string> $names
     * @return void
     * @causes froq\util\UtilException
     */
    public static function loadSugars(array $names): void
    {
        foreach ($names as $name) {
            self::loadSugar($name);
        }
    }

    /**
     * Get client IP.
     *
     * @return string|null
     */
    public static function getClientIp(): string|null
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = split(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ips) {
                return end($ips);
            }
        }

        // Possible header names.
        static $names = [
            'HTTP_CLIENT_IP',   'HTTP_X_REAL_IP',
            'REMOTE_ADDR_REAL', 'REMOTE_ADDR'
        ];

        foreach ($names as $name) {
            // Not using isset(), cos variables may be set but empty.
            if (!empty($_SERVER[$name])) {
                return $_SERVER[$name];
            }
        }

        return null;
    }

    /**
     * Get client user agent.
     *
     * @param  bool $safe
     * @return string|null
     * @since  3.6
     */
    public static function getClientUserAgent(bool $safe = false): string|null
    {
        // Possible header names.
        static $names = [
            'HTTP_USER_AGENT',
            // Opera.
            'HTTP_X_OPERAMINI_PHONE_UA',
            // Vodafone.
            'HTTP_X_DEVICE_USER_AGENT', 'HTTP_X_ORIGINAL_USER_AGENT', 'HTTP_X_SKYFIRE_PHONE',
            'HTTP_X_BOLT_PHONE_UA',     'HTTP_DEVICE_STOCK_UA',       'HTTP_X_UCBROWSER_DEVICE_UA'
        ];

        foreach ($names as $name) {
            // Not using isset(), cos variables may be set but empty.
            if (!empty($_SERVER[$name])) {
                return !$safe ? $_SERVER[$name] : substr($_SERVER[$name], 0, 255);
            }
        }

        return null;
    }

    /**
     * Get current URL.
     *
     * @param  bool $withQuery
     * @return string|null
     */
    public static function getCurrentUrl(bool $withQuery = true): string|null
    {
        @ ['REQUEST_SCHEME' => $scheme, 'SERVER_NAME' => $host,
           'REQUEST_URI'    => $uri,    'SERVER_PORT' => $port]  = $_SERVER;

        if (!$scheme || !$host) {
            return null;
        }

        $url = $scheme . '://';
        if ($port && !(($port == '80' && $scheme == 'http') ||
                       ($port == '443' && $scheme == 'https'))) {
            $url .= $host . ':' . $port;
        } else {
            $url .= $host;
        }

        $colon = strpos($uri, ':');

        // Fix parse_url()'s fail with ":" character.
        if ($colon) {
            $uri = str_replace(':', '%3A', $uri);
        }

        $uri .= '';
        // PHP thinks it's a host, not path (also gives false if URI kinda "//").
        if (($i = strpos($uri, '//')) === 0) {
            while (($uri[++$i] ?? '') === '/');

            $uri = '/'. substr($uri, $i);
            $tmp = parse_url($uri) ?: [];

            // Yes, I'am maniac, give them back..
            $tmp['path'] = str_repeat('/', $i - 1) . ($tmp['path'] ?? '');
        } else {
            $tmp = parse_url($uri) ?: [];
        }

        $tmp['path'] ??= '/';
        if ($colon) {
            $tmp['path'] = str_replace('%3A', ':', $tmp['path']);
        }

        $url .= $tmp['path'];
        if ($withQuery && ($query = $tmp['query'] ?? '') !== '') {
            $url .= '?' . $query;
        }

        return $url;
    }

    /**
     * Build query string.
     *
     * @param  array  $data
     * @param  string $ignoredKeys
     * @param  bool   $removeTags
     * @return string
     */
    public static function buildQueryString(array $data, string $ignoredKeys = '', bool $removeTags = false): string
    {
        if (!$data) {
            return '';
        }

        // Drop ignored keys.
        if ($ignoredKeys != '') {
            $data = array_delete_key($data, ...explode(',', $ignoredKeys));
        }

        // Remove HTML tags.
        if ($removeTags) {
            $data = array_map_recursive(fn($value) => (
                is_string($value) ? preg_remove('~<(\w+)\b[^>]*/?>(?:.*?</\1>)?~isu', $value) : $value
            ), $data);
        }

        // Fix skipped nulls by http_build_query() & empty strings of falses.
        $data = array_map_recursive(fn($value) => (
            is_bool($value) ? intval($value) : strval($value)
        ), $data);

        $query = http_build_query($data, encoding_type: PHP_QUERY_RFC3986);

        // Normalize arrays.
        if (str_contains($query, '%5D=')) {
            $query = str_replace(['%5B', '%5D'], ['[', ']'], $query);
        }

        return $query;
    }

    /**
     * Parse query string (without changing dotted param keys).
     * https://github.com/php/php-src/blob/master/main/php_variables.c#L103
     *
     * @param  string $query
     * @param  string $ignoredKeys
     * @param  bool   $removeTags
     * @return array
     */
    public static function parseQueryString(string $query, string $ignoredKeys = '', bool $removeTags = false): array
    {
        $query = trim($query);
        if ($query == '') {
            return [];
        }

        $data = http_parse_query($query);

        // Drop ignored keys.
        if ($ignoredKeys != '') {
            $data = array_delete_key($data, ...explode(',', $ignoredKeys));
        }

        // Remove HTML tags.
        if ($removeTags) {
            $data = array_map_recursive(fn($value) => (
                is_string($value) ? preg_remove('~<(\w+)\b[^>]*/?>(?:.*?</\1>)?~isu', $value) : $value
            ), $data);
        }

        return $data;
    }

    /**
     * Make an array with given data input.
     * Note: must be used for arrays/iterables and stdClass or public var'ed objects.
     *
     * @param  array|object|null $data
     * @param  bool              $deep
     * @return array
     * @since  5.2
     */
    public static function makeArray(array|object|null $data, bool $deep = true): array
    {
        // Memoize maker function.
        static $make; $make ??= function ($data) use (&$make, $deep) {
            if ($data) {
                if ($data instanceof \Traversable) {
                    if ($data instanceof \Generator) {
                        // Prevent "Cannot rewind a generator that was already run" error.
                        $data = (new \froq\collection\iterator\GeneratorIterator($data))
                            ->toArray();
                    } else {
                        // Rewind for keys after iteration.
                        $temp = iterator_to_array($data);
                        $data->rewind();
                        $data = $temp;
                        unset($temp);
                    }
                }

                if ($deep) {
                    $array = is_array($data);
                    foreach ($data as $key => $value) {
                        $value = is_array($value) || is_object($value) ? $make($value) : $value;
                        if ($array) {
                            $data[$key] = $value;
                        } else {
                            $data->$key = $value;
                        }
                    }
                }
            }

            return (array) $data;
        };

        return $make($data);
    }

    /**
     * Make an object with given data input.
     * Note: must be used for arrays/iterables and stdClass or public var'ed objects.
     *
     * @param  array|object|null $data
     * @param  bool              $deep
     * @return object
     * @since  5.2
     */
    public static function makeObject(array|object|null $data, bool $deep = true): object
    {
        // Memoize maker function.
        static $make; $make ??= function ($data) use (&$make, $deep) {
            if ($data) {
                if ($data instanceof \Traversable) {
                    if ($data instanceof \Generator) {
                        // Prevent "Cannot rewind a generator that was already run" error.
                        $data = (new \froq\collection\iterator\GeneratorIterator($data))
                            ->toArray();
                    } else {
                        // Rewind for keys after iteration.
                        $temp = iterator_to_array($data);
                        $data->rewind();
                        $data = $temp;
                        unset($temp);
                    }
                }

                if ($deep) {
                    $array = is_array($data);
                    foreach ($data as $key => $value) {
                        $value = is_array($value) || is_object($value) ? $make($value) : $value;
                        if ($array) {
                            $data[$key] = $value;
                        } else {
                            $data->$key = $value;
                        }
                    }
                }
            }

            return (object) $data;
        };

        return $make($data);
    }
}
