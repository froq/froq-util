<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

/**
 * A static class for system related stuff.
 *
 * @package froq\util
 * @class   froq\util\System
 * @author  Kerem Güneş
 * @since   6.0
 * @static
 */
class System extends \StaticClass
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
            $value = '0';
        } elseif (is_float($value)) {
            $value = format_number($value, true, '.', '');
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
            $value = $value && equals(strtolower($value), '1', 'on', 'yes', 'true');
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
     * @param int|float|string|bool|null $value
     * @param bool                       $server For checking $_SERVER global.
     */
    public static function envSet(string $option, int|float|string|bool|null $value, bool $server = false): bool
    {
        $_ENV[$option] = $value;

        // Add it to server global.
        $server && $_SERVER[$option] = $value;

        if ($value === false) {
            $value = '0';
        } elseif (is_float($value)) {
            $value = format_number($value, true, '.', '');
        }

        return putenv($option . '=' . $value);
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

    /**
     * Check whether system is nix*-like.
     *
     * @return bool
     */
    public static function isUnix(): bool
    {
        return PATH_SEPARATOR === ':';
    }

    /**
     * Check whether system is win*-like.
     *
     * @return bool
     */
    public static function isWindows(): bool
    {
        return PATH_SEPARATOR === ';';
    }

    /**
     * Get OS info (best for Linux only).
     *
     * @return array
     */
    public static function osInfo(): array
    {
        $osf = strtolower(PHP_OS_FAMILY);
        if (str_starts_with($osf, 'win')) {
            return ['id' => 'windows', 'type' => 'Windows', 'name' => 'Windows'];
        }
        if (str_starts_with($osf, 'darwin')) {
            return ['id' => 'darwin', 'type' => 'Darwin', 'name' => 'Darwin'];
        }

        $res = self::exec('cat', '/etc/os-release', silent: true);

        return reduce((array) $res->result, function ($ret, $info) {
            $info = split('=', (string) $info, 2);

            if (isset($info[0])) {
                $key = lower($info[0]);
                $value = trim((string) $info[1], '"');
                $ret[$key] = $value;
            }

            return $ret;
        });
    }

    /**
     * Execute a command with/without arguments and options.
     *
     * @param  string         $program
     * @param  string|array   $arguments
     * @param  bool           $escape
     * @param  bool           $silent
     * @return object<?string, ?array, int, ?string>
     * @throws ArgumentError|Error
     */
    public static function exec(string $program, string|array $arguments = [], bool $escape = false, bool $silent = false): object
    {
        $arguments = (array) $arguments;

        if ($escape && $arguments) {
            $arguments = map($arguments, 'escapeshellarg');
        }

        $command = [
            $program,
            join(' ', $arguments),
            '2>&1' // Redirect stderr > stdout.
        ];

        $command = join(' ', filter(map($command, 'trim')));

        if (!$command) {
            throw new \ArgumentError('Empty exec command');
        }

        $return = exec($command, $result, $code);

        if ($code !== 0) {
            if (!$silent) {
                throw new \Error((string) $return, (int) $code);
            }

            // Use return as error & drop.
            [$error, $return, $result] = [$return, null, null];
        }

        return object(return: $return, result: $result, code: $code, error: $error ?? null);
    }

    /**
     * Read bytes from `/dev/urandom` device.
     *
     * @param  int $length
     * @return string|null
     * @throws ArgumentError|Error
     */
    public static function urandom(int $length): string|null
    {
        if ($length < 1) {
            throw new \ArgumentError('Argument $length must be greater than 0');
        }

        $ret = @file_get_contents('/dev/urandom', length: $length);

        if ($ret === false) {
            $error  = error_message(extract: true) ?: 'Unknown error';
            $search = 'Failed to open stream: ';

            if (strsrc($error, $search)) {
                $error = strsub($error, strlen($search));
            }

            throw new \Error('Cannot open /dev/urandom: ' . $error);
        }

        return $ret;
    }
}
