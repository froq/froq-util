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

namespace Froq\Util\Interfaces;

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Interfaces\Arrayable
 * @author     Kerem Güneş <k-gun@mail.com>
 */
interface Arrayable extends \Countable, \IteratorAggregate
{
    /**
     * Keys.
     * @return array
     */
    public function keys(): array;

    /**
     * Values.
     * @return array
     */
    public function values(): array;

    /**
     * Is empty.
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * To array.
     * @return array
     */
    public function toArray(): array;
}
