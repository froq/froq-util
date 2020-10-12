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

namespace froq\util;

use froq\common\objects\StaticClass;
use froq\common\exceptions\InvalidArgumentException;
use Error, Reflection, ReflectionException, ReflectionClass, ReflectionProperty;

/**
 * Objects.
 * @package froq\util
 * @object  froq\util\Objects
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 * @static
 */
final class Objects extends StaticClass
{
    /**
     * Has constant.
     * @param  string|object $class
     * @param  string        $name
     * @return ?bool
     */
    public static function hasConstant($class, string $name): ?bool
    {
        try {
            return (new ReflectionClass($class))->hasConstant($name);
        } catch (ReflectionException $e) {
            return null;
        }
    }

    /**
     * Has property.
     * @param  string|object $class
     * @param  string        $name
     * @return ?bool
     */
    public static function hasProperty($class, string $name): ?bool
    {
        try {
            return (new ReflectionClass($class))->hasProperty($name);
        } catch (ReflectionException $e) {
            return null;
        }
    }

    /**
     * Get reflection.
     * @param  string|object $class
     * @return ?ReflectionClass
     */
    public static function getReflection($class): ?ReflectionClass
    {
        try {
            return new ReflectionClass($class);
        } catch (ReflectionException $e) {
            return null;
        }
    }

    /**
     * Get id.
     * @param  object $object
     * @param  bool   $withName
     * @return string
     */
    public static function getId(object $object, bool $withName = true): string
    {
        return $withName ? get_class($object) .'#'. spl_object_id($object)
                         : spl_object_id($object) .'';
    }

    /**
     * Get hash.
     * @param  object $object
     * @param  bool   $withName
     * @param  bool   $withRehash
     * @return string
     */
    public static function getHash(object $object, bool $withName = true, bool $withRehash = true): string
    {
        $hash = spl_object_hash($object);
        if ($withRehash) {
            $hash = hash('crc32', $hash); // Pack "000..." stuff.
        }
        return $withName ? get_class($object) .'#'. $hash : $hash;
    }

    /**
     * Get serialized hash.
     * @param  object      $object
     * @param  string|null $algo
     * @return string
     */
    public static function getSerializedHash(object $object, string $algo = null): string
    {
        return hash($algo ?: 'crc32',
            // For a unique hash, use id & hash.
            spl_object_id($object) . spl_object_hash($object) . serialize($object));
    }

    /**
     * Get name.
     * @param  string|object $class
     * @return string
     * @throws froq\common\exceptions\InvalidArgumentException
     */
    public static function getName($class): string
    {
        if (is_string($class)) return $class;
        if (is_object($class)) return get_class($class);

        throw new InvalidArgumentException('Invalid $class argument, string and object arguments '.
            'accepted only, "%s" given', [gettype($class)]);
    }

    /**
     * Get short name.
     * @param  string|object $class
     * @return string
     */
    public static function getShortName($class): string
    {
        $name = self::getName($class);
        $npos = strrpos($name, '\\');

        return substr($name, ($npos !== false) ? $npos + 1 : 0);
    }

    /**
     * Get namespace.
     * @param  string|object $class
     * @param  bool          $baseOnly
     * @return string
     */
    public static function getNamespace($class, bool $baseOnly = false): string
    {
        $name = self::getName($class);
        $npos = !$baseOnly ? strrpos($name, '\\') : strpos($name, '\\');

        return substr($name, 0, ($npos !== false) ? $npos : 0);
    }

    /**
     * Get constant.
     * @param  string|object $class
     * @param  string        $name
     * @return ?array
     */
    public static function getConstant($class, string $name): ?array
    {
        return self::getConstants($class, true, $name)[$name] ?? null;
    }

    /**
     * Get constant value.
     * @param  string|object $class
     * @param  string        $name
     * @return any
     */
    public static function getConstantValue($class, string $name)
    {
        return self::getConstants($class, true, $name)[$name]['value'] ?? null;
    }

