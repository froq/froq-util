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
final class Iter implements \Countable, \IteratorAggregate
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
    final public function __construct($data = null, bool $convert = true)
    {
        if (!is_empty($data)) {
            $this->data = to_iter_array($data);
        }
    }

    /**
     * To array.
     * @return array
     */
    final public function toArray(): array
    {
        return $this->data;
    }

    /**
     * To object.
     * @return \stdClass
     */
    final public function toObject(): \stdClass
    {
        return (object) $this->data;
    }

    /**
     * Empty.
     * @return bool
     */
    final public function empty(): bool
    {
        return empty($this->data);
    }

    /**
     * Count.
     * @return int
     */
    final public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get iterator.
     * @return \ArrayIterator
     */
    final public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }
}
