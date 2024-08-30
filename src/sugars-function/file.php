<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\file\{Directory, File, TempFile, Path, Stat};

/**
 * Get system temporary directory.
 *
 * @return string
 * @since  4.0
 */
function tmp(): string
{
    return sys_get_temp_dir();
}

/**
 * Create a directory in system temporary directory.
 *
 * @param  string $prefix
 * @param  int    $mode
 * @return string|null
 * @since  5.0
 */
function tmpdir(string $prefix = '', int $mode = 0755): string|null
{
    // Prefix may become subdir here.
    $dir = tmp() . DIRECTORY_SEPARATOR . $prefix . suid();

    return mkdir($dir, $mode, true) ? $dir : null;
}

/**
 * Create a file in system temporary directory.
 *
 * @param  string $prefix
 * @param  int    $mode
 * @return string|null
 * @since  5.0
 */
function tmpnam(string $prefix = '', int $mode = 0644): string|null
{
    // Prefix may become subdir here.
    $nam = tmp() . DIRECTORY_SEPARATOR . $prefix . suid();

    return mkfile($nam, $mode) ? $nam : null;
}

/**
 * Check whether given directory is in temporary directory.
 *
 * @param  string $dir
 * @return bool
 * @since  5.0
 */
function is_tmpdir(string $dir): bool
{
    return is_dir($dir)
        && str_starts_with($dir, tmp() . DIRECTORY_SEPARATOR)
        && realpath($dir) !== tmp();
}

/**
 * Check whether given file is in temporary directory.
 *
 * @param  string $nam
 * @return bool
 * @since  5.0
 */
function is_tmpnam(string $nam): bool
{
    return is_file($nam)
        && str_starts_with($nam, tmp() . DIRECTORY_SEPARATOR);
}

/**
 * Make a file.
 *
 * @param  string $file
 * @param  int    $mode
 * @return bool
 * @since  4.0
 */
function mkfile(string $file, int $mode = 0644): bool
{
    if (!$file = get_real_path($file)) {
        trigger_error(format('%s(): No file given', __FUNCTION__));
        return false;
    }

    if (is_dir($file)) {
        trigger_error(format('%s(%s): Cannot make file: Is a directory', __FUNCTION__, $file));
        return false;
    }
    if (is_file($file)) {
        trigger_error(format('%s(%s): Cannot make file: File exists', __FUNCTION__, $file));
        return false;
    }

    // Ensure directory.
    if (!@dirmake(dirname($file))) {
        trigger_error(format('%s(%s): Cannot make file directory: %s', __FUNCTION__, $file, (
            strsrc($error = (string) error_message(extract: true), 'permission', true)
                ? 'Permission denied' : $error
        )));
        return false;
    }

    if (!@touch($file)) {
        trigger_error(format('%s(%s): Cannot make file: %s', __FUNCTION__, $file, (
            strsrc($error = (string) error_message(extract: true), 'permission', true)
                ? 'Permission denied' : $error
        )));
        return false;
    }

    if (!@chmod($file, $mode)) {
        trigger_error(format('%s(%s): Cannot make file: %s', __FUNCTION__, $file, (
            strsrc($error = (string) error_message(extract: true), 'permitted', true)
                ? 'Permission denied' : $error
        )));
        return false;
    }

    return true;
}

/**
 * Remove a file.
 *
 * @param  string $file
 * @return bool
 * @since  4.0
 */
function rmfile(string $file): bool
{
    if (!$file = get_real_path($file)) {
        trigger_error(format('%s(): No file given', __FUNCTION__));
        return false;
    }

    if (is_dir($file)) {
        trigger_error(format('%s(%s): Cannot remove file: Is a directory', __FUNCTION__, $file));
        return false;
    }

    if (!@unlink($file)) {
        trigger_error(format('%s(%s): Cannot remove file: %s', __FUNCTION__, $file, error_message(extract: true)));
        return false;
    }

    return true;
}

/**
 * Open a file.
 *
 * @param  string     $file
 * @param  string     $mode
 * @param  bool       $temp
 * @param  array|null $options
 * @return froq\file\{File|TempFile}
 */
function file_open(string $file, string $mode = 'rb', bool $temp = false, array $options = null): File|TempFile
{
    if ($temp) {
        $options['prefix'] = $file;
        return new TempFile(true, $options);
    }

    $options['open'] = $mode;
    return new File($file, $options);
}

/**
 * Create a file, optionally a temporary file.
 *
 * @param  string $file
 * @param  int    $mode
 * @param  bool   $temp
 * @return string|null
 * @since  4.0
 */
function file_create(string $file, int $mode = 0644, bool $temp = false): string|null
{
    return $temp ? tmpnam($file, $mode) // file=prefix.
                 : (mkfile($file, $mode) ? $file : null);
}

