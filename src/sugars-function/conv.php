<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Convert a decimal / decimal string to alphabetical string.
 *
 * @param  int|string $decs
 * @param  int        $base
 * @return string
 */
function decalp(int|string $decs, int $base = 62): string
{
    $ret = convert_base($decs, 10, $base);

    return $ret;
}

/**
 * Convert an alphabetical string to decimal / decimal string.
 *
 * @param  string $alps
 * @param  int    $base
 * @param  bool   $cast
 * @return int|string
 */
function alpdec(string $alps, int $base = 62, bool $cast = true): int|string
{
    $ret = convert_base($alps, $base, 10);

    if ($cast && $ret <= PHP_INT_MAX) {
        $ret = (int) $ret;
    }

    return $ret;
}

/**
 * Aliases for those weirdos.
 */
function binhex(string $string): string       { return bin2hex($string); }
function hexbin(string $string): string|false { return hex2bin($string); }
