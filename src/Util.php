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

namespace Froq\Util;

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Util
 * @author     Kerem Güneş <k-gun@mail.com>
 * @since      1.0
 */
final /* static */ class Util
{
    // @wait
    // public static function setEnv(string $key, $value) {}

    /**
     * Get env.
     * @param  string $key
     * @param  any    $valueDefault
     * @return any
     */
    public static function getEnv(string $key, $valueDefault = null)
    {
        $value = $valueDefault;
        if (isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            $value = $_ENV[$key];
        } elseif (false === ($value = getenv($key))) {
            $value = $valueDefault;
        } elseif (function_exists('apache_getenv') && false === ($value = apache_getenv($key))) {
            $value = $valueDefault;
        }

        return $value;
    }

    /**
     * Get client ip.
     * @return ?string
     */
    public static function getClientIp(): ?string
    {
        $ip = null;
        if (null != ($ip = self::getEnv('HTTP_X_FORWARDED_FOR'))) {
            if (false !== strpos($ip, ',')) {
                $ip = trim((string) end(explode(',', $ip)));
            }
        }
        // all ok
        elseif (null != ($ip = self::getEnv('HTTP_CLIENT_IP'))) {}
        elseif (null != ($ip = self::getEnv('HTTP_X_REAL_IP'))) {}
        elseif (null != ($ip = self::getEnv('REMOTE_ADDR_REAL'))) {}
        elseif (null != ($ip = self::getEnv('REMOTE_ADDR'))) {}

        return $ip;
    }

    /**
     * Get current url.
     * @param  bool $withQuery
     * @return string
     */
    public static function getCurrentUrl(bool $withQuery = true): string
    {
        static $filter;
        if ($filter == null) {
            $filter = function($input) {
                // encode quotes and html tags
                return html_encode(
                    // remove NUL-byte, ctrl-z, vertical tab
                    preg_replace('~[\x00\x1a\x0b]|%(?:00|1a|0b)~i', '', trim(
                        // slice at \n or \r
                        substr($input, 0, strcspn($input, "\n\r"))
                    ))
                );
            };
        }

        [$scheme, $host, $port, $path, $query] = [
            $_SERVER['REQUEST_SCHEME'], $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'],
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $_SERVER['QUERY_STRING'] ?? null
        ];

        $port = ($scheme != 'https' && $port != '80') ? ':'. $port : '';
        $path = $filter($path);

        $url = sprintf('%s://%s%s%s', $scheme, $host, $port, $path);
        if ($withQuery && $query !== null) {
            $url .= '?'. $filter($query);
        }

        return $url;
    }
}