/**
 * Remove a file.
 *
 * @alias rmfile()
 * @since 4.0
 */
function file_remove(string $file): bool
{
    return rmfile($file);
}

/**
 * Copy a file safely, copy() alternative.
 *
 * @param  string $file
 * @param  string $to
 * @param  bool   $check
 * @return bool
 */
function file_copy(string $file, string $to, bool $check = false): bool
{
    if (!$file = get_real_path($ofile = $file, check: true)) {
        trigger_error(format('%s(%s): No such file', __FUNCTION__, $ofile));
        return false;
    }

    if ($check && file_exists($to)) {
        trigger_error(format('%s(%s,%s): To file already exists', __FUNCTION__, $ofile, $to));
        return false;
    }

    if (copy($file, $to)) {
        return true;
    }

    // Failing sometimes, somehow..
    if (is_file($to) && copy($file, $tmp = tmpnam())) {
        if (unlink($to) && copy($tmp, $to)) {
            unlink($tmp); // Drop temp file.
            return true;
        }
    }

    return false;
}

/**
 * Move a file safely, rename() alternative.
 *
 * @param  string $file
 * @param  string $to
 * @param  bool   $check
 * @return bool
 */
function file_move(string $file, string $to, bool $check = false): bool
{
    if (!$file = get_real_path($ofile = $file, check: true)) {
        trigger_error(format('%s(%s): No such file', __FUNCTION__, $ofile));
        return false;
    }

    if ($check && file_exists($to)) {
        trigger_error(format('%s(%s,%s): To file already exists', __FUNCTION__, $ofile, $to));
        return false;
    }

    return rename($file, $to);
}

/**
 * Load a file, with normal include or output buffer.
 *
 * @param  string     $file
 * @param  array|null $file_data To use in included file.
 * @param  bool       $ob        To Use Output Buffer or not.
 * @return mixed
 */
function file_load(string $file, array $file_data = null, bool $ob = false): mixed
{
    if (!$file = get_real_path($ofile = $file, check: true)) {
        trigger_error(format('%s(%s): No such file', __FUNCTION__, $ofile));
        return null;
    }

    if (is_dir($file)) {
        trigger_error(format('%s(%s): Cannot load file: Is a directory', __FUNCTION__, $file));
        return null;
    }

    extract($file_data ?? []);

    if (!$ob) {
        return include $file;
    }

    ob_start();
    include $file;
    return ob_get_clean();
}

/**
 * Read a file.
 *
 * @alias file_get_contents()
 * @since 4.0
 */
function file_read(string $file, int $offset = 0, int $length = null): string|null
{
    if (!$file = get_real_path($ofile = $file, check: true)) {
        trigger_error(format('%s(%s): No such file', __FUNCTION__, $ofile));
        return null;
    }

    if (is_dir($file)) {
        trigger_error(format('%s(%s): Cannot read file: Is a directory', __FUNCTION__, $file));
        return null;
    }

    $ret = file_get_contents($file, offset: $offset, length: $length);

    return ($ret !== false) ? $ret : null;
}

/**
 * Write a file, with append option.
 *
 * @alias file_put_contents()
 * @since 4.0
 */
function file_write(string $file, string $contents, int $flags = 0, bool $append = false): int|null
{
    if (!$file = get_real_path($file)) {
        trigger_error(format('%s(): No file given', __FUNCTION__));
        return null;
    }

    if (is_dir($file)) {
        trigger_error(format('%s(%s): Cannot write file: Is a directory', __FUNCTION__, $file));
        return null;
    }

    // Don't truncate file contents.
    $append && $flags |= FILE_APPEND;

    $ret = file_put_contents($file, $contents, $flags);

    return ($ret !== false) ? $ret : null;
}

/**
 * Set a file contents, without append option.
 *
 * @param  string $file
 * @param  string $contents
 * @param  int    $flags
 * @return int|false
 * @since  4.0
 */
function file_set_contents(string $file, string $contents, int $flags = 0): int|false
{
    // Drop append option, truncate.
    $flags && $flags &= ~FILE_APPEND;

    return file_write($file, $contents, $flags) ?? false;
}

/**
 * Get a file path.
 *
 * @alias get_real_path()
 * @since 4.0
 */
function file_path(string $file, ...$args)
{
    return get_real_path($file, ...$args);
}

/**
 * Get a file name, not base name.
 *
 * @param  string $file
 * @param  bool   $with_ext
 * @return string|null
 * @since  4.0
 */
