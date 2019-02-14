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

namespace froq\util\traits;

/**
 * Property trait.
 * @package froq\util\traits
 * @object  froq\util\traits\PropertyTrait
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   3.0
 */
trait PropertyTrait
{
    /**
     * Strict.
     * @var bool
     */
    private static $___strict;

    /**
     * Properties.
     * @var array
     */
    private static $___properties = [];

    /**
     * Properties type.
     * @var array
     */
    private static $___propertiesType = [
        'int'   => 'integer',
        'float' => 'double',
        'bool'  => 'boolean'
    ];

    /**
     * Strict.
     * @param  bool $strict
     * @return void
     */
    public static function ___strict(bool $strict): void
    {
        self::$___strict = $strict;
    }

    /**
     * Check property entry.
     * @param  string $name
     * @return void
     * @throws froq\util\traits\PropertyTraitException
     */
    private function ___checkPropertyEntry(string $name): void
    {
        if (!isset(self::$___properties[$name])) {
            try {
                // get type from docs, and strict status from self $___strict or class file
                $ref = new \ReflectionProperty($this, $name);
                $refDoc = $ref->getDocComment() ?: '';
                $refFile = $ref->getDeclaringClass()->getFileName();

                $type = null;
                $strict = self::$___strict !== null ? self::$___strict : null;
                $nullable = $this->{$name} === null;

                // get type
                if (strpos($refDoc, '@var')) {
                    $type = preg_replace('~.+@(?:var)\s+(\w+).+~is', '\1', $refDoc);
                    if (isset(self::$___propertiesType[$type])) {
                        $type = self::$___propertiesType[$type];
                    }
                }

                // get strict
                if ($strict === null) {
                    foreach (new \SplFileObject($refFile) as $line) {
                        $line = trim($line);
                        // skip comments
                        if (strpbrk($line, '/*#') !== false) {
                            continue;
                        }
                        // expects right declare() notation, sorry..
                        if (strpos($line, 'declare(strict_types=1)') !== false) {
                            $strict = true;
                            break;
                        }
                    }
                }

                // add entry
                self::$___properties[$name] = ['type' => $type, 'strict' => (bool) $strict,
                    'nullable' => $nullable];
            } catch (\ReflectionException $e) {
                throw new PropertyTraitException($e->getMessage(), $e->getCode());
            }
        }
    }
}
