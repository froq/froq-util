<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;

use froq\util\UtilException;
use froq\common\object\StaticClass;

/**
 * Util.
 *
 * @package froq\util
 * @object  froq\util\Util
 * @author  Kerem Güneş
 * @since   1.0
 * @static
 */
final /* fuckic static */ class Util extends StaticClass
{
    /**
     * Load sugar.
     *
     * @param  string|array $name
     * @return void
     */
    public static function loadSugar(string|array $name): void
    {
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

            throw new UtilException('Invalid sugar name %s, valids are: %s', [$name, join(', ', $names)]);
        }

        include_once $file;
    }

    /**
     * Load sugars.
     *
     * @param  array<string> $names
     * @return void
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
            'HTTP_X_DEVICE_USER_AGENT',  'HTTP_X_ORIGINAL_USER_AGENT', 'HTTP_X_SKYFIRE_PHONE',
            'HTTP_X_BOLT_PHONE_UA',      'HTTP_DEVICE_STOCK_UA',       'HTTP_X_UCBROWSER_DEVICE_UA'
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
     * Parse query string (without changing dotted param keys).
     * https://github.com/php/php-src/blob/master/main/php_variables.c#L103
     *
     * @param  string      $qs
     * @param  bool        $encode
     * @param  string|null $ignoredKeys
     * @return array
     */
    public static function parseQueryString(string $qs, bool $encode = false, string $ignoredKeys = null): array
    {
        $qa = [];

        if ($qs == '') {
            return $qa;
        }

        $hexed = false;
        if (str_contains($qs, '.')) {
            $hexed = true;

            // Normalize arrays.
            if (str_contains($qs, '%5D=')) {
                $qs = str_replace(['%5B', '%5D'], ['[', ']'], $qs);
            }

            // Hex keys.
            $qs = preg_replace_callback('~(^|(?<=&))[^=&\[]+~', fn($m) => bin2hex($m[0]), $qs);
        }

        // Preserve pluses (otherwise parse_str() will replace all with spaces).
        if ($encode) {
            $qs = str_replace('+', '%2B', $qs);
        }

        parse_str($qs, $qsp);

        if ($hexed) {
            // Unhex keys.
            foreach ($qsp as $key => $value) {
                $key = hex2bin((string) $key);
                if (str_contains($key, '%')) {
                    $key = rawurldecode($key);
                }
                $qa[$key] = $value;
            }
        } else {
            $qa = $qsp;
        }

        if ($ignoredKeys != null) {
            $ignoredKeys = explode(',', $ignoredKeys);
            foreach (array_keys($qa) as $key) {
                if (in_array($key, $ignoredKeys)) {
                    unset($qa[$key]);
                }
            }
        }

        return $qa;
    }

    /**
     * Build query string.
     *
     * @param  array       $qa
     * @param  bool        $decode
     * @param  string|null $ignoredKeys
     * @param  bool        $stripTags
     * @param  bool        $normalizeArrays
     * @return string
     */
    public static function buildQueryString(array $qa, bool $decode = false, string $ignoredKeys = null,
        bool $stripTags = true, bool $normalizeArrays = true): string
    {
        if ($ignoredKeys != null) {
            $ignoredKeys = explode(',', $ignoredKeys);
            foreach (array_keys($qa) as $key) {
                if (in_array($key, $ignoredKeys)) {
                    unset($qa[$key]);
                }
            }
        }

        // Memoize: fix skipped NULL values by http_build_query().
        static $filter; $filter ??= function ($data) use (&$filter) {
            foreach ($data as $key => $value) {
                $data[$key] = is_array($value) ? $filter($value) : strval($value);
            }
            return $data;
        };

        $qs = http_build_query($filter($qa));

        if ($decode) {
            $qs = urldecode($qs);
            // Fix such "=#foo" queries that not taken as parameter.
            $qs = str_replace('=#', '=%23', $qs);
        }
        if ($stripTags && str_contains($qs, '%3C')) {
            $qs = preg_replace('~%3C[\w]+(%2F)?%3E~ismU', '', $qs);
        }
        if ($normalizeArrays && str_contains($qs, '%5D=')) {
            $qs = str_replace(['%5B', '%5D'], ['[', ']'], $qs);
        }

        return trim($qs);
    }
}
