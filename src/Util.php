<?php
/**
 * Copyright (c) 2016 Kerem Güneş
 *    <k-gun@mail.com>
 *
 * GNU General Public License v3.0
 *    <http://www.gnu.org/licenses/gpl-3.0.txt>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Froq\Util;

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Util
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final class Util
{
    /**
     * Array getter with dot notation support for sub-array paths.
     *
     * @param  array      $array
     * @param  int|string $key (aka path)
     * @param  any        $valueDefault
     * @return any
     */
    final public static function arrayDig(array $array, $key, $valueDefault = null)
    {
        // direct access
        if (array_key_exists($key, $array)) {
            $value =& $array[$key];
        } else {
            // trace element path
            $value =& $array;
            foreach (explode('.', trim((string) $key)) as $key) {
                $value =& $value[$key];
            }
        }

        return ($value !== null) ? $value : $valueDefault;
    }

    /**
     * Array pick.
     *
     * @param  array      $array
     * @param  int|string $key
     * @param  any        $value
     * @return any
     */
    final public static function arrayPick(array &$array, $key, $value = null)
    {
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            unset($array[$key]);
        }

        return $value;
    }

    /**
     * Array filter with key.
     *
     * @param  array         $array
     * @param  callable|null $filter
     * @return array
     */
    final public static function arrayFilter(array $array, callable $filter = null): array
    {
        if (!$filter) {
            $filter = function($key, $value) { return ((bool) $value); };
        }

        $return = [];
        foreach ($array as $key => $value) {
            if ($filter($key, $value)) {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * Array exclude.
     *
     * @param  array  $array
     * @param  array  $keysExclude
     * @return array
     */
    final public static function arrayExclude(array $array, array $keysExclude): array
    {
        $return = [];
        foreach ($array as $key => $value) {
            if (!in_array($key, $keysExclude)) {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    // @wait
    final public static function setEnv(string $key, $value) {}

    /**
     * Get real env.
     *
     * @param  string $key
     * @param  any    $valueDefault
     * @return any
     */
    final public static function getEnv(string $key, $valueDefault = null) {
        if (isset($_SERVER[$key])) {
            $valueDefault = $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            $valueDefault = $_ENV[$key];
        } elseif (false !== ($value = getenv($key))) {
            $valueDefault = $value;
        }

        return $valueDefault;
    }

    /**
     * Get client IP.
     *
     * @return string
     */
    final public static function getClientIp(): string
    {
        $ip = '';
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
}
