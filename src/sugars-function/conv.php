<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Convert a decimal string to alphanumeric string.
 *
 * @param  int|string $dec
 * @return string|null
 */
function decalp(int|string $dec): string|null
{
    if (!ctype_digit((string) $dec)) {
        trigger_error('decalp(): Invalid decimal input', E_USER_WARNING);
        return null;
    }

    return convert_base($dec, 10, 62);
}

/**
 * Convert an alphanumeric string to decimal string.
 *
 * @param  string $alp
 * @param  bool   $cast
 * @return int|string|null
 */
function alpdec(string $alp, bool $cast = true): int|string|null
{
    if (!ctype_alnum($alp)) {
        trigger_error('alpdec(): Invalid alphanumeric input', E_USER_WARNING);
        return null;
    }

    $ret = convert_base($alp, 62, 10);

    if ($cast && $ret <= PHP_INT_MAX) {
        $ret = (int) $ret;
    }

    return $ret;
}

/**
 * Convert hexadecimal string to alphanumeric string.
 *
 * @param  string $hex
 * @return string|null
 */
function hexalp(string $hex): string|null
{
    if (!ctype_xdigit($hex)) {
        trigger_error('hexalp(): Invalid hexadecimal input', E_USER_WARNING);
        return null;
    }

    return convert_base($hex, 16, 62);
}

/**
 * Convert an alphanumeric string to hexadecimal string.
 *
 * @param  string $alp
 * @return string|null
 */
function alphex(string $alp): string|null
{
    if (!ctype_alnum($alp)) {
        trigger_error('alphex(): Invalid alphanumeric input', E_USER_WARNING);
        return null;
    }

    return convert_base($alp, 62, 16);
}

/**
 * Aliases for those weirdos.
 */
function binhex(string $bin): string       { return bin2hex($bin); }
function hexbin(string $hex): string|false { return hex2bin($hex); }
