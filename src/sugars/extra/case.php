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
 * @return string
 * @since  4.0
 */
function to_title_case(string $in, string $sep = null): string
{
    return convert_case($in, CASE_TITLE, $sep);
}

/**
 * To dash case (eg: "foo bar" => "foo-bar").
 *
 * @param  string      $in
 * @param  string|null $sep
 * @return string
 * @since  4.0
 */
function to_dash_case(string $in, string $sep = null): string
{
    return convert_case($in, CASE_DASH, $sep);
}

/**
 * To snake case (eg: "foo bar" => "foo_bar").
 *
 * @param  string      $in
 * @param  string|null $sep
 * @return string
 * @since  4.0
 */
function to_snake_case(string $in, string $sep = null): string
{
    return convert_case($in, CASE_SNAKE, $sep);
}

/**
 * To camel case (eg: "foo bar" => "fooBar").
 *
 * @param  string      $in
 * @param  string|null $sep
 * @return string
 * @since  4.0
 */
function to_camel_case(string $in, string $sep = null): string
{
    return convert_case($in, CASE_CAMEL, $sep);
}
