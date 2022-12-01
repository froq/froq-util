<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

use froq\util\UtilException;
use froq\logger\Logger;
use froq\App;

// Check dependencies (all others already come with froq\App).
if (!class_exists(App::class, false)) {
    throw new UtilException('Logger sugars dependent to "froq" module but not found');
}

/**
 * Get app's logger.
 *
 * @return froq\logger\Logger.
 */
function logger(): Logger
{
    return app()->logger;
}

/**
 * Log an error message.
 *
 * @param  string|Stringable $message
 * @return bool
 */
function log_error(string|Stringable $message): bool
{
    return app()->logger->logError($message);
}

/**
 * Log a warning message.
 *
 * @param  string|Stringable $message
 * @return bool
 */
function log_warn(string|Stringable $message): bool
{
    return app()->logger->logWarn($message);
}

/**
 * Log an info message.
 *
 * @param  string|Stringable $message
 * @return bool
 */
function log_info(string|Stringable $message): bool
{
    return app()->logger->logInfo($message);
}

/**
 * Log a debug message.
 *
 * @param  string|Stringable $message
 * @return bool
 */
function log_debug(string|Stringable $message): bool
{
    return app()->logger->logDebug($message);
}
