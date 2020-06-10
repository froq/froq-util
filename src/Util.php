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

namespace froq\util;

use froq\common\objects\StaticClass;
use froq\util\UtilException;

/**
 * Util.
 * @package froq\util
 * @object  froq\util\Util
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 * @static
 */
final /* fuckic static */ class Util extends StaticClass
{
    /**
     * Load sugar.
     * @param  string $name
     * @return void
     */
    public static function loadSugar(string $name): void
    {
        $file = __dir__ .'/sugars/'. $name .'.php';

        if (!is_file($file)) {
            $files = glob(__dir__ .'/sugars/{*.php,extra/*.php}', GLOB_BRACE);
            $names = array_map(
                fn($file) => strpos($file, 'extra/')
                    ? 'extra/'. pathinfo($file, PATHINFO_FILENAME)
                    : pathinfo($file, PATHINFO_FILENAME)
            , $files);

            throw new UtilException('Invalid sugar name "%s" given, valids are: %s',
                [$name, join(', ', $names)]);
        }

        include_once $file;
    }

    /**
     * Load sugars.
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
     * Get type.
     * @param  any  $in
     * @param  bool $classes
     * @param  bool $scalars
     * @return string
     * @since  4.0
     */
    public static function getType($in, bool $classes = false, bool $scalars = false): string
    {
        $type = gettype($in);

        if ($classes && $type == 'object') {
            $class = get_class($in);
            // Return 'object' for silly stdClass stuff.
            return ($class != 'stdClass') ? $class : 'object';
        }

        static $scalarsArray   = ['int', 'float', 'string', 'bool'];
        static $translateArray = [
            'NULL'   => 'null',  'integer' => 'int',
            'double' => 'float', 'boolean' => 'bool'
        ];

        $ret = strtr($type, $translateArray);

        if ($scalars && in_array($ret, $scalarsArray)) {
            return 'scalar';
        }

        return $ret;
    }

    /**
     * Get client ip.
     * @return ?string
     */
    public static function getClientIp(): ?string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = array_filter(array_map('trim',
                explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
            if (!empty($ips)) {
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
     * @return ?string
     * @since  3.6
     */
    public static function getClientUserAgent(): ?string
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
                return $_SERVER[$name];
            }
        }

        return null;
    }

    /**
     * Get current url.
     * @param  bool $withQuery
     * @return ?string
     */
    public static function getCurrentUrl(bool $withQuery = true): ?string
    {
        @ ['REQUEST_SCHEME' => $scheme, 'SERVER_NAME' => $host,
           'REQUEST_URI'    => $uri,    'SERVER_PORT' => $port]  = $_SERVER;

        if (!$scheme || !$host) {
            return null;
        }

        $url = $scheme .'://';
        if ($port && !(($port == '80' && $scheme == 'http') ||
                       ($port == '443' && $scheme == 'https'))) {
            $url .= $host .':'. $port;
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
            $url .= '?'. $query;
        }

        return $url;
    }

    /**
     * Parse query string (without changing dotted param keys [https://github.com/php/php-src/blob/master/main/php_variables.c#L103]).
     * @param  string      $qs
     * @param  bool        $encode
     * @param  string|null $ignoredKeys
     * @return array
     */
    public static function parseQueryString(string $qs, bool $encode = false,
        string $ignoredKeys = null): array
    {
        $qa = [];

        if ($qs == '') {
            return $qa;
        }

        $hexed = false;
        if (strpos($qs, '.')) {
            $hexed = true;

            // Normalize arrays.
            if (strpos($qs, '%5D')) {
                $qs = str_replace(['%5B', '%5D'], ['[', ']'], $qs);
            }

            // Hex keys.
            $qs = preg_replace_callback('~(^|(?<=&))[^=&\[]+~', function($match) {
                return bin2hex($match[0]);
            }, $qs);
        }

        // Preserve pluses (otherwise parse_str() will replace all with spaces).
        if ($encode) {
            $qs = str_replace('+', '%2B', $qs);
        }

        parse_str($qs, $qsp);

        if ($hexed) {
            // Unhex keys.
            foreach ($qsp as $key => $value) {
                $qa[hex2bin((string) $key)] = $value;
            }
        } else {
            $qa = $qsp;
        }

        if ($ignoredKeys != '') {
            $ignoredKeys = explode(',', $ignoredKeys);
            foreach ($qa as $key => $_) {
                if (in_array($key, $ignoredKeys)) {
                    unset($qa[$key]);
                }
            }
        }

        return $qa;
    }

    /**
     * Build query string.
     * @param  array       $qa
     * @param  bool        $decode
     * @param  string|null $ignoredKeys
     * @param  bool        $stripTags
     * @param  bool        $normalizeArrays
     * @return string
     */
    public static function buildQueryString(array $qa, bool $decode = false,
        string $ignoredKeys = null, bool $stripTags = true, bool $normalizeArrays = true): string
    {
        if ($ignoredKeys != '') {
            $ignoredKeys = explode(',', $ignoredKeys);
            foreach (array_keys($qa) as $key) {
                if (in_array($key, $ignoredKeys)) {
                    unset($qa[$key]);
                }
            }
        }

        // Memoize: fix skipped NULL values by http_build_query().
        static $filter; if (!$filter) {
               $filter = function ($in) use (&$filter) {
                    foreach ($in as $key => $value) {
                        $in[$key] = is_array($value) ? $filter($value) : strval($value);
                    }
                    return $in;
               };
        }

        $qs = http_build_query($filter($qa));

        if ($decode) {
            $qs = urldecode($qs);
            // Fix such "=#foo" queries that not taken as parameter.
            $qs = str_replace('=#', '=%23', $qs);
        }
        if ($stripTags && strpos($qs, '%3C')) {
            $qs = preg_replace('~%3C[\w]+(%2F)?%3E~ismU', '', $qs);
        }
        if ($normalizeArrays && strpos($qs, '%5D')) {
            $qs = preg_replace('~([\w\.\-]+)%5B([\w\.\-]+)%5D(=)?~iU', '\1[\2]\3', $qs);
        }

        return trim($qs);
    }
}
