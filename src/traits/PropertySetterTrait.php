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
 * Property setter trait.
 * @package froq\util\traits
 * @object  froq\util\traits\PropertySetterTrait
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 */
trait PropertySetterTrait
{
    /*** Notice: Do not define '__set' in use'r object. ***/

    /**
     * Set magic.
     * @param  string $name
     * @param  any    $value
     * @return void
     * @throws froq\util\traits\PropertyTraitException
     */
    public function __set(string $name, $value)
    {
        // check property entry
        $this->___checkPropertyEntry($name);

        $nullable = self::$___properties[$name]['nullable'];
        if (!$nullable) {
            $type = self::$___properties[$name]['type'];
            if ($type != null) {
                $valueType = gettype($value);
                if ($type != $valueType) {
                    // check strict status
                    $strict = self::$___properties[$name]['strict'];
                    if ($strict) {
                        throw new PropertyTraitException(sprintf('%s type has not valid type for '.
                            '%s::$%s, %s expected', $valueType, static::class, $name, $type));
                    }
                    // or simple type cast
                    settype($value, $type);
                }
            }
        }

        $this->{$name} = $value;
    }
}
