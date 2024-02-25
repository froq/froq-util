<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

// Load top function files.
require 'sugars-function/_array.php';
require 'sugars-function/_string.php';

// Load other function files.
require 'sugars-function/base.php';
require 'sugars-function/cast.php';
require 'sugars-function/conv.php';
require 'sugars-function/date.php';
require 'sugars-function/dump.php';
require 'sugars-function/file.php';
require 'sugars-function/http.php';
require 'sugars-function/json.php';
require 'sugars-function/preg.php';
require 'sugars-function/rand.php';

/**
 * State initializer.
 *
 * @param  mixed ...$states
 * @return State
 */
function state(mixed ...$states): State
{
    return new State(...$states);
}

/**
 * Reflection initializer.
 *
 * @param  string|object $target
 * @param  string|null   $type
 * @return Reflector|null
 */
function reflect(string|object $target, string $type = null): Reflector|null
{
    return XReflection::reflect($target, $type);
}

/**
 * Temporary function.
 * @todo Remove as of 8.3
 */
if (!function_exists('json_validate')) {
    function json_validate(string $json): bool {
        return json_verify($json);
    }
}
