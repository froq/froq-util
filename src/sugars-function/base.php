<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Convert a string to Base-62 string.
 *
 * @param  string $input
 * @return string
 */
function base62_encode(string $input): string
{
    // Always hex (no way else, so far).
    $input = bin2hex($input);

    return convert_base($input, 16, 62);
}

/**
 * Convert a Base-62 string to string.
 *
 * @param  string $input
 * @param  bool   $strict
 * @return string|null
 */
function base62_decode(string $input, bool $strict = true): string|null
{
    if ($strict && !ctype_alnum($input)) {
        trigger_error('base62_decode(): Invalid Base-62 input', E_USER_WARNING);
        return null;
    }

    $ret = convert_base($input, 62, 16);

    // Fix non-even length.
    if (strlen($ret) % 2) {
        $ret .= '0';
    }

    return hex2bin($ret);
}
