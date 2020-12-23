<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

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
