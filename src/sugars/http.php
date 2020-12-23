<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\UtilException;
use froq\http\{Http, Request, Response, request\Segments, response\Status};
use froq\App;

// Check dependencies (all others already come with froq\App).
if (!class_exists(App::class, false)) {
    throw new UtilException('Http sugars dependent to froq module but not found');
}

/**
 * Get HTTP version.
 *
 * @return string
 * @since  4.0
 */
function http_version(): string
{
    return Http::version();
}

/**
 * Make/get an HTTP date.
 *
 * @param  int|null $time
 * @return string
 * @since  4.0
 */
function http_date(int $time = null): string
{
    return Http::date($time);
}

/**
 * Verify an HTTP date.
 *
 * @param  string $date
 * @return bool
 * @since  4.0
 */
function http_date_verify(string $date): bool
{
    return Http::dateVerify($date);
}

/**
 * Get app's request.
 *
 * @return froq\http\Request
 */
function request(): Request
{
    return app()->request();
}

/**
 * Get app's response, but also optionally set code, content, attributes, headers and cookies.
 *
 * @param  ... $args
 * @return froq\http\Response
 */
function response(...$args): Response
{
    $response = app()->response();

    if ($args) {
        @ [$code, $content, $attributes, $headers, $cookies] = $args;

        $code && $response->setStatus($code);

        if (count($args) >= 3) {
            $response->setBody($content, (array) $attributes);
        }

        $headers && $response->setHeaders($headers);
        $cookies && $response->setCookies($cookies);
    }

    return $response;
}

/**
 * Set or get HTTP status code using app's response.
 *
 * @param  int|null $code
 * @return int|void
 */
function status(int $code = null)
{
    if ($code === null) {
        return app()->response()->status()->getCode();
    }

    app()->response()->status()->setCode($code);
}

/**
 * Check whether request method is "GET".
 *
 * @return bool
 */
function is_get(): bool
{
    return app()->request()->isGet();
}

/**
 * Check whether request method is "POST".
 *
 * @return bool
 */
function is_post(): bool
{
    return app()->request()->isPost();
}

/**
 * Check whether request method is "PUT".
 *
 * @return bool
 */
function is_put(): bool
{
    return app()->request()->isPut();
}

/**
 * Check whether request method is "PATCH".
 *
 * @return bool
 * @since  4.0
 */
function is_patch(): bool
{
    return app()->request()->isPatch();
}

/**
 * Check whether request method is "DELETE".
 *
 * @return bool
 */
function is_delete(): bool
{
    return app()->request()->isDelete();
}

/**
 * Check whether request was made via "AJAX".
 *
 * @return bool
 */
function is_ajax(): bool
{
    return app()->request()->isAjax();
}

/**
 * Get one/many "GET" param.
 *
 * @param  string|array|null $name
 * @param  any|null          $default
 * @return any|null
 */
function get(string|array $name = null, $default = null)
{
    return app()->request()->get($name, $default);
}

/**
 * Get one/many "GET" param existence.
 *
 * @param  string|array $name
 * @return bool
 */
function get_has(string|array $name): bool
{
    return app()->request()->hasGet($name);
}

/**
 * Get one/many "POST" param.
 *
 * @param  string|array|null $name
 * @param  any|null          $default
 * @return any|null
 */
function post(string|array $name = null, $default = null)
{
    return app()->request()->post($name, $default);
}

/**
 * Get one/many "POST" param existence.
 *
 * @param  string|array $name
 * @return bool
 */
function post_has(string|array $name): bool
{
    return app()->request()->hasPost($name);
}

/**
 * Get one/many "COOKIE" param.
 *
 * @param  string|array|null $name
 * @param  any|null          $default
 * @return any|null
 */
function cookie($name = null, $default = null)
{
    return func_num_args() > 1 ? app()->request()->cookie($name, $default)
                               : app()->request()->cookie($name);
}

/**
 * Get one/many "COOKIE" param existence.
 *
 * @param  string|array $name
 * @return bool
 */
function cookie_has($name): bool
{
    return app()->request()->hasCookie($name);
}

/**
 * Get a segment param.
 *
 * @param  int|string $key
 * @param  any|null   $default
 * @return any|null
 */
function segment(int|string $key, $default = null)
{
    return app()->request()->uri()->getSegment($key, $default);
}

/**
 * Get URI segments property or segment params.
 *
 * @param  bool $list
 * @return froq\http\request\Segments|array
 */
function segments(array $keys = null, $default = null): Segments|array
{
    return app()->request()->uri()->getSegments($key, $default);
}

/**
 * Redirect client to given location with/without given headers and cookies.
 *
 * @param  string     $to
 * @param  int        $code
 * @param  array|null $headers
 * @param  array|null $cookies
 * @return void
 */
function redirect(string $to, int $code = Status::FOUND, array $headers = null, array $cookies = null): void
{
    app()->response()->redirect($to, $code, $headers, $cookies);
}
