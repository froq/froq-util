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

use froq\util\Strings;

/**
 * String contains.
 * @param  string   $source
 * @param  string   $search
 * @param  int|null offset
 * @param  bool     $caseSensitive
 * @return bool
 * @since  3.0
 */
function string_contains(string $source, string $search, int $offset = null,
    bool $caseSensitive = true): bool
{
    return Strings::contains($source, $search, $offset, $caseSensitive);
}

/**
 * String contains any.
 * @param  string   $source
 * @param  array    $search
 * @param  int|null $offset
 * @param  bool     $caseSensitive
 * @return bool
 * @since  3.0
 */
function string_contains_any(string $source, array $searches, int $offset = null,
    bool $caseSensitive = true): bool
{
    return Strings::containsAny($source, $searches, $offset, $caseSensitive);
}

/**
 * String contains all.
 * @param  string   $source
 * @param  array    $search
 * @param  int|null $offset
 * @param  bool     $caseSensitive
 * @return bool
 * @since  3.0
 */
function string_contains_all(string $source, array $searches, int $offset = null,
    bool $caseSensitive = true): bool
{
    return Strings::containsAll($source, $searches, $offset, $caseSensitive);
}

/**
 * String starts with.
 * @param  string $source
 * @param  string $search
 * @return bool
 * @since  3.0
 */
function string_starts_with(string $source, string $search): bool
{
    return Strings::startsWith($source, $search);
}

/**
 * String ends with.
 * @param  string $source
 * @param  string $search
 * @return bool
 * @since  3.0
 */
function string_ends_with(string $source, string $search): bool
{
    return Strings::endsWith($source, $search);
}
