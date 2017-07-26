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

/**
 * Encode.
 * @param  string|array $input
 * @return string|array
 */
function html_encode($input)
{
    if (is_array($input)) {
        return array_map('html_encode', $input);
    }

    $input = trim((string) $input);
    if ($input) {
        $input = str_replace(
            ["'"    , '"'    , '<'   , '>'],
            ['&#39;', '&#34;', '&lt;', '&gt;'],
            $input
        );
    }

    return $input;
}

/**
 * Decode.
 * @param  string|array $input
 * @return string|array
 */
function html_decode($input)
{
    if (is_array($input)) {
        return array_map('html_decode', $input);
    }

    $input = trim((string) $input);
    if ($input) {
        $input = str_ireplace(
            ['&#39;', '&#34;', '&lt;', '&gt;'],
            ["'"    , '"'    , '<'   , '>'],
            $input
        );
    }

    return $input;
}

/**
 * Strip.
 * @param  string|array $input
 * @param  bool         $decode
 * @return string|array
 */
function html_strip($input, bool $decode = false)
{
    if (is_array($input)) {
        return array_map('html_strip', $input);
    }

    if ($decode) {
        $input = html_decode($input);
    }

    return strip_tags((string) $input);
}

/**
 * Remove.
 * @param  string|array $input
 * @param  bool         $decode
 * @return string|array
 */
function html_remove($input = null, bool $decode = false)
{
    if (is_array($input)) {
        return array_map('html_remove', $input);
    }

    if ($decode) {
        $input = html_decode($input);
    }

    return preg_replace('~<([^>]+)>(.*?)</([^>]+)>|<([^>]+)/?>~', '', $input);
}

/**
 * Options.
 * @param  iterable $input
 * @param  any      $keySearch
 * @param  string   $extra
 * @return string
 */
function html_options(iterable $input, $keySearch = null, string $extra = ''): string
{
    $return = '';
    foreach ($input as $key => $value) {
        $return .= sprintf('<option value="%s"%s%s>%s</option>', $key,
            html_selected($key, $keySearch), $extra, $value);
    }

    return $return;
}

/**
 * Checked.
 * @param  any  $a
 * @param  any  $b
 * @param  bool $strict
 * @return string
 */
function html_checked($a, $b, bool $strict = false): string
{
    if ($a !== null) {
        return !$strict ? ($a == $b ? ' checked' : '') : ($a === $b ? ' checked' : '');
    }

    return '';
}

/**
 * Disabled.
 * @param  any  $a
 * @param  any  $b
 * @param  bool $strict
 * @return string
 */
function html_disabled($a, $b, bool $strict = false): string
{
    if ($a !== null) {
        return !$strict ? ($a == $b ? ' disabled' : '') : ($a === $b ? ' disabled' : '');
    }

    return '';
}

/**
 * Selected.
 * @param  any  $a
 * @param  any  $b
 * @param  bool $strict
 * @return string
 */
function html_selected($a, $b, bool $strict = false): string
{
    if ($a !== null) {
        return !$strict ? ($a == $b ? ' selected' : '') : ($a === $b ? ' selected' : '');
    }

    return '';
}

/**
 * Compress.
 * @param  string $input
 * @return string
 */
function html_compress(?string $input): string
{
    if (empty($input)) {
        return $input;
    }

    // scripts
    $input = preg_replace_callback('~(<script>(.*?)</script>)~sm', function($match) {
        $input = trim($match[2]);
        // line comments (protect http:// etc)
        if (is_local()) {
            $input = preg_replace('~(^|[^:])//([^\r\n]+)$~sm', '', $input);
        } else {
            $input = preg_replace('~(^|[^:])//.*?[\r\n]$~sm', '', $input);
        }

        // doc comments
        preg_match_all('~\s*/[\*]+(?:.*?)[\*]/\s*~sm', $input, $matchAll);
        foreach ($matchAll as $key => $value) {
            $input = str_replace($value, "\n\n", $input);
        }

        return sprintf('<script>%s</script>', trim($input));
    }, $input);

    // remove comments
    $input = preg_replace('~<!--[^-]\s*(.*?)\s*[^-]-->~sm', '', $input);
    // remove tabs
    $input = preg_replace('~^[\t ]+~sm', '', $input);
    // remove tag spaces
    $input = preg_replace('~>\s+<(/?)([\w\d-]+)~sm', '><\\1\\2', $input);

    // textarea \n problem
    $textareaTpl = '%{{{TEXTAREA}}}';
    $textareaCount = preg_match_all(
        '~(<textarea(.*?)>(.*?)</textarea>)~sm', $input, $matchAll);

    // fix textareas
    if ($textareaCount) {
        foreach ($matchAll[0] as $match) {
            $input = str_replace($match, $textareaTpl, $input);
        }
    }

    // reduce white spaces
    $input = preg_replace('~\s+~', ' ', $input);

    // fix textareas
    if ($textareaCount) {
        foreach ($matchAll[0] as $match) {
            $input = preg_replace("~{$textareaTpl}~", $match, $input, 1);
        }
    }

    return trim($input);
}
