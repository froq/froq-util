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

use froq\App;
use froq\logger\Logger;
use froq\util\UtilException;

// Check dependencies (all others already come with froq\App).
if (!class_exists(App::class, false)) {
    throw new UtilException('Logger sugars dependent to "froq" module that not found');
}

/**
 * Logger.
 * @return froq\logger\Logger.
 */
function logger(): Logger
{
    return app()->logger();
}

/**
 * Log fail.
 * @param  any $message
 * @return bool
 */
function log_fail($message): bool
{
    return app()->logger()->logFail($message);
}

/**
 * Log warn.
 * @param  any $message
 * @return bool
 */
function log_warn($message): bool
{
    return app()->logger()->logWarn($message);
}

/**
 * Log info.
 * @param  any $message
 * @return bool
 */
function log_info($message): bool
{
    return app()->logger()->logInfo($message);
}

/**
 * Log debug.
 * @param  any $message
 * @return bool
 */
function log_debug($message): bool
{
    return app()->logger()->logDebug($message);
}