function file_name(string $file, bool $with_ext = false): string|null
{
    // An empty input & a directory is not a file.
    if (str_empty($file) || str_ends_with($file, DIRECTORY_SEPARATOR)) {
        return null;
    }

    // Function basename() wants an explicit suffix to remove it from name,
    // but using just a boolean here is more sexy..
    $ret = basename($file);

    if ($ret === '.' || $ret === '..') {
        return null;
    }

    if ($ret && !$with_ext && ($ext = file_extension($file, true))) {
        $ret = substr($ret, 0, -strlen($ext));
    }

    return $ret ?: null;
}

/**
 * Get a file mime.
 *
 * @param  string $file
 * @return string|null
 * @since  4.0
 */
function file_mime(string $file): string|null
{
    if (str_empty($file)) {
        return null;
    }

    if (is_dir($file)) {
        return 'directory';
    }

    // Utilize file module.
    if (class_exists('froq\file\mime\Mime')) {
        return froq\file\mime\Mime::getType($file);
    }

    $ret = mime_content_type($file);

    if ($ret === false) {
        // Try with extension.
        $extension = file_extension($file);
        if ($extension !== null) {
            static $cache; // For some speed..
            if (empty($cache[$extension])) {
                foreach (require __DIR__ . '/../etc/mimes.php' as $type => $extensions) {
                    if (in_array($extension, $extensions, true)) {
                        $cache[$extension] = $ret = $type;
                        break;
                    }
                }
            }
        }
    }

    return $ret ?: null;
}

/**
 * Get a file (file, directory or link) stat.
 *
 * @param  string $file
 * @param  bool   $clear When needed to clear a file stat cache.
 * @param  bool   $check When resolved files no need to be checked.
 * @return array|null
 * @since  7.0
 * @link   https://en.wikipedia.org/wiki/Stat_(system_call)
 */
function file_stat(string $file, bool $clear = true, bool $check = true): array|null
{
    if ($check && !$file = get_real_path($ofile = $file, check: true, real: false)) {
        trigger_error(format('%s(%s): Failed to open stat: No such file or directory',
            __FUNCTION__, $ofile));
        return null;
    }

    // For fresh results.
    $clear && clearstatcache(true, $file);

    $ret = is_link($file) ? lstat($file) : stat($file);

    // Not used.
    // if ($ret) {
    //     $mod = $ret['mode'];
    //     if (is_link($file) && ($rfile = realpath($file))) {
    //         $rmod = @fileperms($rfile);
    //         if ($rmod !== false) {
    //             $mod = $rmod;
    //         }
    //     }
    //     // https://github.com/php/php-src/blob/master/ext/standard/filestat.c#L824
    //     $rmask = $wmask = $xmask = 0;
    //     if ($ret['uid'] === getmyuid()) {
    //         [$rmask, $wmask, $xmask] = [0000400, 0000200, 0000100];
    //     } elseif ($ret['gid'] === getmygid()) {
    //         [$rmask, $wmask, $xmask] = [0000040, 0000020, 0000010];
    //     } elseif (function_exists('posix_getgroups')) {
    //         $mygid = getmygid();
    //         foreach (posix_getgroups() as $gid) {
    //             if ($mygid === $gid) {
    //                 [$rmask, $wmask, $xmask] = [0000040, 0000020, 0000010];
    //                 break;
    //             }
    //         }
    //     }
    //     $S_ISWTB = ($mod & $wmask) !== 0;
    //     $S_ISRDB = ($mod & $rmask) !== 0;
    //     $S_ISXTB = ($mod & $xmask) !== 0;
    //     // https://github.com/openbsd/src/blob/master/sys/sys/stat.h
    //     $S_ISLNK = is_link($file);
    //     // https://php.net/stat#54999
    //     // $S_ISLNK = (($mod & 0170000) === 0120000);
    //     $S_ISREG = (($mod & 0170000) === 0100000);
    //     $S_ISDIR = (($mod & 0170000) === 0040000);
    //     $ret += [
    //         // Type modes.
    //         'is_link' => +$S_ISLNK, 'is_file' => +$S_ISREG, 'is_dir'  => +$S_ISDIR,
    //         // Operation modes.
    //         'is_readable' => +$S_ISWTB, 'is_writable' => +$S_ISRDB, 'is_executable' => +$S_ISXTB,
    //     ];
    // }

    return $ret ?: null;
}

/**
 * Get a file extension.
 *
 * @param  string $file
 * @param  bool   $with_dot
 * @param  bool   $lower
 * @return string|null
 * @since  4.0
 */
function file_extension(string $file, bool $with_dot = false, bool $lower = true): string|null
{
    // An empty input & a directory is not a file.
    if (str_empty($file) || str_ends_with($file, DIRECTORY_SEPARATOR) || !str_contains($file, '.')) {
        return null;
    }

    $info = array_filter(pathinfo($file), 'strlen');

    // Function pathinfo() returns ".foo" for example "/some/path/.foo",
    // and if $with_dot false then this function return ".", no baybe!
    if (!isset($info['filename']) || !isset($info['extension'])) {
        return null;
    }

    $ret = strrchr($info['basename'], '.');

    if ($ret !== '') {
        $lower && $ret = strtolower($ret);
        if (!$with_dot) {
            $ret = ltrim($ret, '.');
        }
    }

    return ($ret !== '' && $ret !== '.') ? $ret : null;
}

