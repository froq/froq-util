<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

namespace froq\util\objects;

/**
 * Pool.
 * @package froq\util\objects
 * @object  froq\util\objects\Pool
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   3.0
 */
abstract class Pool
{
    /**
     * Objects.
     * @var array
     */
    private $objects = [];

    /**
     * Has.
     * @param  int|string|object $id
     * @return bool
     */
    public final function has($id): bool
    {
        if (is_object($id)) {
            $id = spl_object_hash($id);
            foreach ($this->objects as $object) {
                if ($object[0] == $id) { return true; }
            }
            return false;
        }

        return isset($this->objects[$id]);
    }

    /**
     * Set.
     * @param  int|string $id
     * @param  object     $object
     * @return void
     */
    public final function set($id, object $object): void
    {
        $this->objects[$id] = [spl_object_hash($object), $object];
    }

    /**
     * Get.
     * @param  int|string|object $id
     * @return ?object
     */
    public final function get($id): ?object
    {
        if (is_object($id)) {
            $id = spl_object_hash($id);
            foreach ($this->objects as $object) {
                if ($object[0] == $id) { return $object[1]; }
            }
            return null;
        }

        return $this->objects[$id][1] ?? null;
    }

    /**
     * Remove.
     * @param  int|string|object $id
     * @return void
     */
    public final function remove($id): void
    {
        if (is_object($id)) {
            $id = spl_object_hash($id);
            foreach ($this->objects as $i => $object) {
                if ($object[0] == $id) {
                    unset($this->objects[$i]);
                    break;
                }
            }
        } else {
            unset($this->objects[$id]);
        }
    }

    /**
     * Add.
     * @param  int|string $id
     * @param  object     $object
     * @return void
     * @throws froq\util\objects\PoolException
     */
    public final function add($id, object $object): void
    {
        if ($this->has($id)) {
            throw new PoolException("Object already in pool with id '{$id}'");
        }

        $this->set($id, $object);
    }

    /**
     * Size.
     * @return int
     */
    public final function size(): int
    {
        return count($this->objects);
    }

    /**
     * Create object.
     * @param  int|string $id
     * @return any
     */
    abstract public function createObject($id);
}
