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
 * @param  ?string $input
 * @param  bool    $do_simple
 * @return string
 */
function html_encode(?string $input, bool $do_simple = false): string
{
    return $do_simple
        ? str_replace(['<', '>'], ['&lt;', '&gt;'], (string) $input)
        : str_replace(["'", '"', '<', '>'], ['&#39;', '&#34;', '&lt;', '&gt;'], (string) $input);
}

/**
 * Html decode.
 * @param  ?string $input
 * @param  bool    $do_simple
 * @return string
 */
function html_decode(?string $input, bool $do_simple = false): string
{
    return $do_simple
        ? str_ireplace(['&lt;', '&gt;'], ['<', '>'], (string) $input)
        : str_ireplace(['&#39;', '&#34;', '&lt;', '&gt;'], ["'", '"', '<', '>'], (string) $input);
}

/**
 * Html strip.
 * @param  ?string $input
 * @param  ?string $allowed_tags
 * @param  bool    $do_decode
 * @return string
 */
function html_strip(?string $input, ?string $allowed_tags = '', bool $do_decode = false): string
{
    if ($do_decode) {
        $input = html_decode($input, true);
    }

    if ($allowed_tags != '') {
        $allowed_tags = implode('', array_map(function ($tag) {
            return '<'. trim($tag, '<>') .'>';
        }, explode(',', $allowed_tags)));
    }

    return strip_tags((string) $input, (string) $allowed_tags);
}

/**
 * Html remove.
 * @param  ?string $input
 * @param  ?string $allowed_tags
 * @param  bool    $do_decode
 * @return string
 */
function html_remove(?string $input, ?string $allowed_tags = '', bool $do_decode = false): string
{
    if ($do_decode) {
        $input = html_decode($input, true);
    }

    if ($allowed_tags != '') {
        $pattern = '~<(?!(?:'. str_replace(',', '|', $allowed_tags) .')\b)(\w+)\b[^>]*/?>(?:.*?</\1>)?~is';
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
 * @param  any          $key_b
 * @param  string|array $extra
 * @return string
 */
function html_options(iterable $input, $key_b = null, $extra = null): string
{
    if ($extra !== null) {
        if (is_array($extra)) {
            $extra = html_attributes($extra);
        }
        $extra = ' '. trim($extra);
    }

    $return = '';
    foreach ($input as $key_a => $text) {
        $return .= sprintf('<option value="%s"%s%s>%s</option>', $key_a,
            html_selected($key_a, $key_b), $extra, $text);
    }

    return $return;
}

/**
 * Html checked.
 * @param  any  $a
 * @param  any  $b
 * @param  bool $is_strict
 * @return string
 */
function html_checked($a, $b, bool $is_strict = false): string
{
    return $a === null ? '' : (
        $is_strict ? $a === $b ? ' checked' : '' : $a == $b ? ' checked' : ''
    );
}

/**
 * Html disabled.
 * @param  any  $a
 * @param  any  $b
 * @param  bool $is_strict
 * @return string
 */
function html_disabled($a, $b, bool $is_strict = false): string
{
    return $a === null ? '' : (
        $is_strict ? $a === $b ? ' disabled' : '' : $a == $b ? ' disabled' : ''
    );
}

/**
 * Html selected.
 * @param  any  $a
 * @param  any  $b
 * @param  bool $is_strict
 * @return string
 */
function html_selected($a, $b, bool $is_strict = false): string
{
    return $a === null ? '' : (
        $is_strict ? $a === $b ? ' selected' : '' : $a == $b ? ' selected' : ''
    );
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
        preg_match_all('~\s*/[\*]+(?:.*?)[\*]/\s*~sm', $input, $matches);
        foreach ($matches as $key => $value) {
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
    $textarea_tpl = '%{{{TEXTAREA}}}';
    $textarea_count = preg_match_all(
        '~(<textarea(.*?)>(.*?)</textarea>)~sm', $input, $matches);

    // fix textareas
    if ($textarea_count) {
        foreach ($matches[0] as $match) {
            $input = str_replace($match, $textarea_tpl, $input);
        }
    }

    // reduce white spaces
    $input = preg_replace('~\s+~', ' ', $input);

    // fix textareas
    if ($textarea_count) {
        foreach ($matches[0] as $match) {
            $input = preg_replace("~{$textarea_tpl}~", $match, $input, 1);
        }
    }

    return trim($input);
}
