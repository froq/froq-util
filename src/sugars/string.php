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
 * @param  string $in
 * @param  string $search
 * @param  bool   $case_insensitive
 * @return bool
 * @since  3.0
 */
function string_contains(string $in, string $search, bool $case_insensitive = false): bool
{
    return Strings::contains($in, $search, $case_insensitive);
}

/**
 * String contains any.
 * @param  string $in
 * @param  array  $search
 * @param  bool   $case_insensitive
 * @return bool
 * @since  3.0
 */
function string_contains_any(string $in, array $searches, bool $case_insensitive = false): bool
{
    return Strings::containsAny($in, $searches, $case_insensitive);
}

/**
 * String contains all.
 * @param  string $in
 * @param  array  $search
 * @param  bool   $case_insensitive
 * @return bool
 * @since  3.0
 */
function string_contains_all(string $in, array $searches, bool $case_insensitive = false): bool
{
    return Strings::containsAll($in, $searches, $case_insensitive);
}

/**
 * String starts with.
 * @param  string               $in
 * @param  string|array<string> $search
 * @return bool
 * @since  3.0
 */
function string_starts_with(string $in, $search, bool $case_insensitive = false,
    bool $multi_byte = false): bool
{
    return is_array($search) ? Strings::startsWithAny($in, $search, $case_insensitive, $multi_byte)
                             : Strings::startsWith($in, $search, $case_insensitive, $multi_byte);
}

/**
 * String ends with.
 * @param  string               $in
 * @param  string|array<string> $search
 * @return bool
 * @since  3.0
 */
function string_ends_with(string $in, $search, bool $case_insensitive = false,
    bool $multi_byte = false): bool
{
    return is_array($search) ? Strings::endsWithAny($in, $search, $case_insensitive, $multi_byte)
                             : Strings::endsWith($in, $search, $case_insensitive, $multi_byte);
}

/**
 * Is utf.
 * @param  string $in
 * @param  int     $bits
 * @return bool
 * @since  4.0
 */
function is_utf_string(string $in, int $bits = 8): bool
{
    return Strings::isUtf($in, $bits);
}

/**
 * Is ascii.
 * @param  string $in
 * @return bool
 * @since  4.0
 */
function is_ascii_string(string $in): bool
{
    return Strings::isAscii($in);
}

/**
 * Is binary.
 * @param  string $in
 * @return bool
 * @since  4.0
 */
function is_binary_string(string $in): bool
{
    return Strings::isBinary($in);
}

/**
 * Is base64.
 * @param  string $in
 * @return bool
 * @since  4.0
 */
function is_base64_string(string $in): bool
{
    return Strings::isBase64($in);
}
