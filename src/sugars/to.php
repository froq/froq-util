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

/**
 * To array.
 * @param  array|object $in
 * @param  bool         $deep
 * @return array
 */
function to_array($in, bool $deep = true): array
{
    if ($in && is_object($in)) {
        $out = (array) (
            is_iterable($in) ? iterator_to_array($in) : (
                method_exists($in, 'toArray') ? $in->toArray() : (
                    get_object_vars($in)
                )
            )
        );
    } else {
        $out = (array) $in;
    }

    if ($deep) {
        foreach ($out as $key => $value) {
            $out[$key] = is_iterable($value) || is_object($input)
                ? to_array($value, true) : $value;
        }
    }

    return $out;
}

/**
 * To object.
 * @param  array|object $in
 * @param  bool         $deep
 * @return object
 */
function to_object($in, bool $deep = true): object
{
    if ($in && is_object($in)) {
        $out = (object) (
            is_iterable($in) ? iterator_to_array($in) : (
                method_exists($in, 'toArray') ? $in->toArray() : (
                    get_object_vars($in)
                )
            )
        );
    } else {
        $out = (object) $in;
    }

    if ($deep) {
        foreach ($out as $key => $value) {
            $out->{$key} = is_iterable($value) || is_object($input)
                ? to_object($value, true) : $value;
        }
    }

    return $out;
}

/**
 * To closure.
 * @param  string   $func
 * @param  int|null $argc
 * @return Closure
 */
function to_closure(string $func, int $argc = null): Closure
{
    return function (...$args) use ($func, $argc) {
        if ($argc != null) {
            $args = array_slice($args, 0, $argc);
        }
        return $func(...$args);
    };
}
