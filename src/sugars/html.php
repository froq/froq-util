<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Encode HTML characters in given input.
 *
 * @param  string $input
 * @param  bool   $simple
 * @return string
 */
function html_encode(string $input, bool $simple = false): string
{
    return xstring($input)->htmlEncode($simple)->toString();
}

/**
 * Decode HTML characters in given input.
 *
 * @param  string $input
 * @param  bool   $simple
 * @return string
 */
function html_decode(string $input, bool $simple = false): string
{
    return xstring($input)->htmlDecode($simple)->toString();
}

/**
 * Strip HTML tags in given input.
 *
 * @param  string            $input
 * @param  string|array|null $allowed
 * @param  bool              $decode
 * @return string
 */
function html_strip(string $input, string|array $allowed = null, bool $decode = false): string
{
    return xstring($input)->stripTags($allowed, $decode)->toString();
}

/**
 * Remove HTML tags in given input.
 *
 * @param  string            $input
 * @param  string|array|null $allowed
 * @param  bool              $decode
 * @return string
 */
function html_remove(string $input, string|array $allowed = null, bool $decode = false): string
{
    return xstring($input)->removeTags($allowed, $decode)->toString();
}

/**
 * Make an attribute string with given name/value notated array.
 *
 * @param  array $attributes
 * @return string
 */
function html_attributes(array $attributes): string
{
    $ret = [];

    foreach ($attributes as $name => $value) {
        $ret[] = sprintf('%s="%s"', $name, $value);
    }

    return join(' ', $ret);
}

/**
 * Make options string with given name/value notated array.
 *
 * @param  array             $options
 * @param  mixed             $current
 * @param  bool              $strict
 * @param  string|array|null $extra
 * @return string
 */
function html_options(array $options, mixed $current = null, bool $strict = false, string|array $extra = null): string
{
    if ($extra !== null) {
        if (is_array($extra)) {
            $extra = html_attributes($extra);
        }
        $extra = ' ' . trim($extra);
    }

    $ret = '';

    foreach ($options as $value => $text) {
        if (is_array($text)) {
            [$value, $text] = $text;
        }

        $ret .= sprintf(
            '<option value="%s"%s%s>%s</option>', $value,
            html_selected($value, $current, $strict), $extra, $text
        );
    }

    return $ret;
}

/**
 * Make a "checked" attribute string when given inputs are equal.
 *
 * @param  mixed $input1
 * @param  mixed $input2
 * @param  bool  $strict
 * @return string
 */
function html_checked(mixed $input1, mixed $input2, bool $strict = false): string
{
    $ret = '';

    if ($input1 !== null && (
        $strict ? $input1 === $input2 : $input1 == $input2
    )) {
        $ret = ' checked';
    }

    return $ret;
}

/**
 * Make a "selected" attribute string when given inputs are equal.
 *
 * @param  mixed $input1
 * @param  mixed $input2
 * @param  bool  $strict
 * @return string
 */
function html_selected(mixed $input1, mixed $input2, bool $strict = false): string
{
    $ret = '';

    if ($input1 !== null && (
        $strict ? $input1 === $input2 : $input1 == $input2
    )) {
        $ret = ' selected';
    }

    return $ret;
}

/**
 * Compress given HTML input.
 *
 * @param  string $input
 * @return string
 */
function html_compress(string $input): string
{
    $input = trim($input);
    if ($input === '') {
        return '';
    }

    // Styles.
    $input = preg_replace_callback('~<style(.*?)>(.*?)</style>~sm', function ($match) {
        $content = trim($match[2]);

        // Remove doc comments.
        $content = preg_remove('~(?<![\'"])/\*(.*?)\*/~sm', $content);

        return '<style>' . trim($content) . '</style>';
        return sprintf('<style%s>%s</style>', $match[1], trim($content));
    }, $input);

    // Scripts.
    $input = preg_replace_callback('~<script(.*?)>(.*?)</script>~sm', function ($match) {
        $content = trim($match[2]);

        // Remove doc comments.
        $content = preg_remove('~(?<![\'"])/\*(.*?)\*/~sm', $content);

        // Remove line comments (but keep "http://" etc).
        $content = preg_remove('~(?<![\'":])//(?:([^\r\n]+)|(.*?)[\r\n])$~sm', $content);

        return sprintf('<script%s>%s</script>', $match[1], trim($content));
    }, $input);

    // Remove comments.
    $input = preg_remove('~<!--(.*?)-->~sm', $input);

    // @cancel: Corrupting irrelavant spaces.
    // Remove tag spaces (not inner spaces, eg: "Text <b>bold</b>").
    // $input = preg_replace('~<(\w[\w-]*)(.*?)>\s+~sm', '<\1\2>', $input);
    // $input = preg_replace('~\s*</(\w[\w-]*)>\s*~sm', '</\1>', $input);

    // Textarea "\n" problem.
    $reduce_spaces = fn($s) => preg_replace('~\s+~', ' ', $s);

    preg_match_all('~(<textarea(.*?)>(.*)?</textarea>)~sm', $input, $matches);

    if ($matches) {
        $temp = 'textarea@' . huid();

        foreach ($matches[0] as $match) {
            $input = str_replace($match, $temp, $input);
        }
    }

    $input = $reduce_spaces($input);

    if ($matches) {
        foreach ($matches[0] as $i => $_) {
            $textarea = sprintf(
                '<textarea%s>%s</textarea>',
                $reduce_spaces($matches[2][$i]),
                $matches[3][$i]
            );

            $input = preg_replace("~{$temp}~", $textarea, $input, 1);
        }
    }

    return trim($input);
}
