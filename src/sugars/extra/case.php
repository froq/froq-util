<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
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
