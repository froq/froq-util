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
    throw new UtilException("Session sugars dependent to 'froq' module but not found");
}

/**
 * Session.
 * @param  string|array|null $key
 * @param  any               $value
 * @return froq\session\Session|any
 */
function session($key = null, $value = null)
{
    static $session;
    $session ??= app()->session();

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
 * Session flash.
 * @param  any|null $message
 * @return ?any
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
 * Session array.
 * @return ?array
 */
function session_array(): ?array
{
    return ($session = session()) ? $session->toArray() : null;
}

/**
 * Session has.
 * @param  string $key
 * @return ?bool
 */
function session_has(string $key): ?bool
{
    return ($session = session()) ? $session->has($key) : null;
}

/**
 * Session set.
 * @param  string|array $key
 * @param  any|null     $value
 * @return ?bool
 */
function session_set($key, $value = null): ?bool
{
    return ($session = session()) ? $session->set($key, $value) != null : null;
}

/**
 * Session get.
 * @param  string|array $key
 * @param  any|null     $value
 * @param  bool         $remove
 * @return ?any
 */
function session_get($key, $default = null, bool $remove = false)
{
    return ($session = session()) ? $session->get($key, $default, $remove) : null;
}

/**
 * Session remove.
 * @param  string|array $key
 * @return ?bool
 */
function session_remove($key): ?bool
{
    return ($session = session()) ? $session->remove($key) == null : null;
}

/**
 * Start session.
 * @return ?bool
 */
function start_session(): ?bool
{
    return ($session = session()) ? $session->start() : null;
}

/**
 * End session.
 * @return ?bool
 */
function end_session(): ?bool
{
    return ($session = session()) ? $session->end() : null;
}
