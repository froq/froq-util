<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

use Throwable;

/**
 * Console class for logging, imitates `error_log()` function.
 *
 * @package froq\util
 * @class   froq\util\Console
 * @author  Kerem Güneş
 * @since   7.0
 * @static
 */
class Console
{
    /**
     * Colors (light).
     * @see https://misc.flogisoft.com/bash/tip_colors_and_formatting
     */
    public const COLOR_BLACK     = "\e[30m",
                 COLOR_WHITE     = "\e[97m",
                 COLOR_GREEN     = "\e[92m",
                 COLOR_YELLOW    = "\e[93m",
                 COLOR_ORANGE    = "\e[38;5;208m",
                 COLOR_RED       = "\e[91m",
                 COLOR_BOLD      = "\e[1m",
                 COLOR_BOLD_END  = "\e[22m",
                 COLOR_ULINE_    = "\e[4m",
                 COLOR_ULINE_END = "\e[24m",
                 COLOR_RESET     = "\e[0m",
                 COLOR_DEFAULT   = "\e[39m";

    /**
     * Log.
     *
     * @param  string|Throwable $input
     * @param  bool             $dated
     * @return void
     */
    public static function log(string|Throwable $input, bool $dated = false): void
    {
        $log = self::prepareLog($input);

        if ($dated) {
            // Compatable with CLI Server logging.
            $log = self::prepareDate() . ' ' . $log;
        }

        self::sendLog($log, 'stdout', self::COLOR_DEFAULT);
    }

    /**
     * Log okay.
     *
     * @param  string|Throwable $input
     * @param  bool             $dated
     * @return void
     */
    public static function okay(string|Throwable $input, bool $dated = false): void
    {
        $log = self::prepareLog($input);

        if ($dated) {
            // Compatable with CLI Server logging.
            $log = self::prepareDate() . ' Okay: ' . $log;
        } else {
            $log = 'Okay: ' . $log;
        }

        self::sendLog($log, 'stdout', self::COLOR_GREEN);
    }

    /**
     * Log info.
     *
     * @param  string|Throwable $input
     * @param  bool             $dated
     * @return void
     */
    public static function info(string|Throwable $input, bool $dated = false): void
    {
        $log = self::prepareLog($input);

        if ($dated) {
            // Compatable with CLI Server logging.
            $log = self::prepareDate() . ' Info: ' . $log;
        } else {
            $log = 'Info: ' . $log;
        }

        self::sendLog($log, 'stdout', self::COLOR_YELLOW);
    }

    /**
     * Log warn.
     *
     * @param  string|Throwable $input
     * @param  bool             $dated
     * @return void
     */
    public static function warn(string|Throwable $input, bool $dated = false): void
    {
        $log = self::prepareLog($input);

        if ($dated) {
            // Compatable with CLI Server logging.
            $log = self::prepareDate() . ' Warning: ' . $log;
        } else {
            $log = 'Warning: ' . $log;
        }

        self::sendLog($log, 'stdout', self::COLOR_ORANGE);
    }

    /**
     * Log error.
     *
     * @param  string|Throwable $input
     * @param  bool             $dated
     * @return void
     */
    public static function error(string|Throwable $input, bool $dated = false): void
    {
        $log = self::prepareLog($input);

        if ($dated) {
            // Compatable with CLI Server logging.
            $log = self::prepareDate() . ' Error: ' . $log;
        } else {
            $log = 'Error: ' . $log;
        }

        self::sendLog($log, 'stderr', self::COLOR_RED);
    }

    /**
     * Log JSON.
     *
     * @param  mixed $input
     * @return void
     */
    public static function json(mixed $input): void
    {
        if ($input instanceof Throwable) {
            $debug = Debugger::debug($input, withTracePath: true);
            $debug['trace'] = $debug['tracePath'];
            unset($debug['tracePath']);
            $input = $debug;
        }

        $log = self::prepareLog(json_serialize($input, 2), true);

        self::sendLog($log, 'stdout');
    }

    /**
     * Print log.
     *
     * @param  string      $log
     * @param  string      $type
     * @param  string|null $color
     * @param  string|null $colorEnd
     * @return void
     */
    public static function sendLog(string $log, string $type, string $color = null, string $colorEnd = null): void
    {
        if (isset($color) && !isset($colorEnd)) {
            $colorEnd = self::COLOR_RESET;
        }

        $log = $color . $log . $colorEnd . PHP_EOL;

        // // No color for Sublime Text.
        // if ($sublime = getenv('SSL_CERT_FILE', true)) {
        //     $sublime = str_contains($sublime, 'sublime_text');
        //     if ($sublime) {
        //         $log = $log . PHP_EOL;
        //     }
        // }

        // No print for none-CLI environments.
        if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'cli-server') {
            error_log($log);
            return;
        }

        $typeConstant = strtoupper($type);
        $typeResource = strtolower($type);

        static $resource;

        // I/O stream constants.
        // @see https://www.php.net/manual/en/features.commandline.io-streams.php
        if (defined($typeConstant)) {
            $resource = constant($typeConstant);
        }
        if (!is_resource($resource)) {
            $resource = fopen('php://'. $typeResource, 'w');
        }

        fwrite($resource, $log);
    }

    /**
     * Prepare log.
     *
     * @param  string|Throwable $input
     * @return string
     */
    public static function prepareLog(string|Throwable $input): string
    {
        if ($input instanceof Throwable) {
            return Debugger::debugString($input);
        }

        return $input;
    }

    /**
     * Prepare date.
     *
     * @return string
     */
    public static function prepareDate(): string
    {
        static $timezone;

        if (!$timezone) {
            // CLI Server uses machine's timezone.
            if (PHP_SAPI === 'cli-server') {
                try {
                    $result   = System::exec('cat /etc/timezone');
                    $timezone = $result->return ?: null;
                } catch (Throwable) {}
            }

            $timezone ??= date_default_timezone_get();
        }

        $datetime = new \DateTime('', new \DateTimeZone($timezone));

        // Use local date like CLI Server.
        return $datetime->format('[D M j H:i:s Y]');
    }
}
