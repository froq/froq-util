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

/**
 * Rand int.
 * @return int
 */
function rand_int(): int
{
    return random_int(0, PHP_INT_MAX);
}

/**
 * Rand float.
 * @return float
 */
function rand_float(): float
{
    return lcg_value();
}

/**
 * Rand string.
 * @param  int $len
 * @return string
 * @since  4.0
 */
function rand_string(int $len = 16): string
{
    $ret = base64_encode(random_bytes($len));
    $ret = str_replace(['/', '+'], '0', rtrim($ret, '='));

    return substr($ret, 0, $len);
}

/**
 * Rand hash.
 * @param  int $len
 * @return string
 * @since  4.0
 */
function rand_hash(int $len = 16): string
{
    $ret = bin2hex(random_bytes($len));

    return substr($ret, 0, $len);
}

/**
 * Rand id.
 * @return string
 * @since  4.0
 */
function rand_id(): string
{
    $tmp = explode(' ', microtime());

    return $tmp[1] . substr($tmp[0], 2, 6) . random_int(1000, 9999);
}

/**
 * Rand oid.
 * @param  bool $count
 * @return string
 * @since  4.0
 */
function rand_oid(bool $count = true): string
{
    static $counter = 0;

    $bin = pack('N', time()) . substr(md5(gethostname()), 0, 3)
         . pack('n', getmypid()) . substr(pack('N', $count ? $counter++ : mt_rand()), 1, 3);

    // convert to hex
    $ret = '';
    for ($i = 0; $i < 12; $i++) {
        $ret .= sprintf('%02x', ord($bin[$i]));
    }

    return $ret;
}

/**
 * Rand guid.
 * @return string
 * @since  4.0
 */
function rand_guid(): string
{
    $ret = random_bytes(16);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($ret), 4));
}

/**
 * Rand uuid.
 * @return string
 * @since  4.0
 */
function rand_uuid(): string
{
    $ret = random_bytes(16);
    $ret[6] = chr(ord($ret[6]) & 0x0F | 0x40);
    $ret[8] = chr(ord($ret[8]) & 0x3F | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($ret), 4));
}
