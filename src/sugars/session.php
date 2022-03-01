<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\UtilException;
use froq\App;

// Check dependencies (all others already come with froq\App).
if (!class_exists(App::class, false)) {
    throw new UtilException('Session sugars dependent to `froq` module but not found');
}

/**
 * Set/get a session variable or get session object.
 *
 * @param  string|array|null $key
 * @param  mixed|null        $value
 * @return mixed|froq\session\Session|null
 */
function session(string|array $key = null, mixed $value = null): mixed
{
    $session = app()->session();

    // Set/get.
    if ($session) {
        switch (func_num_args()) {
            case 1: return $session->get($key);
            case 2: return $session->set($key, $value);
        }
    }

    return $session;
}

/**
 * Set/get session flash.
 *
 * @param  mixed|null $message
 * @return mixed|null
 */
function session_flash(mixed $message = null): mixed
{
    $session = app()->session();

    if ($session) {
        switch (func_num_args()) {
            case 0: return $session->flash();
            case 1: return $session->flash($message);
        }
    }

    return null;
}

/**
 * Get session as array.
 *
 * @return array|null
 */
function session_array(): array|null
{
    return app()->session()?->toArray();
}

/**
 * Check whether session has an variable.
 *
 * @param  string $key
 * @return bool|null
 */
function session_has(string $key): bool|null
{
    return app()->session()?->has($key);
}

/**
 * Set a session variable.
 *
 * @param  string|array $key
 * @param  mixed|null   $value
 * @return bool|null
 */
function session_set(string|array $key, mixed $value = null): bool|null
{
    return app()->session()?->set($key, $value) ? true : null;
}

/**
 * Get a session variable.
 *
 * @param  string|array $key
 * @param  mixed|null   $default
 * @param  bool         $drop
 * @return mixed|null
 */
function session_get(string|array $key, mixed $default = null, bool $drop = false): mixed
{
    return app()->session()?->get($key, $default, $drop);
}

/**
 * Remove a session variable.
 *
 * @param  string|array $key
 * @return bool|null
 */
function session_remove(string|array $key): bool|null
{
    return app()->session()?->remove($key) ? true : null;
}

/**
 * Start session.
 *
 * @return bool|null
 */
function start_session(): bool|null
{
    return app()->session()?->start();
}

/**
 * End session.
 *
 * @return bool|null
 */
function end_session(): bool|null
{
    return app()->session()?->end();
}

/**
 * Generate a CSRF token for a form.
 *
 * @param  string $form
 * @param  string $token
 * @return bool|null
 * @since  5.0
 */
function generate_csrf_token(string $form): string|null
{
    return app()->session()?->generateCsrfToken($form);
}

/**
 * Validate a CSRF token for a form.
 *
 * @param  string $form
 * @param  string $token
 * @return bool|null
 * @since  5.0
 */
function validate_csrf_token(string $form, string $token): bool|null
{
    return app()->session()?->validateCsrfToken($form, $token);
}
