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

use froq\App;
use froq\http\{Http, Request, Response, response\Status};
use froq\util\UtilException;

// Check dependencies (all others already come with froq\App).
if (!class_exists(App::class, false)) {
    throw new UtilException('Http sugars dependent to "froq" module that not found');
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
 * @return froq\http\Response
 */
function response(...$arguments): Response
{
    $response = app()->response();
    if ($arguments) {
        @ [$code, $content, $content_attributes, $headers, $cookies] = $arguments;
        $code && $response->setStatus($code);
        if (!is_null($content)) {
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
    return app()->request()->method()->isGet();
}

/**
 * Is post.
 * @return bool
 */
function is_post(): bool
{
    return app()->request()->method()->isPost();
}

/**
 * Is put.
 * @return bool
 */
function is_put(): bool
{
    return app()->request()->method()->isPut();
}

/**
 * Is delete.
 * @return bool
 */
function is_delete(): bool
{
    return app()->request()->method()->isDelete();
}

/**
 * Is ajax.
 * @return bool
 */
function is_ajax(): bool
{
    return app()->request()->method()->isAjax();
}

/**
 * Get.
 * @param  string|array|null $name
 * @param  any|null          $value_default
 * @return any|null
 */
function get($name = null, $value_default = null)
{
    return app()->request()->get($name, $value_default);
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
 * @param  any|null          $value_default
 * @return any|null
 */
function post($name = null, $value_default = null)
{
    return app()->request()->post($name, $value_default);
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
 * @param  any|null          $value_default
 * @return any|null
 */
function cookie($name = null, $value_default = null)
{
    return app()->request()->cookie($name, $value_default);
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
 * @param  int      $i
 * @param  any|null $value_default
 * @return any|null
 */
function segment(int $i, $value_default = null)
{
    return app()->request()->uri()->segment($i, $value_default);
}

/**
 * Segments.
 * @return array
 */
function segments(): array
{
    return app()->request()->uri()->segments();
}

/**
 * Redirect.
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