    /**
     * Get constants registry.
     * @param  string|object $class
     * @param  bool          $all
     * @param  string        $_name @internal
     * @return ?array
     */
    public static function getConstants($class, bool $all = true, string $_name = null): ?array
    {
        $ref = self::getReflection($class);
        if (!$ref) {
            return null;
        }

        foreach ($ref->getReflectionConstants() as $constant) {
            if ($_name && $_name != $constant->name) {
                continue;
            }
            if (!$all && !$constant->isPublic()) {
                continue;
            }

            $interface = null;
            $class     = $constant->getDeclaringClass()->getName();

            // Simply interface check for real definer.
            if (interface_exists($class, false)) {
                $interface = $class;
                $class     = $constant->class;
            }

            // Nope..
            // if ($interfaces = self::getInterfaces($constant->class)) {
            //     foreach ($interfaces as $interfaceName) {
            //         // Far enough, cos interfaces don't allow constant invisibility.
            //         if (defined($interfaceName .'::'. $constant->name)) {
            //             // Break, cos interfaces don't allow constant overriding.
            //             $interface = $interfaceName;
            //             break;
            //         }
            //     }
            // }

            // Sorry, but no method such getType()..
            preg_match('~\[ (?<visibility>\w+) (?<type>\w+) .+ \]~', $constant->__toString(), $match);

            // Using name as key, since all names will be overridden internally in children.
            $ret[$constant->name] = [
                'value'      => $constant->getValue(),
                'type'       => $match['type'],
                'visibility' => $match['visibility'],
                'class'      => $class,
                'interface'  => $interface
            ];
        }

        return $ret ?? [];
    }

    /**
     * Get constant names.
     * @param  string|object $class
     * @param  bool          $all
     * @return ?array
     */
    public static function getConstantNames($class, bool $all = true): ?array
    {
        $ref = self::getReflection($class);
        if (!$ref) {
            return null;
        }

        if ($all) {
            // Seems doesn't matter constant visibility for getConstants().
            $ret = array_keys($ref->getConstants());
        } else {
            foreach ($ref->getReflectionConstants() as $constant) {
                if ($constant->isPublic()) {
                    $ret[] = $constant->name;
                }
            }
        }

        return $ret ?? [];
    }

    /**
     * Get constant values.
     * @param  string|object $class
     * @param  bool          $all
     * @param  bool          $withNames
     * @return ?array
     */
    public static function getConstantValues($class, bool $all = true, bool $withNames = false): ?array
    {
        $ref = self::getReflection($class);
        if (!$ref) {
            return null;
        }

        if ($all) {
            // Seems doesn't matter constant visibility for getConstants().
            $ret = !$withNames ? array_values($ref->getConstants())
                               : $ref->getConstants();
        } else {
            foreach ($ref->getReflectionConstants() as $i => $constant) {
                if ($constant->isPublic()) {
                    !$withNames ? $ret[$i] = $constant->getValue()
                                : $ret[$constant->name] = $constant->getValue();
                }
            }
        }

        return $ret ?? [];
    }

    /**
     * Get property.
     * @param  string|object $class
     * @param  string        $name
     * @return ?array
     */
    public static function getProperty($class, string $name): ?array
    {
        return self::getProperties($class, true, $name)[$name] ?? null;
    }

    /**
     * Get property value.
     * @param  string|object $class
     * @param  string        $name
     * @return any
     */
    public static function getPropertyValue($class, string $name)
    {
        return self::getProperties($class, true, $name)[$name]['value'] ?? null;
    }

