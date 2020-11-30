<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\UtilException;
use froq\http\{Http, Request, Response};
use froq\App;

// Check dependencies (all others already come with froq\App).
if (!class_exists(App::class, false)) {
    throw new UtilException("Http sugars dependent to 'froq' module but not found");
}

/**
 * HTTP version.
 * @return string
 * @since  4.0
 */
function http_version(): string
{
    return Http::version();
}

/**
 * HTTP date.
 * @param  int|null $time
 * @return string
 * @since  4.0
 */
function http_date(int $time = null): string
{
    return Http::date($time);
}

/**
 * HTTP date verify.
 * @param  string $date
 * @return bool
 * @since  4.0
 */
function http_date_verify(string $date): bool
{
    return Http::dateVerify($date);
}

/**
 * Request.
 * @return froq\http\Request
 */
function request(): Request
{
    return app()->request();
}

/**
 * Response.
 * @param  ... $args
 * @return froq\http\Response
 */
function response(...$args): Response
{
    $response = app()->response();

    if ($args) {
        @ [$code, $content, $content_attributes, $headers, $cookies] = $args;

        $code && $response->setStatus($code);

        if (count($args) >= 3) {
            $response->setBody($content, (array) $content_attributes);
        }

        $headers && $response->setHeaders($headers);
        $cookies && $response->setCookies($cookies);
    }

    return $response;
}

/**
 * Status.
 * @param  int|null $code
 * @return int|void
 */
function status(int $code = null)
{
    $response = app()->response();

    if ($code) {
        $response->status($code);
        return;
    }

    return $response->status()->getCode();
}

/**
 * Is get.
 * @return bool
 */
function is_get(): bool
{
    return app()->request()->isGet();
}

/**
 * Is post.
 * @return bool
 */
function is_post(): bool
{
    return app()->request()->isPost();
}

/**
 * Is put.
 * @return bool
 */
function is_put(): bool
{
    return app()->request()->isPut();
}

/**
 * Is patch.
 * @return bool
 * @since  4.0
 */
function is_patch(): bool
{
    return app()->request()->isPatch();
}

/**
 * Is delete.
 * @return bool
 */
function is_delete(): bool
{
    return app()->request()->isDelete();
}

/**
 * Is ajax.
 * @return bool
 */
function is_ajax(): bool
{
    return app()->request()->isAjax();
}

/**
 * Get.
 * @param  string|array|null $name
 * @param  any|null          $default
 * @return any|null
 */
function get($name = null, $default = null)
{
    return app()->request()->get($name, $default);
}

/**
 * Get has.
 * @param  string|array $name
 * @return bool
 */
function get_has($name): bool
{
    return app()->request()->hasGet($name);
}

/**
 * Post.
 * @param  string|array|null $name
 * @param  any|null          $default
 * @return any|null
 */
function post($name = null, $default = null)
{
    return app()->request()->post($name, $default);
}

/**
 * Post has.
 * @param  string|array $name
 * @return bool
 */
function post_has($name): bool
{
    return app()->request()->hasPost($name);
}

/**
 * Cookie.
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
 * Cookie has.
 * @param  string|array $name
 * @return bool
 */
function cookie_has($name): bool
{
    return app()->request()->hasCookie($name);
}

/**
 * Segment.
 * @param  int|string $key
 * @param  any|null   $default
 * @return any|null
 */
function segment($key, $default = null)
{
    $segments = app()->request()->uri()->segments();

    return $segments ? $segments->get($key, $default) : $default;
}

/**
 * Segments.
 * @param  bool $list
 * @return ?froq\http\request\Segments|?array
 */
function segments(bool $list = false)
{
    $segments = app()->request()->uri()->segments();

    if ($list && $segments) {
        $segments = $segments->toList();
    }

    return $segments;
}

/**
 * Redirect.
 * @param  string     $to
 * @param  int        $code
 * @param  array|null $headers
 * @param  array|null $cookies
 * @return void
 */
function redirect(string $to, int $code = 302, array $headers = null, array $cookies = null): void
{
    app()->response()->redirect($to, $code, $headers, $cookies);
}
