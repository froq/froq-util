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

use froq\StaticClass;
use froq\exceptions\{InvalidKeyException, InvalidArgumentException};
use Closure;

/**
 * Arrays.
 * @package froq\util
 * @object  froq\util\Arrays
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 * @static
 */
final class Arrays extends StaticClass
{
    /**
     * Is sequential array.
     * @param  array $array
     * @return bool
     */
    public static function isSequentialArray(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            if (is_string($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Is associative array.
     * @param  array $array
     * @return bool
     */
    public static function isAssociativeArray(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            if (is_int($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set (with dot notation support for sub-array paths).
     * @param  array<int|string, any> &$array
     * @param  int|string              $key
     * @param  any                     $value
     * @return array<int|string, any>
     */
    public static function set(array &$array, $key, $value): array
    {
        self::keyCheck($key);

        if (array_key_exists($key, $array)) { // Direct access.
            $array[$key] = $value;
        } else {
            $keys = explode('.', (string) $key);
            if (count($keys) <= 1) { // Direct access.
                $array[$key] = $value;
            } else { // Path access (with dot notation).
                $current =& $array;

                foreach($keys as $key) {
                    if (isset($current[$key])) {
                        $current[$key] = (array) $current[$key];
                    }
                    $current =& $current[$key];
                }

                $current = $value;
                unset($current);
            }
        }

        return $array;
    }

    /**
     * Set all (with dot notation support for sub-array paths).
     * @param  array &$array
     * @param  array  $items
     * @return array
     * @since  4.0
     */
    public static function setAll(array &$array, array $items): array
    {
        foreach ($items as $key => $value) {
            self::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Get (with dot notation support for sub-array paths).
     * @param  array<int|string, any> &$array
     * @param  int|string              $key AKA path.
     * @param  any|null                $valueDefault
     * @param  bool                    $drop @internal
     * @return any|null
     */
    public static function get(array &$array, $key, $valueDefault = null, bool $drop = false)
    {
        self::keyCheck($key);

        if (empty($array)) {
            return $valueDefault;
        }

        if (array_key_exists($key, $array)) {
            $value = $array[$key] ?? $valueDefault;

            // Drop gotten item.
            if ($drop) {
                unset($array[$key]);
            }
        } else {
            $keys = explode('.', (string) $key);
            $key = array_shift($keys);

            if (empty($keys)) {
                $value = $array[$key] ?? $valueDefault;

                // Drop gotten item.
                if ($drop) {
                    unset($array[$key]);
                }
            } elseif (isset($array[$key])) {
                // Dig more..
                $value = self::get($array[$key], implode('.', $keys), $valueDefault, $drop);
            }
        }

        return $value ?? $valueDefault;
    }

    /**
     * Get all (shortcuts like: list(..) = Arrays::getAll(..)).
     * @param  array<int|string, any> &$array
     * @param  array<int|string>       $keys AKA paths.
     * @param  any|null                $valueDefault
     * @param  bool                    $drop @internal
     * @return array
     */
    public static function getAll(array &$array, array $keys, $valueDefault = null, bool $drop = false): array
    {
        $values = [];

        foreach ($keys as $key) {
            if (is_array($key)) { // Default value given as array (eg: $keys=[['x',1], 'y']).
                @ [$_key, $_valueDefault] = $key;
                $values[] = self::get($array, $_key, $_valueDefault, $drop);
            } else {
                $values[] = self::get($array, $key, $valueDefault, $drop);
            }
        }

        return $values;
    }

    /**
     * Pull.
     * @param  array<int|string, any> &$array
     * @param  int|string              $key
     * @param  any|null                $valueDefault
     * @return any|null
     */
    public static function pull(array &$array, $key, $valueDefault = null)
    {
        return self::get($array, $key, $valueDefault, true);
    }

    /**
     * Pull all (shortcuts like: list(..) = Arrays::pullAll(..)).
     * @param  array<int|string, any> &$array
     * @param  array<int|string>       $keys
     * @param  any|null                $valueDefault
     * @return array
     */
    public static function pullAll(array &$array, array $keys, $valueDefault = null): array
    {
        return self::getAll($array, $keys, $valueDefault, true);
    }

    /**
     * Test (like JavaScript Array.some()).
     * @param  array    $array
     * @param  callable $func
     * @return bool
     */
    public static function test(array $array, callable $func): bool
    {
        foreach ($array as $key => $value) {
            if ($func($value, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Test all (like JavaScript Array.every()).
     * @param  array    $array
     * @param  callable $func
     * @return bool
     */
    public static function testAll(array $array, callable $func): bool
    {
        foreach ($array as $key => $value) {
            if (!$func($value, $key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Rand.
     * @param  array &$array
     * @param  int    $size
     * @param  bool   $pack Return as [key, value].
     * @param  bool   $drop Drop used items from array.
     * @return any|null
     * @throws froq\exceptions\InvalidArgumentException
     */
    public static function rand(array &$array, int $size = 1, bool $pack = false, bool $drop = false)
    {
        $count = count($array);
        if ($count == 0) {
            return null;
        }

        if ($size < 1) {
            throw new InvalidArgumentException(sprintf('Minimum size must be 1, %s given', $size));
        } elseif ($size > $count) {
            throw new InvalidArgumentException(sprintf('Maximum size must not be greater than %s, '.
                'given size %s is exceeding the size of items', $count, $size));
        }

        $keys = array_keys($array);
        shuffle($keys);

        $ret = [];
        while ($size--) {
            $key = $keys[$size];
            if (!$pack) {
                $ret[] = $array[$key];
            } else {
                $ret[$key] = $array[$key];
            }

            // Drop used item.
            if ($drop) {
                unset($array[$key]);
            }
        }

        if (count($ret) == 1) {
            $ret = !$pack ? current($ret) : [key($ret), current($ret)];
        }

        return $ret;
    }

    /**
     * Shuffle.
     * @param  array &$array
     * @param  bool   $keepKeys
     * @return array
     */
    public static function shuffle(array &$array, bool $keepKeys = false): array
    {
        if (!$keepKeys) {
            shuffle($array);
        } else {
            $keys = array_keys($array);
            shuffle($keys);

            $shuffledarray = [];
            foreach ($keys as $key) {
                $shuffledarray[$key] = $array[$key];
            }
            $array = $shuffledarray;

            // Nope.. (cos killing speed and also randomness).
            // uasort($array, function () {
            //     return rand(-1, 1);
            // });
        }

        return $array;
    }

    /**
     * Sort.
     * @param  array         &$array
     * @param  callable|null  $func
     * @param  callable|null  $ufunc
     * @param  int            $flags
     * @return array
     * @throws froq\exceptions\InvalidArgumentException
     */
    public static function sort(array &$array, callable $func = null, callable $ufunc = null,
        int $flags = 0): array
    {
        if ($func == null) {
            sort($array, $flags);
        } elseif ($func instanceof Closure) {
            usort($array, $func);
        } elseif (is_string($func)) {
            if ($func[0] == 'u' && $ufunc == null) {
                throw new InvalidArgumentException('Second argument must be callable when usort, '.
                    'uasort or uksort given');
            }

            $arguments = [&$array, $flags];
            if ($ufunc != null) {
                if (in_array($func, ['sort', 'asort', 'ksort'])) {
                    $func = 'u'. $func; // update to user function
                }
                $arguments[1] = $ufunc; // replace flags with ufunc
            }

            call_user_func_array($func, $arguments);
        }

        return $array;
    }

    /**
     * Include.
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    public static function include(array $array, array $keys): array
    {
        return array_filter($array, function($_, $key) use($keys) {
            return in_array($key, $keys, true);
        }, 1);
    }

    /**
     * Exclude.
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    public static function exclude(array $array, array $keys): array
    {
        return array_filter($array, function($_, $key) use($keys) {
            return !in_array($key, $keys, true);
        }, 1);
    }

    /**
     * First.
     * @param  array    $array
     * @param  any|null $valueDefault
     * @return any|null
     */
    public static function first(array $array, $valueDefault = null)
    {
        return (($key = array_key_first($array)) !== null) ?  $array[$key] : $valueDefault;
    }

    /**
     * Last.
     * @param  array    $array
     * @param  any|null $valueDefault
     * @return any|null
     */
    public static function last(array $array, $valueDefault = null)
    {
        return (($key = array_key_last($array)) !== null) ?  $array[$key] : $valueDefault;
    }

    /**
     * Get int.
     * @param  array      $array
     * @param  int|string $key
     * @param  int|null   $valueDefault
     * @return int
     */
    public static function getInt(array $array, $key, int $valueDefault = null): int
    {
        return (int) self::get($array, $key, $valueDefault);
    }

    /**
     * Get float.
     * @param  array      $array
     * @param  int|string $key
     * @param  float|null $valueDefault
     * @return float
     */
    public static function getFloat(array $array, $key, float $valueDefault = null): float
    {
        return (float) self::get($array, $key, $valueDefault);
    }

    /**
     * Get string.
     * @param  array       $array
     * @param  int|string  $key
     * @param  string|null $valueDefault
     * @return string
     */
    public static function getString(array $array, $key, string $valueDefault = null): string
    {
        return (string) self::get($array, $key, $valueDefault);
    }

    /**
     * Get bool.
     * @param  array      $array
     * @param  int|string $key
     * @param  bool|null  $valueDefault
     * @return bool
     */
    public static function getBool(array $array, $key, bool $valueDefault = null): bool
    {
        return (bool) self::get($array, $key, $valueDefault);
    }

    /**
     * Key check.
     * @param  int|string $key
     * @return void
     * @throws froq\exceptions\InvalidKeyException
     * @internal
     */
    private static function keyCheck($key): void
    {
        if (!is_int($key) && !is_string($key)) {
            throw new InvalidKeyException(sprintf('Arrays accept int and string keys only, '.
                '%s given', gettype($key)));
        }
    }
}
