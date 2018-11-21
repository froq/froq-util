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
 * @object     Froq\Util\Strings
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final /* static */ class Strings
{
    /**
     * Contains.
     * @param  string $source
     * @param  string $search
     * @param  int    $offset
     * @param  bool   $isCaseInsensitive
     * @return bool
     */
    public static function contains(string $source, string $search, int $offset = 0,
        bool $isCaseInsensitive = false): bool
    {
        return ($isCaseInsensitive ? stripos($source, $search, $offset)
            : strpos($source, $search, $offset)) !== false;
    }

    /**
     * Starts with.
     * @param  string $source
     * @param  string $search
     * @return bool
     */
    public static function startsWith(string $source, string $search): bool
    {
        return ($search === substr($source, 0, strlen($search)));
    }

    /**
     * Ends with.
     * @param  string $source
     * @param  string $search
     * @return bool
     */
    public static function endsWith(string $source, string $search): bool
    {
        return ($search === substr($source, -strlen($search)));
    }
}
