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
 * Just for fun.
 * @const null
 */
if (!defined('NIL')) {
    define('NIL', null, true);
}

/**
 * More readable empty strings.
 * @const string
 */
if (!defined('NILS')) {
    define('NILS', '', true);
}

/**
 * Used to detect local env.
 * @const bool
 */
if (!defined('LOCAL')) {
    $serverName = $_SERVER['SERVER_NAME'] ?? '';
    $serverNameExtension = substr($serverName, strrpos($serverName, '.') + 1);
    define('LOCAL', $serverNameExtension && in_array($serverNameExtension, ['local', 'localhost']), true);
    unset($serverName, $serverNameExtension);
}

/**
 * Error function (normally comes from froq/froq).
 */
if (!function_exists('error')) {
    /**
     * Error.
     * @param  bool $clear
     * @return string|null
     * @since  3.0
     */
    function error(bool $clear = false)
    {
        $error = error_get_last();

        if ($error !== null) {
            $error = preg_replace('~(?:.*?:)?.*?:\s*(.+)~', '\1', strtolower($error['message']));
            $error = $error ?: 'unknown error';
            $clear && error_clear_last();
        }

        return $error;
    }
}

// include all function files
$files = glob(__dir__ .'/functions/*.php');
foreach ($files as $file) {
    include_once $file;
}
unset($files, $file);

/**
 * Int.
 * @param  any $input
 * @return int
 * @since  3.0
 */
function int($input): int
{
    return is_numeric($input) ? intval($input) : 0;
}

/**
 * Float.
 * @param  any $input
 * @return float
 * @since  3.0
 */
function float($input): float
{
    return is_numeric($input) ? floatval($input) : 0.0;
}

/**
 * String.
 * @param  any $input
 * @return string
 * @since  3.0
 */
function string($input): string
{
    return strval($input);
}

/**
 * Bool.
 * @param  any $input
 * @return bool
 * @since  3.0
 */
function bool($input): bool
{
    $input .= ''; // to string
    return ($input === '1' || $input === '0') ? boolval($input) : false;
}

/**
 * Void.
 * @param  any &...$inputs
 * @return void
 * @since  3.0
 */
function void(&...$inputs): void
{
    foreach ($inputs as &$input) {
        $input = null;
    }
}
