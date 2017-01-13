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

/*** All function module. ***/

/**
 * Just for fun.
 * @const null
 */
if (!defined('nil')) {
    define('nil', null, true);
}

/**
 * More readable empty strings.
 * @const string
 */
if (!defined('none')) {
    define('none', '', true);
}

/**
 * Used to detect local env.
 * @const bool
 */
if (!defined('local')) {
    define('local', (isset($_SERVER['SERVER_NAME'])
        && !!preg_match('~\.local$~i', $_SERVER['SERVER_NAME'])), true);
}

// include all files
$files = glob(__dir__ .'/*.php');
foreach ($files as $file) {
    if ($file <> __file__) {
        include($file);
    }
}
unset($files, $file);
