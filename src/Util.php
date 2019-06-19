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

/**
 * Util.
 * @package froq\util
 * @object  froq\util\Util
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 */
final /* fuckic static */ class Util
{
    /**
     * Set env.
     * @param string $key
     * @param any    $value
     */
    public static function setEnv(string $key, $value): void
    {
        $_ENV[$key] = $value;
    }

    /**
     * Get env.
     * @param  string   $key
     * @param  any|null $valueDefault
     * @return any
     */
    public static function getEnv(string $key, $valueDefault = null)
    {
        // uppers for nginx
        $value = $_ENV[$key] ?? $_ENV[strtoupper($key)] ??
                 $_SERVER[$key] ?? $_SERVER[strtoupper($key)] ?? $valueDefault;

        if ($value === null) {
            if (false === ($value = getenv($key))) {
                $value = $valueDefault;
            } elseif (function_exists('apache_getenv') && false === ($value = apache_getenv($key))) {
                $value = $valueDefault;
            }
        }

        return $value;
    }

    /**
     * Get client ip.
     * @return ?string
     */
    public static function getClientIp(): ?string
    {
        if (null != ($ip = self::getEnv('HTTP_X_FORWARDED_FOR'))) {
            if (false !== ($i = strrpos($ip, ','))) {
                $ip = substr($ip, ($i + 1));
            }
            return $ip;
        }

        // header names
        static $names = [
            'HTTP_CLIENT_IP',   'HTTP_X_REAL_IP',
            'REMOTE_ADDR_REAL', 'REMOTE_ADDR'
        ];

        foreach ($names as $name) {
            if (null != ($value = self::getEnv($name))) {
                return $value;
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
        // header names
        static $names = [
            'HTTP_USER_AGENT',
            // opera
            'HTTP_X_OPERAMINI_PHONE_UA',
            // vodafone
            'HTTP_X_DEVICE_USER_AGENT',  'HTTP_X_ORIGINAL_USER_AGENT', 'HTTP_X_SKYFIRE_PHONE',
            'HTTP_X_BOLT_PHONE_UA',      'HTTP_DEVICE_STOCK_UA',       'HTTP_X_UCBROWSER_DEVICE_UA'
        ];

        foreach ($names as $name) {
            if (null != ($value = self::getEnv($name))) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Get current url.
     * @param  bool $withQuery
     * @return string
     */
    public static function getCurrentUrl(bool $withQuery = true): string
    {
        static $filter, $scheme, $host, $port;
        if ($filter == null) {
            $filter = function($input) {
                // decode first @important
                $input = rawurldecode($input);

                // encode quotes and html tags
                return html_encode(
                    // remove NUL-byte, ctrl-z, vertical tab
                    preg_replace('~[\x00\x1a\x0b]|%(?:00|1a|0b)~i', '', trim(
                        // slice at \n or \r
                        substr($input, 0, strcspn($input, "\n\r"))
                    ))
                );
            };

            [$scheme, $host, $port] = [
                $_SERVER['REQUEST_SCHEME'], $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']
            ];
        }


        $path = $_SERVER['REQUEST_URI'];
        $query = '';
        if (strpos($path, '?')) {
            [$path, $query] = explode('?', $path, 2);
        }

        $port = ($scheme != 'https' && $port != '80') ? ':'. $port : '';
        $path = $filter($path);
        if (strpos($path, '//') !== false) {
            $path = preg_replace('~/+~', '/', $path);
        }

        $url = sprintf('%s://%s%s%s', $scheme, $host, $port, $path);
        if ($withQuery && $query != '') {
            $url .= '?'. $filter($query);
        }

        return $url;
    }

    /**
     * Parse query string (without changing dotted param keys).
     * @param  string      $input
     * @param  bool        $encode
     * @param  string|null $ignoredKeys
     * @return array
     * @link   https://github.com/php/php-src/blob/master/main/php_variables.c#L99
     */
    public static function parseQueryString(string $input, bool $encode = false,
        string $ignoredKeys = null): array
    {
        $ret = [];

        if ($input == '') {
            return $ret;
        }

        // hex keys
        $hexed = false;
        if (strpos($input, '.')) {
            $hexed = true;

            // normalize arrays
            if (strpos($input, '%5D')) {
                $input = str_replace(['%5B', '%5D'], ['[', ']'], $input);
            }

            $input = preg_replace_callback('~(^|(?<=&))[^=&\[]+~', function($match) {
                return bin2hex($match[0]);
            }, $input);
        }

        // preserve pluses, so parse_str() will replace all with spaces
        if ($encode) {
            $input = str_replace('+', '%2B', $input);
        }

        parse_str($input, $input);

        // unhex keys
        if ($hexed) {
            foreach ($input as $key => $value) {
                $ret[hex2bin((string) $key)] = $value;
            }
        } else {
            $ret = $input;
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
     * Unparse query string.
     * @param  array       $input
     * @param  bool        $decode
     * @param  string|null $ignoredKeys
     * @param  bool        $stripTags
     * @param  bool        $normalizeArrays
     * @return string
     */
    public static function unparseQueryString(array $input, bool $decode = false,
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

        // fix skipped NULL values by http_build_query()
        static $filter; if ($filter == null) {
            $filter = function ($var) use (&$filter) {
                foreach ($var as $key => $value) {
                    $var[$key] = is_array($value) ? $filter($value) : strval($value);
                }
                return $var;
            };
        }

        $query = http_build_query($filter($input));
        if ($decode) {
            $query = urldecode($query);
        }
        if ($stripTags && strpos($query, '%3C')) {
            $query = preg_replace('~%3C[\w]+(%2F)?%3E~ismU', '', $query);
        }
        if ($normalizeArrays && strpos($query, '%5D')) {
            $query = preg_replace('~([\w\.\-]+)%5B([\w\.\-]+)%5D(=)?~iU', '\1[\2]\3', $query);
        }

        return trim($query);
    }
}
