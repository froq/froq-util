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
 * @param  int      $offset
 * @param  bool     $caseSensitive
 * @return bool
 * @since  3.0
 */
function string_contains(string $source, string $search, int $offset = 0,
    bool $caseSensitive = true): bool
{
    if ($offset) {
        $source = substr($source, $offset);
    }
    return Strings::contains($source, $search, $caseSensitive);
}

/**
 * String contains any.
 * @param  string   $source
 * @param  array    $search
 * @param  int      $offset
 * @param  bool     $caseSensitive
 * @return bool
 * @since  3.0
 */
function string_contains_any(string $source, array $searches, int $offset = 0,
    bool $caseSensitive = true): bool
{
    if ($offset) {
        $source = substr($source, $offset);
    }
    return Strings::containsAny($source, $searches, $caseSensitive);
}

/**
 * String contains all.
 * @param  string   $source
 * @param  array    $search
 * @param  int      $offset
 * @param  bool     $caseSensitive
 * @return bool
 * @since  3.0
 */
function string_contains_all(string $source, array $searches, int $offset = 0,
    bool $caseSensitive = true): bool
{
    if ($offset) {
        $source = substr($source, $offset);
    }
    return Strings::containsAll($source, $searches, $caseSensitive);
}

/**
 * String starts with.
 * @param  string       $source
 * @param  string|array $search
 * @return bool
 * @since  3.0
 */
function string_starts_with(string $source, $search): bool
{
    $searches = (array) $search;
    foreach ($searches as $search) {
        if (Strings::startsWith($source, $search)) {
            return true;
        }
    }
    return false;
}

/**
 * String ends with.
 * @param  string       $source
 * @param  string|array $search
 * @return bool
 * @since  3.0
 */
function string_ends_with(string $source, $search): bool
{
    $searches = (array) $search;
    foreach ($searches as $search) {
        if (Strings::endsWith($source, $search)) {
            return true;
        }
    }
    return false;
}
