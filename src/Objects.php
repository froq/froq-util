<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;

use froq\common\object\StaticClass;
use Error, Reflection, ReflectionException, ReflectionClass, ReflectionProperty, ReflectionUnionType;

/**
 * Objects.
 *
 * @package froq\util
 * @object  froq\util\Objects
 * @author  Kerem Güneş
 * @since   4.0
 * @static
 */
final class Objects extends StaticClass
{
    /**
     * Get reflection.
     *
     * @param  string|object $class
     * @return ReflectionClass|null
     */
    public static function getReflection(string|object $class): ReflectionClass|null
    {
        try {
            return new ReflectionClass($class);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * Get id.
     *
     * @param  object $object
     * @param  bool   $withName
     * @return string
     */
    public static function getId(object $object, bool $withName = true): string
    {
        $id = spl_object_id($object);

        return (string) ($withName ? self::getName($object) .'#'. $id : $id);
    }

    /**
     * Get hash.
     *
     * @param  object $object
     * @param  bool   $withName
     * @param  bool   $withRehash
     * @return string
     */
    public static function getHash(object $object, bool $withName = true, bool $withRehash = false): string
    {
        $hash = spl_object_hash($object);

        // Pack "000..." stuff.
        $withRehash && $hash = hash('crc32', $hash);

        return (string) ($withName ? self::getName($object) .'#'. $hash : $hash);
    }

    /**
     * Get serialized hash.
     *
     * @param  object $object
     * @return string
     */
    public static function getSerializedHash(object $object): string
    {
        return (string) hash('crc32', self::getHash($object) . serialize($object));
    }

    /**
     * Get name.
     *
     * @param  string|object $class
     * @return string
     */
    public static function getName(string|object $class): string
    {
        $name = is_string($class) ? $class : $class::class;

        if (str_contains($name, '@')) {
            $name = preg_replace('~(.+)@anonymous\0*(.+)\:(.+)\$.*~i', '\1@anonymous@\2:\3', $name);
        }

        return $name;
    }

    /**
     * Get short name.
     *
     * @param  string|object $class
     * @return string
     */
    public static function getShortName(string|object $class): string
    {
        $name = self::getName($class);
        $spos = strrpos($name, '\\');

        return substr($name, ($spos !== false) ? $spos + 1 : 0);
    }

    /**
     * Get namespace.
     *
     * @param  string|object $class
     * @param  bool          $baseOnly
     * @return string
     */
    public static function getNamespace(string|object $class, bool $baseOnly = false): string
    {
        $name = self::getName($class);
        $spos = !$baseOnly ? strrpos($name, '\\') : strpos($name, '\\');

        return substr($name, 0, ($spos !== false) ? $spos : 0);
    }

    /**
     * Has constant.
     *
     * @param  string|object $class
     * @param  string        $name
     * @return bool|null
     */
    public static function hasConstant(string|object $class, string $name): bool|null
    {
        try {
            return (new ReflectionClass($class))->hasConstant($name);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * Get constant.
     *
     * @param  string|object $class
     * @param  string        $name
     * @return array|null
     */
    public static function getConstant(string|object $class, string $name): array|null
    {
        return self::getConstants($class, true, $name)[$name] ?? null;
    }

    /**
     * Get constant value.
     *
     * @param  string|object $class
     * @param  string        $name
     * @return any
     */
    public static function getConstantValue(string|object $class, string $name)
    {
        return self::getConstants($class, true, $name)[$name]['value'] ?? null;
    }

    /**
     * Get constants.
     *
     * @param  string|object $class
     * @param  bool          $all
     * @param  string        $_name @internal
     * @return array|null
     */
    public static function getConstants(string|object $class, bool $all = true, string $_name = null): array|null
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

            $modifiers = Reflection::getModifierNames($constant->getModifiers());

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
            preg_match('~\[ (?<visibility>\w+) (?<type>\w+) .+ \]~', (string) $constant, $match);

            $className = self::getName($class);

            // Using name as key, since all names will be overridden internally in children.
            $ret[$constant->name] = [
                'name'       => $constant->name,
                'value'      => $constant->getValue(), 'type'      => $match['type'],
                'class'      => $className,            'interface' => $interface,
                'visibility' => $match['visibility'],  'modifiers' => $modifiers,
            ];
        }

        return $ret ?? [];
    }

    /**
     * Get constant names.
     *
     * @param  string|object $class
     * @param  bool          $all
     * @return array|null
     */
    public static function getConstantNames(string|object $class, bool $all = true): array|null
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
     *
     * @param  string|object $class
     * @param  bool          $all
     * @param  bool          $withNames
     * @return array|null
     */
    public static function getConstantValues(string|object $class, bool $all = true, bool $withNames = false): array|null
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
            foreach ($ref->getReflectionConstants() as $constant) {
                if ($constant->isPublic()) {
                    !$withNames ? $ret[] = $constant->getValue()
                                : $ret[$constant->name] = $constant->getValue();
                }
            }
        }

        return $ret ?? [];
    }

