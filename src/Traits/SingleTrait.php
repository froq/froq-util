<?php
/**
 * Copyright (c) 2015 Kerem Güneş
 *
 * MIT License <https://opensource.org/licenses/mit>
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
 * @object     Froq\Util\Traits\SingleTrait
 * @author     Kerem Güneş <k-gun@mail.com>
 */
trait SingleTrait
{
    // Notice: Do not define '__construct' or '__clone'
    // methods as public if you want a single use'r object.

    /**
     * Instances.
     * @var array
     */
    private static $__instances = [];

    /**
     * Forbids.
     */
    private function __clone() {}
    private function __construct() {}

    /**
     * Init.
     * @param  ... $arguments
     * @return object
     */
    public static final function init(...$arguments): object
    {
        $className = get_called_class();
        if (!isset(self::$__instances[$className])) {
            // init without constructor
            $classInstance = (new \ReflectionClass($className))->newInstanceWithoutConstructor();

            // call constructor with initial arguments
            call_user_func_array([$classInstance, '__construct'], $arguments);

            self::$__instances[$className] = $classInstance;
        }

        return self::$__instances[$className];
    }
}

