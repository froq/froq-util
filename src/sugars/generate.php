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
 * Generate nonce.
 * @param  int      $length
 * @param  int|null $base
 * @return ?string 20-length or N-length base2-62 characters.
 * @since  4.0, 4.1 Changed from rand_string(),rand_nonce().
 */
function generate_nonce(int $length = null, int $base = null): ?string
{
    if (($length ??= 20) < 1) {
        trigger_error(sprintf('%s(): Invalid length, min=1', __function__));
        return null;
    }

    $characters = BASE62_CHARACTERS;

    if ($base) {
        if ($base < 2 || $base > 62) {
            trigger_error(sprintf('%s(): Invalid base, min=2 & max=62', __function__));
            return null;
        }

        $characters = strcut($characters, $base);
    }

    $ret = '';
    while (strlen($ret) < $length) {
        $ret .= strrnd($characters, 1);
    }

    return strsub($ret, 0, $length);
}

/**
 * Generate nonce hash.
 * @param  int|null $length
 * @param  string   $algo
 * @return ?string
 * @since  4.1
 */
function generate_nonce_hash(int $length = 20, string $algo = 'md5'): ?string
{
    if (!$algo) {
        trigger_error(sprintf('%s(): Empty algo given', __function__));
        return null;
    }

    $ret =@ hash($algo, generate_nonce($length));

    if (!$ret) {
        trigger_error(sprintf('%s(): %s', __function__, error_get_last()['message']));
        return null;
    }

    return $ret;
}

/**
 * Generate random bytes.
 * @param  int|null $length
 * @param  string   $algo
 * @return ?string
 * @since  4.1
 */
function generate_random_bytes(int $length = 20, string $algo = 'md5'): ?string
{
    if (!$algo) {
        trigger_error(sprintf('%s(): Empty algo given', __function__));
        return null;
    }

    $ret =@ hash($algo, random_bytes($length));

    if (!$ret) {
        trigger_error(sprintf('%s(): %s', __function__, error_get_last()['message']));
        return null;
    }

    return $ret;
}

/**
 * Generate id.
 * @param  int|null $length
 * @param  int|null $base
 * @return ?string 20-length digits or N-length digits|base2-62 characters.
 * @since  4.0, 4.1 Changed from rand_id().
 */
function generate_id(int $length = null, int $base = null): ?string
{
    if (($length ??= 20) < 1) {
        trigger_error(sprintf('%s(): Invalid length, min=1', __function__));
        return null;
    }

    $characters = BASE62_CHARACTERS;

    if ($base) {
        if ($base < 11 || $base > 62) {
            trigger_error(sprintf('%s(): Invalid base, min=11 & max=62', __function__));
            return null;
        }

        $characters = strcut($characters, $base);
    }

    $mic = explode(' ', microtime());
    $ret = $mic[1] . substr($mic[0], 2, 6) . mt_rand(1000, 9999);

    // Prevent wrong convert below cos of length.
    if (strlen($ret) > $length) {
        $ret = strcut($ret, $length);
    }

    if (!$base) {
        $characters = BASE10_CHARACTERS;
    } elseif ($base && $base >= 11) { // No convert for digits.
        $ret = str_base_convert($ret, BASE10_CHARACTERS, $characters);
    }

    while (strlen($ret) < $length) {
        $ret .= strrnd($characters, 1);
    }

    return strsub($ret, 0, $length);
}

/**
 * Generate uniq id.
 * @param  int $length
 * @param  int $base
 * @return ?string
 * @since  4.4
 */
function generate_uniq_id(int $length = 20, int $base = 16): ?string
{
    return generate_id($length, $base);
}

/**
 * Generate random id.
 * @param  int $length Bytes length actually.
 * @param  int $base
 * @return ?string
 * @since  4.4
 */
function generate_random_id(int $length = 20, int $base = 16): ?string
{
    $ret = md5(random_bytes($length));

    // Note: this will change the id's length.
    if ($base != 16) {
        $ret = str_base_convert($ret, 16, $base);

        // Just an exception for base 36/62 for fixing id's length.
        switch ($base) {
            case 36: $ret = str_pad($ret, 26, strrnd(BASE36_CHARACTERS)); break;
            case 62: $ret = str_pad($ret, 22, strrnd(BASE62_CHARACTERS)); break;
        }
    }

    return $ret;
}

/**
 * Generate serial id.
 * @param  int|null $length
 * @param  bool     $use_date
 * @return ?string
 * @since  4.1
 */
function generate_serial_id(int $length = null, bool $use_date = false): ?string
{
    if (($length ??= 20) < 20) {
        trigger_error(sprintf('%s(): Invalid length, min=20', __function__));
        return null;
    }

    $mic = explode(' ', microtime());
    $ret = (!$use_date ? $mic[1] : date('YmdHis')) . substr($mic[0], 2, 6) . mt_rand(1000, 9999);

    while (strlen($ret) < $length) {
        $ret .= strrnd(BASE10_CHARACTERS, 1);
    }

    return strsub($ret, 0, $length);
}

/**
 * Generate oid.
 * @param  bool $count
 * @return string 24-length hex like Mongo.ObjectId.
 * @since  4.0, 4.1 Changed from rand_oid().
 */
function generate_oid(bool $count = true): string
{
    static $counter = 0;

    $bin = pack('N', time()) . substr(md5(gethostname()), 0, 3)
         . pack('n', getmypid()) . substr(pack('N', ($count ? $counter++ : mt_rand())), 1, 3);

    // Convert to hex.
    $ret = '';
    for ($i = 0; $i < 12; $i++) {
        $ret .= sprintf('%02x', ord($bin[$i]));
    }

    return $ret;
}

/**
 * Generate uid.
 * @param  int  $type
 * @param  bool $option
 * @return ?string
 * @since  4.4 Replaced with generate_uuid().
 */
function generate_uid(int $type = 1, bool $option = false): ?string
{
    switch ($type) {
        case 1: // Random (UUID/v4 or GUID).
            $ret = random_bytes(16);

            // GUID doesn't use 4 (version) or 8, 9, A, or B.
            if (!$option) { // Guid?
                $ret[6] = chr(ord($ret[6]) & 0x0f | 0x40);
                $ret[8] = chr(ord($ret[8]) & 0x3f | 0x80);
            }

            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($ret), 4));
        case 2: // Simple serial.
            $date = getdate();
            $uniq = sscanf(uniqid('', true), '%8s%6s.%s');

            return sprintf('%.08s-%04x-%04x-%04x-%.6s%.6s',
                $uniq[0], $date['year'],
                ($date['mon'] . $date['mday']),
                ($date['minutes'] . $date['seconds']),
                $uniq[1], $uniq[2]
            );
        case 3: // All digit.
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
        default:
            trigger_error(sprintf(
                '%s(): Invalid type %s; 1, 2 or 3 accepted only', __function__, $type
            ));

            return null;
    }
}

/**
 * Generate uuid.
 * @return string
 * @since  4.0, 4.1 Changed from rand_uuid().
 */
function generate_uuid(): string
{
    return generate_uid(1, false);
}

/**
 * Generate guid.
 * @return string
 * @since  4.0, 4.1 Changed from rand_guid().
 */
function generate_guid(): string
{
    return generate_uid(1, true);
}
