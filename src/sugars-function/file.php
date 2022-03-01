<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

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
 * Create a folder in system temporary directory.
 *
 * @param  string $prefix
 * @param  int    $mode
 * @return string|null
 * @since  5.0
 */
function tmpdir(string $prefix = '', int $mode = 0755): string|null
{
    // Prefix may becomes subdir here.
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
    // Prefix may becomes subdir here.
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
        && realpath($dir) != tmp();
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
 * Create a file with given file path.
 *
 * @param  string $file
 * @param  int    $mode
 * @return bool
 * @since  4.0
 */
function mkfile(string $file, int $mode = 0644): bool
{
    $file = get_real_path($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return false;
    }

    if (is_dir($file)) {
        trigger_error(sprintf('%s(): Cannot make file %s, it\'s a directory', __function__, $file));
        return false;
    }
    if (is_file($file)) {
        trigger_error(sprintf('%s(): Cannot make file %s, it\'s already exist', __function__, $file));
        return false;
    }

    // Ensure directory.
    if (!@dirmake($dir = dirname($file))) {
        trigger_error(sprintf('%s(): Cannot make file directory %s [error: %s]', __function__, $dir,
            error_message()));
        return false;
    }

    return touch($file) && chmod($file, $mode);
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
    $file = get_real_path($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return false;
    }

    if (is_dir($file)) {
        trigger_error(sprintf('%s(): Cannot remove %s, it\'s a directory', __function__, $file));
        return false;
    }

    return is_file($file) && unlink($file);
}

/**
 * Create a file in temporary directory.
 *
 * @param  string $prefix
 * @param  int    $mode
 * @return string|null
 * @since  4.0
 */
function mkfiletemp(string $prefix = '', int $mode = 0644): string|null
{
    return tmpnam($prefix, $mode);
}

/**
 * Remove a file from in temporary directory.
 *
 * @param  string $file
 * @return bool
 * @since  4.0
 */
function rmfiletemp(string $file): bool
{
    if (!is_tmpnam($file)) {
        trigger_error(sprintf('%s(): Cannot remove `%s` file that is out of %s directory or not exists',
            __function__, $file, tmp()));
        return false;
    }

    return is_file($file) && unlink($file);
}

/**
 * Create a folder in system temporary directory.
 *
 * @param  string $prefix
 * @param  int    $mode
 * @since  4.0
 * @return string|null
 */
function mkdirtemp(string $prefix = '', int $mode = 0755): string|null
{
    return tmpdir($prefix, $mode);
}

/**
 * Remove a folder from system temporary directory.
 *
 * @param  string $dir
 * @return bool
 * @since  4.0
 */
function rmdirtemp(string $dir): bool
{
    if (!is_tmpdir($dir)) {
        trigger_error(sprintf('%s(): Cannot remove `%s` directory that is out of %s directory or not exists',
            __function__, $dir, tmp()));
        return false;
    }

    // Clean inside but not recursive.
    if (is_dir($dir)) {
        foreach (glob($dir . '/*') as $file) {
            unlink($file);
        }
    }

    return is_dir($dir) && rmdir($dir);
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
    if ($temp) { // Prefix=file.
        return mkfiletemp($file, $mode);
    }
    return mkfile($file, $mode) ? $file : null;
}

/**
 * Create a temporary file.
 *
 * @param  string $prefix
 * @param  int    $mode
 * @return string|null
 * @since 4.0
 */
function file_create_temp(string $prefix = '', int $mode = 0644): string|null
{
    return mkfiletemp($prefix, $mode);
}

/**
 * Remove a file.
 *
 * @alias rmfile()
 * @since 4.0
 */
function file_remove(...$args)
{
    return rmfile(...$args);
}

/**
 * Write a file contents.
 *
 * @alias file_put_contents()
 * @since 4.0
 */
function file_write(...$args)
{
    $ret = file_put_contents(...$args);

    return ($ret !== false) ? $ret : null;
}

/**
 * Read a file contents.
 *
 * @alias file_get_contents()
 * @since 4.0
 */
function file_read(...$args): string|null
{
    $ret = file_get_contents(...$args);

    return ($ret !== false) ? $ret : null;
}

/**
 * Read a file output (buffer) contents.
 *
 * @param  string     $file
 * @param  array|null $file_data
 * @return string|null
 * @since  4.0
 */
function file_read_output(string $file, array $file_data = null): string|null
{
    if (!is_file($file)) {
        trigger_error(sprintf('%s(): No file exists such %s', __function__, $file));
        return null;
    }
    if (!str_ends_with($file, '.php')) {
        trigger_error(sprintf('%s(): Cannot include non-PHP file such %s', __function__, $file));
        return null;
    }

    // Data, used in file.
    $file_data && extract($file_data);

    ob_start();
    include $file;
    return ob_get_clean();
}

/**
 * Read a file stream contents without modifing seek position.
 *
 * @param  resource &$handle
 * @return string|null
 * @since  5.0
 */
function file_read_stream(&$handle): string|null
{
    $pos = ftell($handle);
    $ret = stream_get_contents($handle, -1, 0);
    fseek($handle, $pos);

    return ($ret !== false) ? $ret : null;
}

/**
 * Set a file contents, but no append.
 *
 * @param  string $file
 * @param  string $contents
 * @param  int    $flags
 * @return int|null
 * @since  4.0
 */
function file_set_contents(string $file, string $contents, int $flags = 0): int|null
{
    // Because, setting entire file contents.
    if ($flags) $flags &= ~FILE_APPEND;

    $ret = file_put_contents($file, $contents, $flags);

    return ($ret !== false) ? $ret : null;
}

/**
 * Get a file path.
 *
 * @aliasOf get_real_path()
 * @since 4.0
 */
function file_path(...$args)
{
    return get_real_path(...$args);
}

/**
 * Get file name, not base name.
 *
 * @param  string $file
 * @param  bool   $with_ext
 * @return string|null
 * @since  4.0
 */
function file_name(string $file, bool $with_ext = false): string|null
{
    // A directory is not a file.
    if (str_ends_with($file, DIRECTORY_SEPARATOR)) {
        return null;
    }

    // Function basename() wants an explicit suffix to remove it from name,
    // but using just a boolean here is more sexy..
    $ret = basename($file);

    if ($ret == '.' || $ret == '..') {
        return null;
    }

    if ($ret && !$with_ext && ($ext = file_extension($file, true))) {
        $ret = substr($ret, 0, -strlen($ext));
    }

    return $ret ?: null;
}

/**
 * Get file mime.
 *
 * @param  string $file
 * @return string|null
 * @since  4.0
 */
function file_mime(string $file): string|null
{
    $mime = mime_content_type($file) ?: null;

    if (!$mime) {
        // Try with extension.
        $extension = file_extension($file, false);
        if ($extension) {
            static $cache; // For some speed..
            if (empty($cache[$extension])) {
                foreach (require __dir__ . '/../statics/mime.php' as $type => $extensions) {
                    if (in_array($extension, $extensions, true)) {
                        $cache[$extension] = $mime = $type;
                        break;
                    }
                }
            }
        }
    }

    return $mime;
}

/**
 * Get file extension.
 *
 * @param  string $file
 * @param  bool   $with_dot
 * @param  bool   $lower
 * @return string|null
 * @since  4.0
 */
function file_extension(string $file, bool $with_dot = false, bool $lower = true): string|null
{
    $info = pathinfo($file);

    // Function pathinfo() returns ".foo" for example "/some/path/.foo",
    // and if $with_dot false then this function return ".", no baybe!
    if (empty($info['filename']) || !isset($info['extension'])) {
        return null;
    }

    $ret = strrchr($info['basename'], '.');

    if ($ret) {
        $lower && $ret = strtolower($ret);
        if (!$with_dot) {
            $ret = ltrim($ret, '.');
        }
    }

    return ($ret != '' && $ret != '.') ? $ret : null;
}

/**
 * Aliases.
 */
function filepath(...$args) { return file_path(...$args); }
function filename(...$args) { return file_name(...$args); }
function filemime(...$args) { return file_mime(...$args); }

/**
 * Make a file.
 *
 * @param  string $file
 * @param  int    $mode
 * @param  bool   $check
 * @return bool
 * @since  6.0
 */
function filemake(string $file, int $mode = 0644, bool $check = true): bool
{
    $file = get_real_path($file);
    if (!$file) {
        trigger_error(sprintf('%s(): No file given', __function__));
        return false;
    }

    // Check existence.
    if ($check && file_exists($dir)) {
        return true;
    }

    return touch($file) && chmod($file, $mode);
}

/**
 * Read all contents a file handle without modifing seek offset.
 *
 * @alias file_read_stream()
 * @since 5.0
 */
function freadall(&$fp): string|null
{
    return file_read_stream($fp);
}

/**
 * Reset a file handle contents & set seek position to top.
 *
 * @alias stream_set_contents()
 * @since 4.0
 */
function freset(&$fp, string $contents): int|null
{
    return stream_set_contents($fp, $contents);
}

/**
 * Get a file handle metadata.
 *
 * @param  resource $fp
 * @return array|null
 * @since  4.0
 */
function fmeta($fp): array|null
{
    return stream_get_meta_data($fp) ?: null;
}

/**
 * Get a file handle size.
 *
 * @param  resource $fp
 * @return int|null
 * @since  5.0
 */
function fsize($fp): int|null
{
    return fstat($fp)['size'] ?? null;
}

/**
 * Get a directory size.
 *
 * @param  string $dir
 * @param  bool   $deep
 * @return int|null
 * @since  5.0
 */
function dirsize(string $dir, bool $deep = true): int|null
{
    $dir = realpath($dir);
    if (!$dir) {
        return null;
    }

    $ret = 0;

    foreach (glob(chop($dir, '/') . '/*') as $path) {
        is_file($path) && $ret += filesize($path);
        if ($deep) {
            is_dir($path) && $ret += dirsize($path, $deep);
        }
    }

    return $ret;
}

/**
 * Make a directory.
 *
 * @param  string $dir
 * @param  int    $mode
 * @param  bool   $recursive
 * @param  bool   $check
 * @return bool
 * @since  6.0
 */
function dirmake(string $dir, int $mode = 0755, bool $recursive = true, bool $check = true): bool
{
    $dir = get_real_path($dir);
    if (!$dir) {
        trigger_error(sprintf('%s(): No directory given', __function__));
        return false;
    }

    // Check existence.
    if ($check && file_exists($dir)) {
        return true;
    }

    return mkdir($dir, $mode, $recursive);
}

/**
 * Reset a file/stream handle contents setting seek position to top.
 *
 * @param  resource &$handle
 * @param  string    $contents
 * @return int|null
 * @since  4.0
 */
function stream_set_contents(&$handle, string $contents): int|null
{
    // Since handle stat size also pointer position is not changing even after ftruncate() for
    // files (not "php://temp" etc), we rewind the handle. Without this, stats won't be resetted!
    rewind($handle);

    // Empty, write & rewind.
    ftruncate($handle, 0);
    $ret = fwrite($handle, $contents);
    rewind($handle);

    return ($ret !== false) ? $ret : null;
}
