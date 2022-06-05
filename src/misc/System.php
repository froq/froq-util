<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

/**
 * A static class for system related stuff.
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
            $value = format_number($value, $decimals, '.', '');
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
     * @return mixed|null
     */
    public static function iniGet(string $option, mixed $default = null, bool $bool = false): mixed
    {
        $value = ini_get($option);

        if ($value === '' || $value === false) {
            $value = $default;
        }

        if ($bool && !is_bool($value)) {
            $value = $value && equals(
                strtolower($value),
                '1', 'on', 'yes', 'true'
            );
        }

        if (is_string($value) && is_numeric($value)) {
            $value = str_contains($value, '.') ? (float) $value : (int) $value;
        }

        return $value;
    }

    /**
     * Set an ENV directive.
     *
     * @param string                     $option
     * @param bool                       $server
     * @param int|float|string|bool|null $value
     */
    public static function envSet(string $option, int|float|string|bool|null $value, bool $server = false): bool
    {
        $_ENV[$option] = $value;

        // Add it to server global.
        $server && $_SERVER[$option] = $value;

        if ($value === false) {
            $value = '0';
        } elseif (is_float($value)) {
            $decimals = 1;
            if ($remainds = strstr((string) $value, '.')) {
                $decimals = strlen($remainds) - 1;
            }
            $value = format_number($value, $decimals, '.', '');
        }

        return putenv($option .'='. $value);
    }

    /**
     * Get an ENV directive.
     *
     * @param  string     $option
     * @param  mixed|null $default
     * @param  bool       $server
     * @return mixed|null
     */
    public static function envGet(string $option, mixed $default = null, bool $server = true): mixed
    {
        $value = $_ENV[$option] ?? null;

        if ($value === null) {
            // Try with server global.
            $server && $value = $_SERVER[$option] ?? null;

            if ($value === null) {
                $value = getenv($option);
                if ($value === false) {
                    $value = null;
                }
            }
        }

        if (is_string($value) && is_numeric($value)) {
            $value = str_contains($value, '.') ? (float) $value : (int) $value;
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
     * Set/get default timezone.
     *
     * @param  string|null $id
     * @return string
     */
    public static function defaultTimezone(string $id = null): string
    {
        if ($id !== null) {
            date_default_timezone_set($id);
        }

        return date_default_timezone_get() ?: 'UTC';
    }
}
