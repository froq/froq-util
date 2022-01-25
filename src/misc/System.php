<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

/**
 * System.
 *
 * @package froq\util\misc
 * @object  froq\util\misc\System
 * @author  Kerem Güneş
 * @since   6.0
 * @static
 */
final class System extends \StaticClass
{
    /**
     * Set an INI directive.
     *
     * @param  string                     $option
     * @param  int|float|string|bool|null $value
     * @return string|null
     */
    public static function iniSet(string $option, int|float|string|bool|null $value): string|null
    {
        if ($value === false) {
            $value = 'off';
        } elseif (is_float($value)) {
            $decimals = 1;
            if ($remainds = strstr((string) $value, '.')) {
                $decimals = strlen($remainds) - 1;
            }
            $value = number_format($value, $decimals);
        }


        $oldValue = ini_set($option, $value);

        return ($oldValue !== false) ? $oldValue : null;
    }

    /**
     * Get an INI directive.
     *
     * @param  string     $option
     * @param  mixed|null $default
     * @param  bool       $bool
     * @return string|bool|null
     */
    public static function iniGet(string $option, mixed $default = null, bool $bool = false): string|bool|null
    {
        $value = ini_get($option);

        if ($value === '') {
            $value = $default;
        }

        if ($bool && !is_bool($value)) {
            $value = $value && equals(
                strtolower($value),
                '1', 'on', 'yes', 'true'
            );
        }

        return $value;
    }

    /**
     * Set an ENV directive.
     *
     * @param string                     $option
     * @param int|float|string|bool|null $value
     */
    public static function envSet(string $option, int|float|string|bool|null $value): bool
    {
        $_ENV[$option] = $value;

        if ($value === false) {
            $value = '0';
        } elseif (is_float($value)) {
            $decimals = 1;
            if ($remainds = strstr((string) $value, '.')) {
                $decimals = strlen($remainds) - 1;
            }
            $value = number_format($value, $decimals);
        }

        return putenv($option .'='. $value);
    }

    /**
     * Get an ENV directive.
     *
     * @param  string     $option
     * @param  mixed|null $default
     * @param  bool       $serverLookup
     * @return mixed|null
     */
    public static function envGet(string $option, mixed $default = null, bool $serverLookup = true): mixed
    {
        $value = $_ENV[$option] ?? null;

        if ($value === null) {
            // Try with server global.
            if ($serverLookup) {
                $value = $_SERVER[$option] ?? null;
            }

            if ($value === null) {
                // Try with getenv() (ini variable order issue).
                if (($value = getenv($option)) === false) {
                    $value = null;
                }
            }
        }

        return $value ?? $default;
    }

    /**
     * Get temporary directory.
     *
     * @return string
     */
    public static function temporaryDirectory(): string
    {
        return sys_get_temp_dir() ?: '/tmp';
    }

    /**
     * Set/Get default timezone.
     *
     * @param  string|null $timezone
     * @return string|null
     */
    public static function defaultTimezone(string $timezone = null): string|null
    {
        if (func_get_args()) {
            $old = date_default_timezone_get() ?: 'UTC';
            return date_default_timezone_set($timezone) ? $old : null;
        }

        return date_default_timezone_get() ?: 'UTC';
    }
}
