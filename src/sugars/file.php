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
 * File read.
 * @param  string       $file
 * @param  int|null     $size
 * @param  string|null &$error
 * @return ?string
 * @since  3.0
 */
function file_read(string $file, int $size = null, string &$error = null): ?string
{
    $ret =@ !$size ? file_get_contents($file) : file_get_contents($file, false, null, 0, $size);

    if ($ret === false) {
        $error = sprintf('Cannot read file [error: %s, file: %s]', error(true), $file);
        return null;
    }

    return $ret;
}

/**
 * File write.
 * @param  string       $file
 * @param  string       $contents
 * @param  int|null     $flags
 * @param  string|null &$error
 * @return bool
 * @since  3.0
 */
function file_write(string $file, string $contents, int $flags = null, string &$error = null): bool
{
    $ret =@ file_put_contents($file, $contents, $flags ?? 0);

    if ($ret === false) {
        $error = sprintf('Cannot write file [error: %s, file: %s]', error(true), $file);
        return false;
    }

    return true;
}

/**
 * File mode.
 * @param  string       $file
 * @param  int|null     $mode
 * @param  string|null &$error
 * @return ?string
 * @since  3.0
 */
function file_mode(string $file, int $mode = null, string &$error = null): ?string
{
    if ($mode !== null) {
        // get mode
        if ($mode === -1) {
            $ret =@ fileperms($file);
            if ($ret === false) {
                $error = sprintf('Cannot get file stat for %s', $file);
            }
        }
        // set mode
        else {
            $ret =@ chmod($file, $mode);
            if ($ret === false) {
                $error = sprintf('Cannot set file mode [error: %s, file: %s]', error(true), $file);
            }
            $ret = $mode;
        }

        // compare;
        // $mode = file_mode($file, -1)
        // $mode === '644' or octdec($mode) === 0644
        return $ret ?  decoct($ret & 0777) : null;
    }

    // get full permissions
    $perms =@ fileperms($file);
    if ($perms === false) {
        $error = sprintf('Cannot get file stat for %s', $file);
        return null;
    }

    // @source http://php.net/fileperms
    switch ($perms & 0xf000) {
        case 0xc000: $ret = 's'; break; // socket
        case 0xa000: $ret = 'l'; break; // symbolic link
        case 0x8000: $ret = 'r'; break; // regular
        case 0x6000: $ret = 'b'; break; // block special
        case 0x4000: $ret = 'd'; break; // directory
        case 0x2000: $ret = 'c'; break; // character special
        case 0x1000: $ret = 'p'; break; // FIFO pipe
            default: $ret = 'u'; // unknown
    }

    // owner
    $ret .= (($perms & 0x0100) ? 'r' : '-');
    $ret .= (($perms & 0x0080) ? 'w' : '-');
    $ret .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

    // group
    $ret .= (($perms & 0x0020) ? 'r' : '-');
    $ret .= (($perms & 0x0010) ? 'w' : '-');
    $ret .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

    // world
    $ret .= (($perms & 0x0004) ? 'r' : '-');
    $ret .= (($perms & 0x0002) ? 'w' : '-');
    $ret .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

    return $ret;
}