    /**
     * Has property.
     *
     * @param  string|object $class
     * @param  string        $name
     * @return bool|null
     */
    public static function hasProperty(string|object $class, string $name): bool|null
    {
        try {
            return (new ReflectionClass($class))->hasProperty($name);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * Get property.
     *
     * @param  string|object $class
     * @param  string        $name
     * @return array|null
     */
    public static function getProperty(string|object $class, string $name): array|null
    {
        return self::getProperties($class, true, $name)[$name] ?? null;
    }

    /**
     * Get property value.
     *
     * @param  string|object $class
     * @param  string        $name
     * @return any
     */
    public static function getPropertyValue(string|object $class, string $name)
    {
        return self::getProperties($class, true, $name)[$name]['value'] ?? null;
    }

    /**
     * Get properties.
     *
     * @param  string|object $class
     * @param  bool          $all
     * @param  string        $_name @internal
     * @return array|null
     */
    public static function getProperties(string|object $class, bool $all = true, string $_name = null): array|null
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

        // Nope..
        // $parent = $ref->getParentClass();
        // $parentProperties = [];
        // while ($parent) {
        //     $parentProperties = array_replace(
        //         array_reduce($parent->getProperties(),
        //             fn($ps, $p) => array_merge($ps, [$p->name => null])
        //         , [])
        //     , $parent->getDefaultProperties());

        //     $privates = array_aggregate($parent->getProperties(ReflectionProperty::IS_PRIVATE),
        //         fn(&$r, $p) => $r[] = $p->name);
        //     $parentProperties = array_filter($parentProperties,
        //         fn($p) => !in_array($p, $privates), ARRAY_FILTER_USE_KEY);

        //     foreach ($parentProperties as $name => $value) {
        //         $properties[$name] = $value;
        //     }

        //     // Move next-up.
        //     $parent = $parent->getParentClass();
        // }

        // Collect & merge some late-bind dynamic (weirdo) vars too.
        if (is_object($class) && ($vars = get_object_vars($class))) {
            $properties = array_merge($properties, array_filter(
                $vars, fn($v) => !in_array($v, $properties)
            ));
        }

        foreach ($properties as $name => $value) {
            $name = (string) $name;
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

            $type = $nullable = $trait = null;
            $modifiers = Reflection::getModifierNames($property->getModifiers());

            if ($propertyType = $property->getType()) {
                // Because type can be a class.
                $type = self::getName(
                    // The fuck: https://www.php.net/manual/en/class.reflectiontype.php
                    ($propertyType instanceof ReflectionUnionType)
                      ? (string) $propertyType : $propertyType->getName()
                );

                // Unify type display (?int => int|null).
                if ($propertyType->allowsNull() && !str_contains($type, '|null')) {
                    $type = str_replace('?', '', $type) .'|null';
                }

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
                } catch (Error) {}

                $initialized = $property->isInitialized($class);
            }

            $nullable    ??= ($value === null);
            $initialized ??= ($value !== null);
            $className     = self::getName($property->class);

            // Using name as key, since all names will be overridden internally in children.
            $ret[$property->name] = [
                'name'        => $property->name,
                'value'       => $value,                'type'        => $type,
                'nullable'    => $nullable ,            'visibility'  => $visibility,
                'static'      => $property->isStatic(), 'initialized' => $initialized,
                'class'       => $className,            'trait'       => $trait,
                'modifiers'   => $modifiers,
            ];
        }

        return $ret ?? [];
    }

    /**
     * Get property names.
     *
     * @param  string|object $class
     * @param  bool          $all
     * @return array|null
     */
    public static function getPropertyNames(string|object $class, bool $all = true): array|null
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
     *
     * @param  string|object $class
     * @param  bool          $all
     * @param  bool          $withNames
     * @return array|null
     */
    public static function getPropertyValues(string|object $class, bool $all = true, bool $withNames = false): array|null
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
            $name = (string) $name;
            $property = new ReflectionProperty($class, $name);
            if (!$all && !$property->isPublic()) {
                continue;
            }