/**
 * Aliases.
 */
function filepath(...$args) { return file_path(...$args); }
function filename(...$args) { return file_name(...$args); }
function filemime(...$args) { return file_mime(...$args); }
function filestat(...$args) { return file_stat(...$args); }

/**
 * Make a directory, return its path.
 *
 * @param  string $dir
 * @param  int    $mode
 * @param  bool   $temp
 * @param  bool   $recursive
 * @param  bool   $check
 * @return string|null
 * @since  6.0
 */
function dirmake(string $dir, int $mode = 0755, bool $temp = false, bool $recursive = true, bool $check = true): string|null
{
    if (!$dir = get_real_path($odir = $dir)) {
        trigger_error(format('%s(): No directory given', __FUNCTION__));
        return null;
    }

    // Check existence.
    if ($check && file_exists($dir)) {
        return $dir;
    }

    return $temp ? tmpdir($odir, $mode) : (mkdir($dir, $mode, $recursive) ? $dir : null);
}

/**
 * Make a file, return its path.
 *
 * @param  string $file
 * @param  int    $mode
 * @param  bool   $temp
 * @param  bool   $check
 * @return string|null
 * @since  6.0
 */
function filemake(string $file, int $mode = 0644, bool $temp = false, bool $check = true): string|null
{
    if (!$file = get_real_path($ofile = $file)) {
        trigger_error(format('%s(): No file given', __FUNCTION__));
        return null;
    }

    // Check existence.
    if ($check && file_exists($file)) {
        return $file;
    }

    return $temp ? tmpnam($ofile, $mode) : (mkfile($file, $mode) ? $file : null);
}

/**
 * @alias stream_read_all()
 * @since 5.0
 */
function freadall($fp): string|false
{
    return stream_read_all($fp);
}

/**
 * @alias stream_set_contents()
 * @since 4.0
 */
function freset($fp, string $contents): int|false
{
    return stream_write_all($fp, $contents);
}

/**
 * Get a file stream metadata.
 *
 * @param  resource $fp
 * @return array|false
 * @since  4.0
 */
function fmeta($fp): array|false
{
    return stream_get_meta_data($fp);
}

/**
 * Get a file stream size.
 *
 * @param  resource $fp
 * @return int|false
 * @since  5.0
 */
function fsize($fp): int|false
{
    return fstat($fp)['size'] ?? false;
}

/**
 * @alias stream_write_all()
 * @since 4.0
 */
function stream_set_contents($stream, string $contents): int|false
{
    return stream_write_all($stream, $contents);
}

/**
 * Read all stream contents, don't move pointer.
 *
 * @param  resource $stream
 * @return string|false
 * @since  7.0
 */
function stream_read_all($stream): string|false
{
    // Check whether stream writable.
    if (@fread($stream, 1) === false) {
        trigger_error(format('%s(): %s', __FUNCTION__, error_message(extract: true)));
        return false;
    }

    $pos = ftell($stream) - 1; // -1: For read above.
    $ret = stream_get_contents($stream, -1, 0);
    fseek($stream, $pos);

    return $ret;
}

/**
 * Reset a stream contents truncating, move pointer to top.
 *
 * @param  resource $stream
 * @param  string   $contents
 * @return int|false
 * @since  7.0
 */
function stream_write_all($stream, string $contents): int|false
{
    // Check whether stream writable.
    if (@fwrite($stream, '.') === false) {
        trigger_error(format('%s(): %s', __FUNCTION__, error_message(extract: true)));
        return false;
    }

    // Since stream stat size also pointer position is not changing even after ftruncate() for
    // files (not "php://temp" etc), we rewind the stream. Without this, stats won't be reset!
    rewind($stream);

    if ($ret = ftruncate($stream, 0)) {
        $ret = fwrite($stream, $contents);
        rewind($stream);
    }

    return $ret;
}

/**
 * Handy file system functions (port of froq\file).
 */
function xdir(string $path, bool $tmp = false, mixed ...$options): Directory {
    $tmp && $path = tmpdir(prefix: $path);
    return new Directory($path, $options);
}
function xfile(string $path, bool $tmp = false, mixed ...$options): File {
    $tmp && $path = tmpnam(prefix: $path);
    return new File($path, $options);
}
function xpath(string $path, bool $useRealPath = false): Path {
    return new Path($path, $useRealPath);
}
function xstat(string $path): Stat {
    return new Stat($path);
}
