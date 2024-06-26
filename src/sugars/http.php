<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\UtilException;
use froq\http\{Request, Response, response\Status};
use froq\App;

// Check dependencies (all others already come with froq\App).
if (!class_exists(App::class, false)) {
    throw new UtilException('Http sugars dependent to "froq" module but not found');
}

/**
 * Get app's request.
 *
 * @return froq\http\Request
 */
function request(): Request
{
    return app()->request;
}

/**
 * Get app's response, but also optionally set code, content, attributes, headers and cookies.
 *
 * @param  mixed ...$args
 * @return froq\http\Response
 */
function response(mixed ...$args): Response
{
    $response = app()->response;

    if ($args) {
        [$code, $content, $attributes] = pad($args, 3);

        $code && $response->setStatus($code);

        $headers = $cookies = null;

        if (count($args) >= 3) {
            $response->setBody($content, $attributes);

            $headers = $args[3] ?? null;
            $cookies = $args[4] ?? null;
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
 * @return int
 */
function status(int $code = null): int
{
    $status = app()->response->status;

    if ($code !== null) {
        $status->setCode($code);
    }

    return $status->getCode();
}

/**
 * Check whether request method is "GET".
 *
 * @return bool
 */
function is_get(): bool
{
    return app()->request->isGet();
}

/**
 * Check whether request method is "POST".
 *
 * @return bool
 */
function is_post(): bool
{
    return app()->request->isPost();
}

/**
 * Check whether request method is "PUT".
 *
 * @return bool
 */
function is_put(): bool
{
    return app()->request->isPut();
}

/**
 * Check whether request method is "PATCH".
 *
 * @return bool
 * @since  4.0
 */
function is_patch(): bool
{
    return app()->request->isPatch();
}

/**
 * Check whether request method is "DELETE".
 *
 * @return bool
 */
function is_delete(): bool
{
    return app()->request->isDelete();
}

/**
 * Check whether request was made via "AJAX".
 *
 * @return bool
 */
function is_ajax(): bool
{
    return app()->request->isAjax();
}

/**
 * Get one/many/all $_GET params.
 *
 * @param  string|array<string>|null $name
 * @param  mixed|null                $default
 * @param  mixed                  ...$options
 * @return mixed
 */
function get(string|array $name = null, mixed $default = null, mixed ...$options): mixed
{
    return app()->request->get($name, $default, ...$options);
}

/**
 * Check one/many $_GET params.
 *
 * @param  string|array<string> $name
 * @return bool
 */
function get_has(string|array $name): bool
{
    return app()->request->hasGet($name);
}

/**
 * Get one/many/all $_POST params.
 *
 * @param  string|array<string>|null $name
 * @param  mixed|null                $default
 * @param  mixed                  ...$options
 * @return mixed
 */
function post(string|array $name = null, mixed $default = null, mixed ...$options): mixed
{
    return app()->request->post($name, $default, ...$options);
}

/**
 * Check one/many $_POST params.
 *
 * @param  string|array<string> $name
 * @return bool
 */
function post_has(string|array $name): bool
{
    return app()->request->hasPost($name);
}

/**
 * Get one/many/all $_COOKIE params.
 *
 * @param  string|array<string>|null $name
 * @param  mixed|null                $default
 * @param  mixed                  ...$options
 * @return mixed
 */
function cookie(string|array $name = null, mixed $default = null, mixed ...$options): mixed
{
    return app()->request->cookie($name, $default, ...$options);
}

/**
 * Check one/many $_COOKIE params.
 *
 * @param  string|array<string> $name
 * @return bool
 */
function cookie_has(string|array $name): bool
{
    return app()->request->hasCookie($name);
}

/**
 * Get a segment param.
 *
 * @param  int|string $key
 * @param  mixed|null $default
 * @return mixed
 */
function segment(int|string $key, mixed $default = null): mixed
{
    return app()->request->segment($key, $default);
}

/**
 * Get many segment params.
 *
 * @param  array<int|string>|null $keys
 * @param  array|null             $defaults
 * @return array
 */
function segments(array $keys = null, array $defaults = null): array
{
    return app()->request->segments($keys, $defaults);
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
    app()->response->redirect($to, $code, $headers, $cookies);
}
