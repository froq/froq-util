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

use Froq\Util\Exceptions\InvalidKeyException;

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Collection
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final class Collection
{
    /**
     * Dig (with dot notation support for sub-array paths).
     * @param  array      $array
     * @param  int|string $key (aka path)
     * @param  any        $valueDefault
     * @return any
     * @throws Froq\Util\Exceptions\InvalidKeyException
     */
    final public static function dig(array $array, $key, $valueDefault = null)
    {
        $keyType = gettype($key);
        if ($keyType != 'integer' && $keyType != 'string') {
            throw new InvalidKeyException("Key type must be int or string, {$keyType} given!");
        }

        // direct access
        if (array_key_exists($key, $array)) {
            $value =& $array[$key];
        } else {
            // path access
            $value =& $array;
            foreach (explode('.', trim((string) $key)) as $key) {
                $value =& $value[$key];
            }
        }

        return ($value !== null) ? $value : $valueDefault;
    }

    /**
     * Pick.
     * @param  array      $array
     * @param  int|string $key
     * @param  any        $value
     * @return any
     */
    final public static function pick(array &$array, $key, $value = null)
    {
        $keyType = gettype($key);
        if ($keyType != 'integer' && $keyType != 'string') {
            throw new InvalidKeyException("Key type must be int or string, {$keyType} given!");
        }

        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            // remove element
            unset($array[$key]);
        }

        return $value;
    }

    /**
     * Exclude.
     * @param  array $array
     * @param  array $keysExclude
     * @return array
     */
    final public static function exclude(array $array, array $keysExclude): array
    {
        $return = [];
        foreach ($array as $key => $value) {
            if (!in_array($key, $keysExclude)) {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * First.
     * @param  array $array
     * @return any
     */
    final public static function first(array $array)
    {
        return current($array);
    }

    /**
     * Last.
     * @param  array $array
     * @return any
     */
    final public static function last(array $array)
    {
        return end($array);
    }
}
