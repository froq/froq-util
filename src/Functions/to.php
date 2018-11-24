<?php
/**
 * Copyright (c) 2015 Kerem Güneş
 *
 * MIT License <https://opensource.org/licenses/mit>
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

use Froq\Util\Iter;
use stdClass as object;

/**
 * To iter.
 * @param  iter $input
 * @return Froq\Util\Iter
 */
function to_iter($input): Iter
{
    return new Iter($input);
}

/**
 * To array.
 * @param  array|object $input
 * @param  bool         $deep
 * @return array
 */
function to_array($input, bool $deep = true): array
{
    $input = (array) $input;
    if ($input && $deep) {
        foreach ($input as $key => $value) {
            $input[$key] = is_iter($value)
                ? to_array($value, $deep) : $value;
        }
    }

    return $input;
}

/**
 * To object.
 * @param  array|object $input
 * @param  bool         $deep
 * @return object
 */
function to_object($input, bool $deep = true): object
{
    $input = (object) $input;
    if ($input && $deep) {
        foreach ($input as $key => $value) {
            $input->{$key} = is_iter($value) && is_array_assoc($value)
                ? to_object($value, $deep) : $value;
        }
    }

    return $input;
}

/**
 * To snake from dash (Foo-Bar -> Foo_Bar | foo_bar).
 * @param  ?string $input
 * @param  bool    $doLower
 * @return string
 */
function to_snake_from_dash(?string $input, bool $doLower = true): string
{
    $input = str_replace('-', '_', (string) $input);

    if ($doLower) {
        $input = strtolower($input);
    }

    return $input;
}

/**
 * To snake from upper (fooBar | FooBar -> foo_bar)
 * @param  ?string $input
 * @param  bool    $doLower
 * @return string
 */
function to_snake_from_upper(?string $input, bool $doLower = true): string
{
    $input = (string) preg_replace_callback('~(?!^)([A-Z])~', function($match) {
        return '_'. $match[1];
    }, (string) $input);

    if ($doLower) {
        $input = strtolower($input);
    }

    return $input;
}

/**
 * To dash from upper (FooBar -> Foo-Bar | foo-bar).
 * @param  ?string $input
 * @param  bool    $doLower
 * @return string
 */
function to_dash_from_upper(?string $input, bool $doLower = true): string
{
    $input = (string) preg_replace_callback('~(?!^)([A-Z])~', function($match) {
        return '-'. $match[0];
    }, (string) $input);

    if ($doLower) {
        $input = strtolower($input);
    }

    return $input;
}

/**
 * To upper from dash (foo-bar | Foo-Bar -> FooBar)
 * @param  ?string $input
 * @return string
 */
function to_upper_from_dash(?string $input): string
{
    $input = (string) preg_replace_callback('~-([a-z])~i', function($match) {
        return ucfirst($match[1]);
    }, ucfirst((string) $input));

    return $input;
}

/**
 * To query string.
 * @param  array  $query
 * @param  string $ignoredKeys
 * @param  bool   $doStripTags
 * @param  bool   $doNormalizeArrays
 * @return string
 */
function to_query_string(array $query, string $ignoredKeys = '',
    bool $doStripTags = true, bool $doNormalizeArrays = true): string
{
    if ($ignoredKeys != '') {
        $ignoredKeys = explode(',', $ignoredKeys);

        foreach ($query as $key => $_) {
            if (in_array($key, $ignoredKeys)) {
                unset($query[$key]);
            }
        }
    }

    $query = http_build_query(
        // fix skipped NULL values by http_build_query()
        array_map(function ($value) {
            return strval($value);
        }, $query)
    );

    if ($doStripTags && false !== strpos($query, '%3C')) {
        $query = preg_replace('~%3C[\w]+(%2F)?%3E~simU', '', $query);
    }
    if ($doNormalizeArrays && false !== strpos($query, '%5D')) {
        $query = preg_replace('~%5B([\d]+)%5D~simU', '[]', $query);
        $query = preg_replace('~%5B([\w\.-]+)%5D~simU', '[\1]', $query);
    }

    return trim($query);
}
