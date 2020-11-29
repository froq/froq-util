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
 * @return bool
 */
function log_error($message): bool
{
    return app()->logger()->logError($message);
}

/**
 * Log warn.
 * @param  string|Throwable $message
 * @return bool
 */
function log_warn($message): bool
{
    return app()->logger()->logWarn($message);
}

/**
 * Log info.
 * @param  string|Throwable $message
 * @return bool
 */
function log_info($message): bool
{
    return app()->logger()->logInfo($message);
}

/**
 * Log debug.
 * @param  string|Throwable $message
 * @return bool
 */
function log_debug($message): bool
{
    return app()->logger()->logDebug($message);
}