    /**
     * Get properties registry.
     * @param  string|object $class
     * @param  bool          $all
     * @param  string        $_name @internal
     * @return array
     */
    public static function getProperties($class, bool $all = true, string $_name = null): array
    {
        $ref = self::getReflection($class);
        if (!$ref) {
            return null;
        }

        // Collect & merge with values (ReflectionProperty gives null for non-initialized classes).
        $properties = array_replace(
            array_reduce($ref->getProperties(),
                fn($ps, $p) => array_merge($ps, [$p->name => null])
            , [])
        , $ref->getDefaultProperties());

        foreach ($properties as $name => $value) {
            if ($_name && $_name != $name) {
                continue;
            }

            $property = new ReflectionProperty($class, $name);
            if (!$all && !$property->isPublic()) {
                continue;
            }

            $visibility = $property->isPublic() ? 'public' : (
                $property->isPrivate() ? 'private' : 'protected'
            );

            $modifiers = ($mods = $property->getModifiers())
                ? join(' ', Reflection::getModifierNames($mods)) : null;

            $type = $nullable = $trait = null;

            if ($propertyType = $property->getType()) {
                $type = $propertyType->getName();
                $nullable = $propertyType->allowsNull();
            }

            if ($traits = self::getTraits($property->class)) {
                foreach ($traits as $traitName) {
                    if (property_exists($traitName, $property->name)) {
                        // No break, cos searching the real 'define'r but not 'use'r trait.
                        $trait = $traitName;
                    }
                }
            }

            if (is_object($class)) {
                // Try, cos "Typed property $foo must not be accessed before initialization".
                try {
                    // For getValue() and others below.
                    if ($visibility != 'public') {
                        $property->setAccessible(true);
                    }
                    $value = $property->getValue($class);
                } catch (Error $e) {}

                $initialized = $property->isInitialized($class);
            }

            $nullable    ??= ($value === null);
            $initialized ??= ($value !== null);

            // Using name as key, since all names will be overridden internally in children.
            $ret[$property->name] = [
                'value'       => $value,                'type'        => $type,
                'nullable'    => $nullable ,            'visibility'  => $visibility,
                'static'      => $property->isStatic(), 'initialized' => $initialized,
                'class'       => $property->class,      'trait'       => $trait,
                'modifiers'   => $modifiers
            ];
        }

        return $ret ?? [];
    }

    /**
     * Get property names.
     * @param  string|object $class
     * @param  bool          $all
     * @return ?array
     */
    public static function getPropertyNames($class, bool $all = true): ?array
    {
        $ref = self::getReflection($class);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        foreach ($ref->getProperties($filter) as $property) {
            $ret[] = $property->name;
        }

        return $ret ?? [];
    }

    /**
     * Get property values.
     * @param  string|object $class
     * @param  bool          $all
     * @param  bool          $withNames
     * @return ?array
     */
    public static function getPropertyValues($class, bool $all = true, bool $withNames = false): ?array
    {
        $ref = self::getReflection($class);
        if (!$ref) {
            return null;
        }

        // Collect & merge with values (ReflectionProperty gives null for non-initialized classes).
        $properties = array_merge(
            array_reduce($ref->getProperties(),
                fn($ps, $p) => array_merge($ps, [$p->name => null])
            , [])
        , $ref->getDefaultProperties());

        // Used "getDefaultProperties" and "ReflectionProperty", since can not get default value
        // for string $class arguments, and also for all other stuffs like: "Typed property $foo
        // must not be accessed before initialization"..
        foreach ($properties as $name => $value) {
            $property = new ReflectionProperty($class, $name);
            if (!$all && !$property->isPublic()) {
                continue;
            }

            if (is_null($value) && is_object($class)) {
                try {
                    $property->setAccessible(true);
                    $value = $property->getValue($class);
                } catch (Error $e) {}
            }

            !$withNames ? $ret[] = $value : $ret[$name] = $value;
        }

        return $ret ?? [];
    }

    /**
     * Get method.
     * @param  string|object $class
     * @param  string        $name
     * @return ?array
     */
    public static function getMethod($class, string $name): ?array
    {
        return self::getMethods($class, true, $name)[$name] ?? null;
    }

