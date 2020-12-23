<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
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
 * @param  any|null          $value
 * @return any|null|froq\session\Session
 */
function session($key = null, $value = null)
{
    static $session; $session ??= app()->session();

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
 * @param  any|null $message
 * @return any|null
 */
function session_flash($message = null)
{
    $session = session();

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
    return session()?->toArray();
}

/**
 * Check whether session has an variable.
 *
 * @param  string $key
 * @return bool|null
 */
function session_has(string $key): bool|null
{
    return session()?->has($key);
}

/**
 * Set a session variable.
 *
 * @param  string|array $key
 * @param  any|null     $value
 * @return bool|null
 */
function session_set($key, $value = null): bool|null
{
    return session()?->set($key, $value) ? true : null;
}

/**
 * Get a session variable.
 *
 * @param  string|array $key
 * @param  any|null     $value
 * @param  bool         $remove
 * @return any|null
 */
function session_get($key, $default = null, bool $remove = false)
{
    return session()?->get($key, $default, $remove);
}

/**
 * Remove a session variable.
 *
 * @param  string|array $key
 * @return bool|null
 */
function session_remove($key): bool|null
{
    return session()?->remove($key);
}

/**
 * Start session.
 *
 * @return bool|null
 */
function start_session(): bool|null
{
    return session()?->start();
}

/**
 * End session.
 *
 * @return bool|null
 */
function end_session(): bool|null
{
    return session()?->end();
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
    return session()?->generateCsrfToken($form);
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
    return session()?->validateCsrfToken($form, $token);
}
