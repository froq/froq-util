<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\System;

/**
 * Create a DateTime instance with/without when & where options.
 *
 * @param  string|int|float|null $when
 * @param  string|null           $where
 * @return DateTime
 */
function datetime(string|int|float $when = null, string $where = null): DateTime
{
    $when  ??= '';
    $where ??= System::defaultTimezone();

    switch (get_type($when)) {
        case 'string': // Eg: 2012-09-12 23:42:53
            return new DateTime($when, new DateTimeZone($where));
        case 'int':    // Eg: 1603339284
            $date = DateTime::createFromFormat('U', sprintf('%010d', $when));
            return $date->setTimezone(new DateTimeZone($where));
        case 'float':  // Eg: 1603339284.221243
            $date = DateTime::createFromFormat('U.u', sprintf('%.6F', $when));
            return $date->setTimezone(new DateTimeZone($where));
    }
}

/**
 * Get current Unix timestamp with milliseconds.
 *
 * @param  bool $string
 * @return int
 * @since  7.0
 */
function millitime(): int
{
    return intval(microtime(true) * 1000);
}

/**
 * Get current Unix timestamp with microseconds as float or string.
 *
 * @param  bool $string
 * @return float|string
 * @since  4.0
 */
function utime(bool $string = false): float|string
{
    $time = microtime(true);

    return !$string ? $time : sprintf('%.6F', $time);
}

/**
 * Get current Unix timestamp with milliseconds as int or string.
 *
 * @param  bool $string
 * @return int|string
 * @since  5.0
 */
function ustime(bool $string = false): int|string
{
    $time = millitime();

    return !$string ? $time : sprintf('%-013s', $time);
}

/**
 * Get an interval by given format.
 *
 * @param  string          $format
 * @param  string|int|null $time
 * @return int|null
 * @since  4.0
 */
function strtoitime(string $format, string|int $time = null): int|null
{
    // Eg: "1 day" or "1D" (instead "60*60*24" or "86400").
    if (preg_match_all('~([+-]?\d+)([YMDhms])~', $format, $match)) {
        [, $nums, $specs] = $match;

        $formats = null;

        foreach ($specs as $i => $spec) {
            $formats[] = match ($spec) {
                'Y' => $nums[$i] . ' year',
                'M' => $nums[$i] . ' month',
                'D' => $nums[$i] . ' day',
                'h' => $nums[$i] . ' hour',
                'm' => $nums[$i] . ' minute',
                's' => $nums[$i] . ' second',
            };
        }

        $formats && $format = join(' ', $formats);
    }

    $time ??= time();
    if (is_string($time)) {
        $time = strtotime($time);
    }

    $base = strtotime($format, $time);

    return ($base !== false) ? $base - $time : null;
}

/**
 * Verify a date by given format.
 *
 * @param  string $date
 * @param  string $format
 * @return bool
 * @since  4.0
 */
function date_verify(string $date, string $format): bool
{
    return ($d = date_create_from_format($format, $date))
        && ($d->format($format) === $date);
}
