<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

/**
 * URL-safe base64 encoding.
 *
 * @param  string $in
 * @return string
 * @since  4.0
 */
function base64_encode_urlsafe(string $in): string
{
    return chop(strtr((string) base64_encode($in), '/+', '_-'), '=');
}

/**
 * URL-safe base64 decoding.
 *
 * @param  string $in
 * @param  bool   $strict
 * @return string
 * @since  4.0
 */
function base64_decode_urlsafe(string $in, bool $strict = false): string
{
    return (string) base64_decode(strtr($in, '_-', '/+'), $strict);
}
