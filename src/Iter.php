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
     * @param iter $data
     * @param bool $convert
     */
    public function __construct($data = null, bool $convert = true)
    {
        if (!is_iter($data)) {
            throw new UtilException('Given data is not iterable!');
        }

        if (is_array($arg) || $arg instanceof object) {
            $data = (array) $arg;
        } elseif ($arg instanceof \Traversable) {
            $data = iterator_to_array($arg);
        } elseif (is_object($arg)) {
            $data = method_exists($arg, 'toArray')
                ? $arg->toArray() : get_object_vars($arg);
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
     * Empty.
     * @return void
     */
    public function empty(): void
    {
        $this->data = [];
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
     * To array.
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * To object.
     * @return object
     */
    public function toObject(): object
    {
        return (object) $this->data;
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
     * Is empty.
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }
}
