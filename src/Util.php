<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

use froq\common\interface\{Lengthable, Sizable, Arrayable, Listable, Stringable};

/**
 * Utility class.
 *
 * @package froq\util
 * @class   froq\util\Util
 * @author  Kerem Güneş
 * @since   1.0
 * @static
 */
final /* fuckic static */ class Util extends \StaticClass
{
    /**
     * Load sugar(s).
     *
     * @param  string    $name
     * @param  string ...$names
     * @return void
     * @throws froq\util\UtilException
     */
    public static function loadSugar(string $name, string ...$names): void
    {
        foreach ([$name, ...$names] as $name) {
            $file = sprintf(__DIR__ . '/sugars/%s.php', $name);
            if (is_file($file)) {
                require_once $file;
                continue;
            }

            // Not exists.
            $names = xglob(__DIR__ . '/sugars/{*.php,extra/*.php}', GLOB_BRACE)
                ->map(fn(string $file): string => (
                    strsrc($file, 'extra/') ? 'extra/' . filename($file) : filename($file)
                ))->toArray();

            throw new UtilException('Invalid sugar name %q [valids: %A]', [$name, $names]);
        }
    }

    /**
     * Get client ip.
     *
     * @return string|null
     */
    public static function getClientIp(): string|null
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
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
     * Get client agent.
     *
     * @param  bool $safe
     * @return string|null
     */
    public static function getClientUa(bool $safe = true): string|null
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
                return $safe ? substr($_SERVER[$name], 0, 255) : $_SERVER[$name];
            }
        }

        return null;
    }

    /**
     * Get current url.
     *
     * @param  bool $withQuery
     * @return string|null
     */
    public static function getCurrentUrl(bool $withQuery = true): string|null
    {
        @['REQUEST_SCHEME' => $scheme, 'REQUEST_URI' => $uri,
          'SERVER_NAME'    => $host,   'SERVER_PORT' => $port] = $_SERVER;

        if (!$scheme || !$host) {
            return null;
        }

        $url = $scheme . '://';
        if ($port && !(
            ((int) $port === 80 && $scheme === 'http') ||
            ((int) $port === 443 && $scheme === 'https')
        )) {
            $url .= $host . ':' . $port;
        } else {
            $url .= $host;
        }

        $colon = str_contains($uri, ':');

        // Fix parse_url()'s fail with ":" character.
        $colon && $uri = str_replace(':', '%3A', $uri);

        $uri .= '';

        // PHP thinks it's a host, not path (also gives false if URI kinda "//").
        if (($i = strpos($uri, '//')) === 0) {
            while (($uri[++$i] ?? '') === '/');

            $uri = '/' . substr($uri, $i);
            $tmp = parse_url($uri) ?: [];

            // Yes, I'am maniac, give them back..
            $tmp['path'] = str_repeat('/', $i - 1) . ($tmp['path'] ?? '');
        } else {
            $tmp = parse_url($uri) ?: [];
        }

        $tmp['path'] ??= '/';

        $colon && $tmp['path'] = str_replace('%3A', ':', $tmp['path']);

        $url .= $tmp['path'];
        if ($withQuery && ($query = $tmp['query'] ?? '') !== '') {
            $url .= '?' . $query;
        }

        return $url;
    }

    /**
     * Convert integer bytes to human-readable text.
     *
     * @param  int $bytes
     * @param  int $precision
     * @return string
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $base  = 1024;
        $units = ['B', 'KB', 'MB', 'GB'];

        $i = 0;
        while ($bytes > $base) {
            $i++;
            $bytes /= $base;
        }

        return round($bytes, $precision) . $units[$i];
    }

    /**
     * Convert human-readable text to integer bytes.
     *
     * @param  string $bytes
     * @return int
     */
    public static function convertBytes(string $bytes): int
    {
        $base  = 1024;
        $units = ['', 'K', 'M', 'G'];

        // Eg: 6.4M or 6.4MB => 6.4MB, 64M or 64MB => 64MB.
        if (sscanf($bytes, '%f%c', $byte, $unit) === 2) {
            $exp = array_search(strtoupper($unit), $units);

            return (int) ($byte * pow($base, $exp));
        }

        return (int) $bytes;
    }

    /**
     * Get size of given var.
     *
     * Note: This methos must NOT be used for the related methods of interfaces below;
     *
     * Stringable, froq\common\interface\{Lengthable, Sizable, Arrayable, Listable, Stringable}.
     *
     * @param  mixed $var
     * @return int
     */
    public static function sizeOf(mixed $var): int
    {
        // Speed up, a bit..
        if ($var === null || $var === '' || $var === []) {
            return 0;
        }

        switch (true) {
            // Easy parts.
            case is_string($var):
                return mb_strlen($var);
            case is_countable($var):
                return count($var);

            // Interfaces.
            case ($var instanceof Lengthable):
                return $var->length();
            case ($var instanceof Sizable):
                return $var->size();
            case ($var instanceof Arrayable):
                return count($var->toArray());
            case ($var instanceof Listable):
                return count($var->toList());
            case ($var instanceof Stringable):
                return mb_strlen($var->toString());
            case ($var instanceof \Stringable):
                return mb_strlen((string) $var);

            // Last tries.
            default:
                // Cannot traverse an already closed generator.
                if (is_iterable($var) && !$var instanceof \Generator) {
                    return iterator_count($var);
                }
                if (is_object($var)) {
                    return count(get_object_vars($var));
                }
                return 0;
        }
    }

    /**
     * Make (or just ensure) a closure from given callable, and bind it to the given object.
     *
     * @param  callable    $func
     * @param  object|null $bind
     * @return Closure
     */
    public static function makeClosure(callable $func, object $bind = null): \Closure
    {
        // $func instanceof \Closure || $func = \Closure::fromCallable($func); // Oldie.
        $func = $func(...); // @see https://php.net/manual/en/functions.first_class_callable_syntax

        if ($bind) {
            $func =@ $func->bindTo($bind, $bind) // If fails (eg: makeClosure("upper")).
                  ?? $func->bindTo($bind);
        }

        return $func;
    }

    /**
     * Make an array with given data input.
     *
     * Note: This method must be used for arrays, iterables and stdClass or public var'ed objects.
     *
     * @param  array|object|null $data
     * @param  bool              $deep
     * @return array
     */
    public static function makeArray(array|object|null $data, bool $deep = true): array
    {
        // Memoize maker function.
        static $make; $make ??= function ($data, $deep) use (&$make): array {
            if ($data) {
                if ($data instanceof Arrayable) {
                    $data = $data->toArray();
                } elseif ($data instanceof \Traversable) {
                    $data = iterator_to_array($data);
                }

                if ($deep) {
                    $isArray = is_array($data);

                    foreach ($data as $key => $value) {
                        $value = is_array($value) || is_object($value)
                            ? $make($value, true) : $value;

                        if ($isArray) {
                            $data[$key] = $value;
                        } else {
                            $data->$key = $value;
                        }
                    }
                }
            }

            return (array) $data;
        };

        return $make($data, $deep);
    }

    /**
     * Make an object with given data input.
     *
     * Note: This method must be used for arrays, iterables and stdClass or public var'ed objects.
     *
     * @param  array|object|null $data
     * @param  bool              $deep
     * @return object
     */
    public static function makeObject(array|object|null $data, bool $deep = true): object
    {
        // Memoize maker function.
        static $make; $make ??= function ($data, $deep) use (&$make): object {
            if ($data) {
                if ($data instanceof Arrayable) {
                    $data = $data->toArray();
                } elseif ($data instanceof \Traversable) {
                    $data = iterator_to_array($data);
                }

                if ($deep) {
                    $isArray = is_array($data);

                    foreach ($data as $key => $value) {
                        $value = is_array($value) || is_object($value)
                            ? $make($value, true) : $value;

                        if ($isArray) {
                            $data[$key] = $value;
                        } else {
                            $data->$key = $value;
                        }
                    }
                }
            }

            return (object) $data;
        };

        return $make($data, $deep);
    }
}
