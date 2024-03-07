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
 * @param  bool|int|null  $indent
 * @param  JsonError|null &$error
 * @return string|null
 */
function json_serialize(mixed $data, bool|int $indent = null, JsonError &$error = null): string|null
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
 * @param  bool           $normalize
 * @return mixed|null
 */
function json_unserialize(string|null $json, bool $assoc = false, JsonError &$error = null, bool $normalize = false): mixed
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
        // Last try with normalization.
        if ($normalize) return json_unserialize(
            json_normalize($json), $assoc, normalize: false
        );

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

/**
 * JSON prettify.
 *
 * @param  string|null $json
 * @param  string|int  $indent
 * @return string|null
 */
function json_prettify(string|null $json, string|int $indent = 2): string|null
{
    return $json ? JsonPrettifier::prettify($json, $indent) : null;
}

/**
 * JSON normalize.
 *
 * @param  string|null $json
 * @param  string|int  $indent
 * @return string|null
 */
function json_normalize(string|null $json, string|int $indent = null): string|null
{
    if ($json === null) {
        return null;
    }
    if ($json === '') {
        return '';
    }

    // Fix line comments.
    if (str_contains($json, '//')) {
        if ($lines = preg_split('~(\r?\n|\r)~', $json, -1, PREG_SPLIT_NO_EMPTY)) {
            $temp = [];

            foreach ($lines as $line) {
                if (preg_match('~(?:["\]\}\w]+\s*,|[\[\{])(\s*//.*(?=[\r\n]|$))~', $line, $match)) {
                    $line = substr($line, 0, -strlen($match[1]));

                    $inside = preg_match('~[\[\{]\s*,?~', $line);

                    // Somehow this skips above.
                    if ($inside && preg_match('~(?:["\]\}\w]+|[\[\{])\s*,~', $line)) {
                        $line = substr($line, 0, -1);
                    }
                }

                if (($trim = trim($line)) && !str_starts_with($trim, '//')) {
                    $temp[] = $line;
                }
            }

            $json = join("\n", $temp);
        }
    }

    // Fix trailing commas.
    if (str_contains($json, ',')) {
        if (preg_match_all('~,\s*([\}\]])~', $json, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $repl = str_replace(',', '', $match[0]);

                if (strlen($repl) > 1) {
                    $repl = trim($repl,  ' ');
                }

                while (($pos = strpos($json, $match[0])) !== false) {
                    $json = substr_replace($json, $repl, $pos, strlen($match[0]));
                }
            }
        }
    }

    if ($indent) {
        $json = preg_replace(['~[\r\n]~', '~ +~'], ' ', $json);
        $json = json_prettify($json, $indent);
    }

    return $json;
}
