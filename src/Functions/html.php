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
 * Html encode.
 * @param  ?string $input
 * @param  bool    $doSimple
 * @return string
 */
function html_encode(?string $input, bool $doSimple = false): string
{
    return $doSimple
        ? str_replace(['<', '>'], ['&lt;', '&gt;'], (string) $input)
        : str_replace(["'", '"', '<', '>'], ['&#39;', '&#34;', '&lt;', '&gt;'], (string) $input);
}

/**
 * Html decode.
 * @param  ?string $input
 * @param  bool    $doSimple
 * @return string
 */
function html_decode(?string $input, bool $doSimple = false): string
{
    return $doSimple
        ? str_ireplace(['&lt;', '&gt;'], ['<', '>'], (string) $input)
        : str_ireplace(['&#39;', '&#34;', '&lt;', '&gt;'], ["'", '"', '<', '>'], (string) $input);
}

/**
 * Html strip.
 * @param  ?string $input
 * @param  ?string $allowedTags
 * @param  bool    $doDecode
 * @return string
 */
function html_strip(?string $input, ?string $allowedTags = '', bool $doDecode = false): string
{
    if ($doDecode) {
        $input = html_decode($input, true);
    }

    if ($allowedTags != '') {
        $allowedTags = implode('', array_map(function ($tag) {
            return '<'. trim($tag, '<>') .'>';
        }, explode(',', $allowedTags)));
    }

    return strip_tags((string) $input, (string) $allowedTags);
}

/**
 * Html remove.
 * @param  ?string $input
 * @param  ?string $allowedTags
 * @param  bool    $doDecode
 * @return string
 */
function html_remove(?string $input, ?string $allowedTags = '', bool $doDecode = false): string
{
    if ($doDecode) {
        $input = html_decode($input, true);
    }

    if ($allowedTags != '') {
        $pattern = '~<(?!(?:'. str_replace(',', '|', $allowedTags) .')\b)(\w+)\b[^>]*/?>(?:.*?</\1>)?~is';
    } else {
        $pattern = '~<(\w+)\b[^>]*/?>(?:.*?</\1>)?~is';
    }

    return preg_replace($pattern, '', (string) $input);
}

/**
 * Html attributes.
 * @param  array $input
 * @return string
 */
function html_attributes(array $input): string
{
    $return = [];
    foreach ($input as $key => $value) {
        $return[] = sprintf('%s="%s"', $key, $value);
    }

    return join(' ', $return);
}

/**
 * Html options.
 * @param  iterable     $input
 * @param  any          $keySearch
 * @param  string|array $extra
 * @return string
 */
function html_options(iterable $input, $keySearch = null, $extra = null): string
{
    $extra = is_array($extra) ? html_attributes($extra) : $extra;

    $return = '';
    foreach ($input as $key => $value) {
        $return .= sprintf('<option value="%s"%s%s>%s</option>', $key,
            html_selected($key, $keySearch), $extra, $value);
    }

    return $return;
}

/**
 * Html checked.
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
 * Html disabled.
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
 * Html selected.
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
 * Html compress.
 * @param  ?string $input
 * @return string
 */
function html_compress(?string $input): string
{
    $input = (string) $input;

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
