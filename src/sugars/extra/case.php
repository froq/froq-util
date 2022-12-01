<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Convert given input to title case (eg: "foo bar" => "FooBar").
 *
 * @param  string      $input
 * @param  string|null $separator
 * @return string
 * @since  4.0
 */
function to_title_case(string $input, string $separator = null): string
{
    return convert_case($input, CASE_TITLE, $separator);
}

/**
 * Convert given input to dash case (eg: "foo bar" => "foo-bar").
 *
 * @param  string      $input
 * @param  string|null $separator
 * @return string
 * @since  4.0
 */
function to_dash_case(string $input, string $separator = null): string
{
    return convert_case($input, CASE_DASH, $separator);
}

/**
 * Convert given input to snake case (eg: "foo bar" => "foo_bar").
 *
 * @param  string      $input
 * @param  string|null $separator
 * @return string
 * @since  4.0
 */
function to_snake_case(string $input, string $separator = null): string
{
    return convert_case($input, CASE_SNAKE, $separator);
}

/**
 * Convert given input to camel case (eg: "foo bar" => "fooBar").
 *
 * @param  string      $input
 * @param  string|null $separator
 * @return string
 * @since  4.0
 */
function to_camel_case(string $input, string $separator = null): string
{
    return convert_case($input, CASE_CAMEL, $separator);
}