    /**
     * Get methods.
     * @param  string|object $class
     * @param  bool          $all
     * @param  string        $_name @internal
     * @return ?array
     */
    public static function getMethods($class, bool $all = true, string $_name = null): ?array
    {
        $ref = self::getReflection($class);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        foreach ($ref->getMethods($filter) as $method) {
            if ($_name && $_name != $method->name) {
                continue;
            }

            $visibility = $method->isPublic() ? 'public' : (
                $method->isPrivate() ? 'private' : 'protected'
            );

            $modifiers = ($mods = $property->getModifiers())
                ? join(' ', Reflection::getModifierNames($mods)) : null;

            $return = $trait = $parameters = null;

            if ($method->hasReturnType()) {
                $return = $method->getReturnType()->getName();
            } elseif ($doc = $method->getDocComment()) {
                preg_match('~(?=@(returns?|alias(?:Of|For)?) *([^\s]+))~', $doc, $match);
                if ($match) {
                    $return = strpos($match[1], 'alias') > -1  // Alias stuff.
                        ? '@see '. $match[2] : $match[2];
                }
            }

            if ($traits = self::getTraits($method->class)) {
                foreach ($traits as $traitName) {
                    if (method_exists($traitName, $method->name)) {
                        // No break, cos searching the real 'define'r but not 'use'r trait.
                        $trait = $traitName;
                    }
                }
            }

            if ($params = $method->getParameters()) {
                foreach ($params as $param) {
                    $parameter = [
                        'name' => $param->name, 'value'    => 'void',
                        'type' => 'void',       'nullable' => $param->allowsNull()
                    ];

                    if ($paramType = $param->getType()) {
                        $parameter['type'] = ($parameter['nullable'])
                            ? '?'. $paramType->getName() : $paramType->getName();
                    }
                    if ($param->isVariadic()) {
                        $parameter['type'] = ($parameter['type'] != 'void')
                            ? $parameter['type'] .' ...' : '...';
                    }

                    try {
                        if ($param->isDefaultValueAvailable()) {
                            $parameter['value'] = $param->getDefaultValue();
                        } elseif ($param->isDefaultValueConstant()) {
                            $parameter['value'] = $param->getDefaultValueConstantName();
                        }
                    } catch (ReflectionException $e) {}

                    $parameters[] = $parameter;
                }
            }

            // Using method name as key, since all names will be overridden internally in children.
            $ret[$method->name] = [
                'visibility' => $visibility,         'return'     => $return,
                'final'      => $method->isFinal(),  'static'     => $method->isStatic(),
                'class'      => $method->class,      'trait'      => $trait,
                'modifiers'  => $modifiers,          'parameters' => $parameters,
            ];
        }

        return $ret ?? [];
    }

    /**
     * Get method names.
     * @param  string|object $class
     * @param  bool          $all
     * @return ?array
     */
    public static function getMethodNames($class, bool $all = true): ?array
    {
        $ref = self::getReflection($class);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        foreach ($ref->getMethods($filter) as $method) {
            $ret[] = $method->name;
        }

        return $ret ?? [];
    }

    /**
     * Get parents.
     * @param  string|object $class
     * @return ?array
     */
    public static function getParents($class): ?array
    {
        $ret =@ class_parents($class);
        if ($ret !== false) {
            $ret = array_keys($ret);
            return $ret;
        }
        return null;
    }

    /**
     * Get interfaces.
     * @param  string|object $class
     * @return ?array
     */
    public static function getInterfaces($class): ?array
    {
        $ret =@ class_implements($class);
        if ($ret !== false) {
            $ret = array_keys($ret);
            $ret = array_reverse($ret); // Fix weird reverse order..
            return $ret;
        }
        return null;
    }

    /**
     * Get traits.
     * @param  string|object $class
     * @param  bool          $all
     * @return ?array
     */
    public static function getTraits($class, bool $all = true): ?array
    {
        $ret =@ class_uses($class);
        if ($ret !== false) {
            $ret = array_keys($ret);
            if ($all) {
                $parents = self::getParents($class) ?? [];
                foreach ($parents as $parent) {
                    $ret = array_merge($ret, self::getTraits($parent) ?? []);
                }

                // Really all..
                if ($ret) {
                    foreach ($ret as $re) {
                        $ret = array_merge($ret, self::getTraits($re) ?? []);
                    }
                    $ret = array_unique($ret);
                }
            }
            return $ret;
        }
        return null;
    }
}
