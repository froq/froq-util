<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * @alias http_parse_query_string()
 * @since 4.0
 */
function parse_query_string(string $query, bool $clean = false): array
{
    return http_parse_query_string($query, $clean);
}

/**
 * @alias http_build_query_string()
 * @since 4.0
 */
function build_query_string(array $data, bool $clean = false): string
{
    return http_build_query_string($data, $clean);
}

/**
 * @alias http_parse_cookie()
 * @since 4.0
 */
function parse_cookie(string $cookie): array
{
    return http_parse_cookie($cookie);
}

/**
 * @alias http_build_cookie()
 * @since 4.0
 */
function build_cookie(string $name, string|null $value, array $options = null): string
{
    return http_build_cookie($name, $value, $options);
}

/**
 * @alias http_parse_headers()
 * @since 6.0
 */
function parse_headers(string $headers, string|int $case = null): array
{
    return http_parse_headers($headers, $case);
}

/**
 * @alias http_build_headers()
 * @since 6.0
 */
function build_headers(array $data, string|int $case = null): string
{
    return http_build_headers($data, $case);
}

/**
 * @alias http_parse_header()
 * @since 6.0
 */
function parse_header(string $header, string|int $case = null, bool $verbose = false): array
{
    return http_parse_header($header, $case, $verbose);
}

/**
 * @alias http_build_header()
 * @since 6.0
 */
function build_header(string $name, string|null $value): string
{
    return http_build_header($name, $value);
}

/**
 * @alias http_parse_request_line()
 * @since 6.0
 */
function parse_request_line(string $line): array
{
    return http_parse_request_line($line);
}

/**
 * @alias http_parse_response_line()
 * @since 6.0
 */
function parse_response_line(string $line): array
{
    return http_parse_response_line($line);
}
