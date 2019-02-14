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

use froq\util\UtilException;
use froq\util\interfaces\Arrayable;

/**
 * Generator.
 * @package froq\util\objects
 * @object  froq\util\objects\Generator
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   3.0
 */
final class Generator implements Arrayable, \Countable, \IteratorAggregate
{
    /**
     * Data.
     * @var array
     */
    private $data = [];

    /**
     * Data count.
     * @var int
     */
    private $dataCount = 0;

    /**
     * Constructor.
     * @param  iterable|array|object $data
     * @throws froq\util\UtilException
     */
    public function __construct($data)
    {
        if ($data instanceof \Traversable) {
            $data = iterator_to_array($data);
        } elseif (is_array($data)) {
            $data = (array) $data;
        } elseif (is_object($data)) {
            $data = method_exists($data, 'toArray') ? $data->toArray() : get_object_vars($data);
        } else {
            throw new UtilException('Given data is not iterable');
        }

        $this->data = $data;
        $this->dataCount = count($data);
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
     * @inheritDoc froq\util\interfaces\Arrayable
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc \Countable
     */
    public function count(): int
    {
        return $this->dataCount;
    }

    /**
     * @inheritDoc \IteratorAggregate
     */
    public function getIterator(): \Generator
    {
        foreach ($this->data as $key => $value) {
            yield [$key, $value];
        }
    }
}
