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
 * To title case (eg: "foo bar" => "FooBar").
 *
 * @param  string      $in
 * @param  string|null $sep
 * @param  bool        $lower
 * @return string
 * @since  4.0
 */
function to_title_case(string $in, string $sep = null, bool $lower = true): string
{
    if ($lower) {
        $in = strtolower($in);
    }

    $sep ??= ' ';

    return implode('', array_map(fn($s) => ucfirst(trim($s)), explode($sep, $in)));
}

/**
 * To dash case (eg: "foo bar" => "foo-bar").
 *
 * @param  string      $in
 * @param  string|null $sep
 * @param  bool        $lower
 * @return string
 * @since  4.0
 */
function to_dash_case(string $in, string $sep = null, bool $lower = true): string
{
    if ($lower) {
        $in = strtolower($in);
    }

    $sep ??= ' ';

    return implode('-', array_map('trim', explode($sep, $in)));
}

/**
 * To camel case (eg: "foo bar" => "fooBar").
 *
 * @param  string      $in
 * @param  string|null $sep
 * @param  bool        $lower
 * @return string
 * @since  4.0
 */
function to_camel_case(string $in, string $sep = null, bool $lower = true): string
{
    if ($lower) {
        $in = strtolower($in);
    }

    $sep ??= ' ';

    return lcfirst(
        implode('', array_map(fn($s) => ucfirst(trim($s)), explode($sep, $in)))
    );
}

/**
 * To snake case (eg: "foo bar" => "foo_bar").
 *
 * @param  string      $in
 * @param  string|null $sep
 * @param  bool        $lower
 * @return string
 * @since  4.0
 */
function to_snake_case(string $in, string $sep = null, bool $lower = true): string
{
    if ($lower) {
        $in = strtolower($in);
    }

    $sep ??= ' ';

    return implode('_', array_map('trim', explode($sep, $in)));
}
