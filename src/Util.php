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
     * @param  any $input
     * @param  bool $classes
     * @param  bool $scalars
     * @return string
     * @since  4.0
     */
    public static function getType($input, bool $classes = false, bool $scalars = false): string
    {
        $type = gettype($input);

        if ($classes && $type == 'object') {
            $class = get_class($input);
            // Return 'object' for silly stdClass stuff.
            return ($class != 'stdClass') ? $class : 'object';
        }

        static $scalarsArray = ['int', 'float', 'string', 'bool'];
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
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return end($ips);
        }

        // Possible header names.
        static $names = [
            'HTTP_CLIENT_IP',   'HTTP_X_REAL_IP',
            'REMOTE_ADDR_REAL', 'REMOTE_ADDR'
        ];

        foreach ($names as $name) {
            if (isset($_SERVER[$name])) {
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
            if (isset($_SERVER[$name])) {
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

        $url .= $tmp['path'] ?? '/';
        if ($withQuery && ($query = $tmp['query'] ?? '') !== '') {
            $url .= '?'. $query;
        }

        return $url;
    }

    /**
     * Parse query string (without changing dotted param keys [https://github.com/php/php-src/blob/master/main/php_variables.c#L103]).
     * @param  string      $input
     * @param  bool        $encode
     * @param  string|null $ignoredKeys
     * @return array
     */
    public static function parseQueryString(string $input, bool $encode = false,
        string $ignoredKeys = null): array
    {
        $ret = [];

        if ($input == '') {
            return $ret;
        }

        $hexed = false;
        if (strpos($input, '.')) {
            $hexed = true;

            // Normalize arrays.
            if (strpos($input, '%5D')) {
                $input = str_replace(['%5B', '%5D'], ['[', ']'], $input);
            }

            // Hex keys.
            $input = preg_replace_callback('~(^|(?<=&))[^=&\[]+~', function($match) {
                return bin2hex($match[0]);
            }, $input);
        }

        // Preserve pluses (otherwise parse_str() will replace all with spaces).
        if ($encode) {
            $input = str_replace('+', '%2B', $input);
        }

        parse_str($input, $parsedInput);

        if ($hexed) {
            // Unhex keys.
            foreach ($parsedInput as $key => $value) {
                $ret[hex2bin((string) $key)] = $value;
            }
        } else {
            $ret = $parsedInput;
        }

        if ($ignoredKeys != '') {
            $ignoredKeys = explode(',', $ignoredKeys);
            foreach ($ret as $key => $_) {
                if (in_array($key, $ignoredKeys)) {
                    unset($ret[$key]);
                }
            }
        }

        return $ret;
    }

    /**
     * Build query string.
     * @param  array       $input
     * @param  bool        $decode
     * @param  string|null $ignoredKeys
     * @param  bool        $stripTags
     * @param  bool        $normalizeArrays
     * @return string
     */
    public static function buildQueryString(array $input, bool $decode = false,
        string $ignoredKeys = null, bool $stripTags = true, bool $normalizeArrays = true): string
    {
        if ($ignoredKeys != '') {
            $ignoredKeys = explode(',', $ignoredKeys);
            foreach ($input as $key => $_) {
                if (in_array($key, $ignoredKeys)) {
                    unset($input[$key]);
                }
            }
        }

        // Memoize: fix skipped NULL values by http_build_query().
        static $filter; if (!$filter) {
               $filter = function ($input) use (&$filter) {
                    foreach ($input as $key => $value) {
                        $input[$key] = is_array($value) ? $filter($value) : strval($value);
                    }
                    return $input;
               };
        }

        $ret = http_build_query($filter($input));

        if ($decode) {
            $ret = urldecode($ret);
        }
        if ($stripTags && strpos($ret, '%3C')) {
            $ret = preg_replace('~%3C[\w]+(%2F)?%3E~ismU', '', $ret);
        }
        if ($normalizeArrays && strpos($ret, '%5D')) {
            $ret = preg_replace('~([\w\.\-]+)%5B([\w\.\-]+)%5D(=)?~iU', '\1[\2]\3', $ret);
        }

        return trim($ret);
    }
}
