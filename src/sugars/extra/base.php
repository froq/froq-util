<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * URL-safe base64 encoding.
 *
 * @param  string $input
 * @return string
 * @since  4.0
 */
function base64_encode_urlsafe(string $input): string
{
    return chop(strtr((string) base64_encode($input), '/+', '_-'), '=');
}

/**
 * URL-safe base64 decoding.
 *
 * @param  string $input
 * @param  bool   $strict
 * @return string
 * @since  4.0
 */
function base64_decode_urlsafe(string $input, bool $strict = false): string
{
    return (string) base64_decode(strtr($input, '_-', '/+'), $strict);
}
