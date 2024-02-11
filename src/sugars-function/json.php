<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * As default, parse/build flags.
 */
const JSON_ENCODE_FLAGS = JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRESERVE_ZERO_FRACTION;
const JSON_DECODE_FLAGS = JSON_BIGINT_AS_STRING;

/**
 * Missing error constants for here.
 */
const JSON_ERROR_EMPTY = 20;
const JSON_ERROR_NULL  = 21;

/**
 * JSON serialize.
 *
 * @param  mixed          $data
 * @param  bool|int       $indent
 * @param  JsonError|null &$error
 * @return string|null
 */
function json_serialize(mixed $data, bool|int $indent = false, JsonError &$error = null): string|null
{
    $error = null;

    try {
        $json = json_encode($data, flags: JSON_ENCODE_FLAGS|JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        $error = new JsonError($e->getMessage(), code: $e->getCode());
        return null;
    }

    if ($indent) {
        $indent = ($indent === true) ? 2 : $indent;
        return JsonPrettifier::prettify($json, $indent);
    }

    return $json;
}

/**
 * JSON unserialize.
 *
 * @param  string|null    $json
 * @param  bool           $assoc
 * @param  JsonError|null &$error
 * @return mixed|null
 */
function json_unserialize(string|null $json, bool $assoc = false, JsonError &$error = null): mixed
{
    $error = null;

    // '' is sytax error (useless).
    if ($json === '' || $json === null) {
        [$message, $code] = $json === '' ? ['Empty JSON', JSON_ERROR_EMPTY] : ['Null JSON', JSON_ERROR_NULL];
        $error = new JsonError($message, code: $code);
        return null;
    }

    try {
        $data = json_decode($json, $assoc, flags: JSON_DECODE_FLAGS|JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        $error = new JsonError($e->getMessage(), code: $e->getCode());
        return null;
    }

    return $data;
}

/**
 * JSON verify.
 *
 * @param  string|null    $json
 * @param  JsonError|null &$error
 * @return bool
 */
function json_verify(string|null $json, JsonError &$error = null): bool
{
    $error = null;

    // '' is sytax error (useless).
    if ($json === '' || $json === null) {
        [$message, $code] = $json === '' ? ['Empty JSON', JSON_ERROR_EMPTY] : ['Null JSON', JSON_ERROR_NULL];
        $error = new JsonError($message, code: $code);
        return false;
    }

    try {
        json_decode($json, flags: JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        $error = new JsonError($e->getMessage(), code: $e->getCode());
        return false;
    }

    return true;
}
