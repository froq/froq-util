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
 * @object     Froq\Util\Iter
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final class Iter implements Interfaces\Arrayable
{
    /**
     * Data.
     * @var array
     */
    private $data = [];

    /**
     * Constructor.
     * @param iter $data
     * @param bool $convert
     */
    public function __construct($data = null, bool $convert = true)
    {
        if ($data != null) {
            $this->data = to_iter_array($data);
        }
    }

    /**
     * To array.
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * To object.
     * @return \stdClass
     */
    public function toObject(): \stdClass
    {
        return (object) $this->data;
    }

    /**
     * Empty.
     * @return void
     */
    public function empty(): void
    {
        $this->data = [];
    }

    /**
     * Is empty.
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Keys.
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Values.
     * @return array
     */
    public function values(): array
    {
        return array_values($this->data);
    }

    /**
     * Count.
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get iterator.
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }
}
