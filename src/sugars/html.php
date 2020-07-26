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
 * Html encode.
 * @param  ?string $in
 * @param  bool    $simple
 * @return string
 */
function html_encode(?string $in, bool $simple = false): string
{
    return $simple
        ? str_replace(['<', '>'], ['&lt;', '&gt;'], (string) $in)
        : str_replace(["'", '"', '<', '>'], ['&#39;', '&#34;', '&lt;', '&gt;'], (string) $in);
}

/**
 * Html decode.
 * @param  ?string $in
 * @param  bool    $simple
 * @return string
 */
function html_decode(?string $in, bool $simple = false): string
{
    return $simple
        ? str_ireplace(['&lt;', '&gt;'], ['<', '>'], (string) $in)
        : str_ireplace(['&#39;', '&#34;', '&lt;', '&gt;'], ["'", '"', '<', '>'], (string) $in);
}

/**
 * Html strip.
 * @param  ?string $in
 * @param  ?string $allowed_tags
 * @param  bool    $decode
 * @return string
 */
function html_strip(?string $in, ?string $allowed_tags = '', bool $decode = false): string
{
    if ($decode) {
        $in = html_decode($in, true);
    }

    if ($allowed_tags != '') {
        $allowed_tags = explode(',', $allowed_tags);
    }

    return strip_tags((string) $in, $allowed_tags);
}

/**
 * Html remove.
 * @param  ?string $in
 * @param  ?string $allowed_tags
 * @param  bool    $decode
 * @return string
 */
function html_remove(?string $in, ?string $allowed_tags = '', bool $decode = false): string
{
    if ($decode) {
        $in = html_decode($in, true);
    }

    if ($allowed_tags != '') {
        $pattern = '~<(?!(?:'. str_replace(',', '|', $allowed_tags) .')\b)(\w+)\b[^>]*/?>(?:.*?</\1>)?~is';
    } else {
        $pattern = '~<(\w+)\b[^>]*/?>(?:.*?</\1>)?~is';
    }

    return preg_replace($pattern, '', (string) $in);
}

/**
 * Html attributes.
 * @param  array $in
 * @return string
 */
function html_attributes(array $in): string
{
    $ret = [];
    foreach ($in as $name => $value) {
        $ret[] = sprintf('%s="%s"', $name, $value);
    }

    return join(' ', $ret);
}

/**
 * Html options.
 * @param  iterable     $in
 * @param  any          $value_current
 * @param  bool         $strict
 * @param  string|array $extra
 * @return string
 */
function html_options(iterable $in, $value_current = null, bool $strict = false, $extra = null): string
{
    if ($extra !== null) {
        if (is_array($extra)) {
            $extra = html_attributes($extra);
        }
        $extra = ' '. trim($extra);
    }

    $ret = '';
    foreach ($in as $value => $text) {
        if (is_array($text)) {
            @ [$value, $text] = $text;
        }
        $ret .= sprintf('<option value="%s"%s%s>%s</option>', $value,
            html_selected($value, $value_current, $strict), $extra, $text);
    }

    return $ret;
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
    return ($a !== null) ? (($strict ? $a === $b : $a == $b) ? ' checked' : '') : '';
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
    return ($a !== null) ? (($strict ? $a === $b : $a == $b) ? ' selected' : '') : '';
}

/**
 * Html compress.
 * @param  ?string $in
 * @return string
 */
function html_compress(?string $in): string
{
    $in = (string) $in;
    if ($in == '') {
        return $in;
    }

    // Styles.
    if (strpos($in, '<style>')) {
        $in = preg_replace_callback('~(<style>(.*?)</style>)~sm', function($match) {
            $in = trim($match[2]);

            // Comments.
            preg_match_all('~[^\'"]/\*+(?:.*)\*/\s*~smU', $in, $matches);
            foreach ($matches as $match) {
                $in = str_replace($match, '', $in);
            }

            return sprintf('<style>%s</style>', trim($in));
        }, $in);
    }

    // Scripts.
    if (strpos($in, '<script>')) {
        $in = preg_replace_callback('~(<script>(.*?)</script>)~sm', function($match) {
            $in = trim($match[2]);

            // Line comments (protect "http://" etc).
            $in = preg_replace('~(^|[^\'":])//(?:([^\r\n]+)|(.*?)[\r\n])$~sm', '', $in);

            // Doc comments.
            preg_match_all('~[^\'"]/\*+(?:.*)\*/\s*~smU', $in, $matches);
            foreach ($matches as $match) {
                $in = str_replace($match, '', $in);
            }

            return sprintf('<script>%s</script>', trim($in));
        }, $in);
    }

    // Remove comments.
    $in = preg_replace('~<!--(.*?)?-->~sm', '', $in);
    // Remove tabs & spaces.
    $in = preg_replace('~^[\t ]+~sm', '', $in);
    // Remove tag spaces.
    $in = preg_replace('~>\s+<(/?)([\w\d-]+)~sm', '><\\1\\2', $in);
    $in = preg_replace('~\s+</(\w+)>~', '</\1>', $in);
    $in = preg_replace('~</(\w+)>\s+~', '</\1> ', $in);

    // Text area "\n" problem.
    $textarea_tpl = '%{{{TEXTAREA}}}';
    $textarea_found = preg_match_all('~(<textarea(.*?)>(.*?)</textarea>)~sm', $in, $matches);

    // Fix text areas.
    if ($textarea_found) {
        foreach ($matches[0] as $match) {
            $in = str_replace($match, $textarea_tpl, $in);
        }
    }

    // Reduce white spaces.
    $in = preg_replace('~\s+~', ' ', $in);

    // fix textareas
    if ($textarea_found) {
        foreach ($matches[0] as $match) {
            $in = preg_replace("~{$textarea_tpl}~", $match, $in, 1);
        }
    }

    return trim($in);
}
