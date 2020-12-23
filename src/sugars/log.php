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
    throw new UtilException('Logger sugars dependent to `froq` module but not found');
}

/**
 * Get app's logger.
 *
 * @return froq\logger\Logger.
 */
function logger(): Logger
{
    return app()->logger();
}

/**
 * Log an error message.
 *
 * @param  string|Throwable $message
 * @param  bool             $separate
 * @return bool
 */
function log_error(string|Throwable $message, bool $separate = true): bool
{
    return app()->logger()->logError($message, $separate);
}

/**
 * Log a warning message.
 *
 * @param  string|Throwable $message
 * @param  bool             $separate
 * @return bool
 */
function log_warn(string|Throwable $message, bool $separate = true): bool
{
    return app()->logger()->logWarn($message, $separate);
}

/**
 * Log an info message.
 *
 * @param  string|Throwable $message
 * @param  bool             $separate
 * @return bool
 */
function log_info(string|Throwable $message, bool $separate = true): bool
{
    return app()->logger()->logInfo($message, $separate);
}

/**
 * Log a debug message.
 *
 * @param  string|Throwable $message
 * @param  bool             $separate
 * @return bool
 */
function log_debug(string|Throwable $message, bool $separate = true): bool
{
    return app()->logger()->logDebug($message, $separate);
}
