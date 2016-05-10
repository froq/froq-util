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
    // @wait
    final public static function setEnv(string $key, $value) {}

    /**
     * Get real env.
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
