<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\UtilException;
use froq\logger\Logger;
use froq\App;

// Check dependencies (all others already come with froq\App).
if (!class_exists(App::class, false)) {
    throw new UtilException("Logger sugars dependent to 'froq' module but not found");
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
 * Log error.
 * @param  string|Throwable $message
 * @param  bool             $separate
 * @return bool
 */
function log_error($message, $separate): bool
{
    return app()->logger()->logError($message, $separate);
}

/**
 * Log warn.
 * @param  string|Throwable $message
 * @param  bool             $separate
 * @return bool
 */
function log_warn($message, $separate): bool
{
    return app()->logger()->logWarn($message, $separate);
}

/**
 * Log info.
 * @param  string|Throwable $message
 * @param  bool             $separate
 * @return bool
 */
function log_info($message, $separate): bool
{
    return app()->logger()->logInfo($message, $separate);
}

/**
 * Log debug.
 * @param  string|Throwable $message
 * @param  bool             $separate
 * @return bool
 */
function log_debug($message, $separate): bool
{
    return app()->logger()->logDebug($message, $separate);
}
