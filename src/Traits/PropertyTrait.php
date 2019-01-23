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

namespace Froq\Util\Traits;

/**
 * @package    Froq
 * @subpackage Froq\Util
 * @object     Froq\Util\Traits\PropertyTrait
 * @author     Kerem Güneş <k-gun@mail.com>
 * @since      3.0
 */
trait PropertyTrait
{
    /**
     * Strict.
     * @var bool
     */
    public static $___strict;

    /**
     * Properties.
     * @var array
     */
    private static $___properties = [];

    /**
     * Properties Type.
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
     * Get property type.
     * @param  string $docComment
     * @return ?string
     */
    private function ___getPropertyType(string $docComment): ?string
    {
        if (strpos($docComment, '@var')) {
            $type = preg_replace('~.+@(?:var)\s+(\w+).+~is', '\1', $docComment);
            if (isset(self::$___propertiesType[$type])) {
                $type = self::$___propertiesType[$type];
            }
        }
        return $type ?? null;
    }

    /**
     * Get property strict status.
     * @param  string $fileName
     * @return bool
     */
    private function ___getPropertyStrictStatus(string $fileName): bool
    {
        foreach (new \SplFileObject($fileName) as $line) {
            $line = trim($line);
            // skip comments
            if (strpbrk($line, '/*#') !== false) {
                continue;
            }

            // expects right declare() notation, sorry..
            if (strpos($line, 'declare(strict_types=1)') !== false) {
                return true;
            }
        }
        return false;
    }
}
