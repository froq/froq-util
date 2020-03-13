<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

use froq\util\Numbers;

/**
 * Rand int.
 * @param  int|null $min
 * @param  int|null $max
 * @return int
 */
function rand_int(int $min = null, int $max = null): int
{
    return Numbers::randomInt($min, $max);
}

/**
 * Rand float.
 * @param  float|null $min
 * @param  float|null $max
 * @return float
 */
function rand_float(float $min = null, float $max = null): float
{
    return Numbers::randomFloat($min, $max);
}

/**
 * Rand string.
 * @param  int  $length
 * @param  bool $hex
 * @return string
 * @since  4.0
 */
function rand_string(int $length = 16, bool $hex = false): string
{
    static $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $str = $chars;
    if ($hex) {
        $str = substr($str, 0, 16);
    }

    $ret = '';
    while (strlen($ret) < $length) {
        $ret .= str_shuffle($str)[0];
    }

    return $ret;
}

/**
 * Rand id.
 * @return string A 20-length digits.
 * @since  4.0
 */
function rand_id(): string
{
    $tmp = explode(' ', microtime());

    return $tmp[1] . substr($tmp[0], 2, 6) . mt_rand(1000, 9999);
}

/**
 * Rand uid.
 * @param  bool $simple
 * @return string A 14|20-length hex.
 * @since  4.0
 */
function rand_uid(bool $simple = true): string
{
    $tmp = explode('.', uniqid('', true));

    return $simple ? $tmp[0] : substr(vsprintf('%14s%\'06x', $tmp), 0, 20);
}

/**
 * Rand oid.
 * @param  bool $count
 * @return string A 24-length hex like Mongo.ObjectId.
 * @since  4.0
 */
function rand_oid(bool $count = true): string
{
    static $counter = 0;

    $bin = pack('N', time()) . substr(md5(gethostname()), 0, 3)
         . pack('n', getmypid()) . substr(pack('N', $count ? $counter++ : mt_rand()), 1, 3);

    // Convert to hex.
    $ret = '';
    for ($i = 0; $i < 12; $i++) {
        $ret .= sprintf('%02x', ord($bin[$i]));
    }

    return $ret;
}

/**
 * Rand uuid.
 * @param  int  $type
 * @param  bool $option
 * @return string
 * @since  4.0
 */
function rand_uuid(int $type = 1, bool $option = false): string
{
    // Random (UUID/v4 or GUID).
    if ($type == 1) {
        $ret = random_bytes(16);

        // GUID doesn't use 4 (version) or 8, 9, A, or B.
        if (!$option) { // Guid?
            $ret[6] = chr(ord($ret[6]) & 0x0f | 0x40);
            $ret[8] = chr(ord($ret[8]) & 0x3f | 0x80);
        }

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($ret), 4));
    }
    // Simple serial.
    elseif ($type == 2) {
        $date = getdate();
        $uniq = sscanf(uniqid('', true), '%8s%6s.%s');

        return sprintf('%.08s-%04x-%04x-%04x-%.6s%.6s',
            $uniq[0], $date['year'],
            ($date['mon'] . $date['mday']),
            ($date['minutes'] . $date['seconds']),
            $uniq[1], $uniq[2]
        );
    }
    // All digit.
    elseif ($type == 3) {
        if ($option) { // Rand?
            $digits = '';
            do {
                $digits .= mt_rand();
            } while (strlen($digits) < 32);
        } else {
            [$msec, $sec] = explode(' ', microtime());
            $digits = $sec . hrtime(true) . substr($msec, 2);
        }

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($digits, 4));
    }

    trigger_error(sprintf(
        '%s(): Invalid type %s; 1, 2 and 3 are accepted only',
        __function__, $type
    ));

    return '';
}

/**
 * Rand guid.
 * @return string
 * @since  4.0
 */
function rand_guid(): string
{
    return rand_uuid(1, true);
}