            if (is_object($class)) {
                try {
                    $property->setAccessible(true);
                    $value = $property->getValue($class);
                } catch (Error) {}
            }

            !$withNames ? $ret[] = $value
                        : $ret[$name] = $value;
        }

        return $ret ?? [];
    }

    /**
     * Has method.
     *
     * @param  string|object $class
     * @param  string        $name
     * @return bool|null
     * @since  5.0
     */
    public static function hasMethod(string|object $class, string $name): bool|null
    {
        try {
            return (new ReflectionClass($class))->hasMethod($name);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * Get method.
     *
     * @param  string|object $class
     * @param  string        $name
     * @return array|null
     */
    public static function getMethod(string|object $class, string $name): array|null
    {
        return self::getMethods($class, true, $name)[$name] ?? null;
    }

    /**
     * Get methods.
     *
     * @param  string|object $class
     * @param  bool          $all
     * @param  string        $_name @internal
     * @return array|null
     */
    public static function getMethods(string|object $class, bool $all = true, string $_name = null): array|null
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

            $return = $trait = $parameters = null;
            $modifiers = Reflection::getModifierNames($method->getModifiers());

            if ($returnType = $method->getReturnType()) {
                // Because type can be a class.
                $return = self::getName(
                    // The fuck: https://www.php.net/manual/en/class.reflectiontype.php
                    ($returnType instanceof ReflectionUnionType)
                      ? (string) $returnType : $returnType->getName()
                );

                // Unify type display (?int => int|null).
                if ($returnType->allowsNull() && !str_contains($return, '|null')) {
                    $return = str_replace('?', '', $return) .'|null';
                }
            } elseif ($doc = $method->getDocComment()) {
                preg_match('~(?=@(returns?|alias *(?:Of|To|For)?) *([^\s]+))~i', $doc, $match);
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
                        // Because type can be a class.
                        $type = self::getName(
                            // The fuck: https://www.php.net/manual/en/class.reflectiontype.php
                            ($paramType instanceof ReflectionUnionType)
                              ? (string) $paramType : $paramType->getName()
                        );

                        // Unify type display (?int => int|null).
                        if ($param->allowsNull() && !str_contains($type, '|null')) {
                            $type = str_replace('?', '', $type) .'|null';
                        }

                        $parameter['type'] = $type;
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
                    } catch (ReflectionException) {}

                    $parameters[] = $parameter;
                }
            }

            $className = self::getName($method->class);

            // Using method name as key, since all names will be overridden internally in children.
            $ret[$method->name] = [
                'name'       => $method->name,
                'visibility' => $visibility,         'return'     => $return,
                'static'     => $method->isStatic(), 'final'      => $method->isFinal(),
                'class'      => $className,          'trait'      => $trait,
                'modifiers'  => $modifiers,          'parameters' => $parameters,
            ];
        }

        return $ret ?? [];
    }

    /**
     * Get method names.
     *
     * @param  string|object $class
     * @param  bool          $all
     * @return array|null
     */
    public static function getMethodNames(string|object $class, bool $all = true): array|null
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
     *
     * @param  string|object $class
     * @return array|null
     */
    public static function getParents(string|object $class): array|null
    {
        $ret = class_parents($class);
        if ($ret !== false) {
            $ret = array_keys($ret);
            return $ret;
        }
        return null;
    }

    /**
     * Get interfaces.
     *
     * @param  string|object $class
     * @return array|null
     */
    public static function getInterfaces(string|object $class): array|null
    {
        $ret = class_implements($class);
        if ($ret !== false) {
            $ret = array_keys($ret);
            $ret = array_reverse($ret); // Fix weird reverse order.
            return $ret;
        }
        return null;
    }

    /**
     * Get traits.
     *
     * @param  string|object $class
     * @param  bool          $all
     * @return array|null
     */
    public static function getTraits(string|object $class, bool $all = true): array|null
    {
        $ret = class_uses($class);
        if ($ret !== false) {
            $ret = array_keys($ret);
            if ($all) {
                foreach ((array) self::getParents($class) as $parent) {
                    $ret = array_merge($ret, (array) self::getTraits($parent));
                }

                // Really all..
                if ($ret) {
                    foreach ($ret as $re) {
                        $ret = array_merge($ret, (array) self::getTraits($re));
                    }
                    $ret = array_unique($ret);
                }
            }
            return $ret;
        }
        return null;
    }
}
