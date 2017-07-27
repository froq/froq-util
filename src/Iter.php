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

use \stdClass as object;

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
     * @param iter|null $data
     */
    public function __construct($data = null)
    {
        if (!is_iter($data)) {
            throw new UtilException('Given data is not iterable!');
        }

        if (is_array($data) || $data instanceof object) {
            $data = (array) $data;
        } elseif ($data instanceof \Traversable) {
            $data = iterator_to_array($data);
        } elseif (is_object($data)) {
            $data = method_exists($data, 'toArray') ? $data->toArray() : get_object_vars($data);
        }

        $this->data = $data;
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
     * To array.
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
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

    /**
     * Empty.
     * @return void
     */
    public function empty(): void
    {
        $this->data = [];
    }
}
