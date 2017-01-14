<?php
/**
 * Copyright (c) 2016 Kerem Güneş
 *    <k-gun@mail.com>
 *
 * GNU General Public License v3.0
 *    <http://www.gnu.org/licenses/gpl-3.0.txt>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Froq\Util;

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Number
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final class Number
{
    /**
     * Compare.
     * @param  number $a
     * @param  number $b
     * @return int|false
     */
    final public static function compare($a, $b, int $precision = 0)
    {
        if (function_exists('bccomp') && is_numeric($a) && is_numeric($b)) {
            return bccomp((string) $a, (string) $b, $precision);
        }
        return false;
    }

    /**
     * Is equal.
     * @param  number $a
     * @param  number $b
     * @param  int    $precision
     * @return bool
     */
    final public static function isEqual($a, $b, int $precision = 2): bool
    {
        return (0 === self::compare($a, $b, $precision));
    }
}
