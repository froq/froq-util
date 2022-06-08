<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util;

use Reflection, ReflectionException,
    ReflectionObjectExtended, ReflectionClassExtended;

/**
 * Objects.
 *
 * @package froq\util
 * @object  froq\util\Objects
 * @author  Kerem Güneş
 * @since   4.0
 * @static
 */
final class Objects extends \StaticClass
{
    /**
     * Reflect.
     *
     * @param  object|string $object
     * @return ReflectionObjectExtended|ReflectionClassExtended|null
     */
    public static function reflect(object|string $object): ReflectionObjectExtended|ReflectionClassExtended|null
    {
        try {
            return is_object($object)
                 ? new ReflectionObjectExtended($object)
                 : new ReflectionClassExtended($object);
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
     * Get type.
     *
     * @param  string|object $object
     * @return string|null
     * @since  6.0
     */
    public static function getType(object|string $object): string|null
    {
        $ref = self::reflect($object);

        return match (true) {
            $ref?->isClass()     => 'class',
            $ref?->isInterface() => 'interface',
            $ref?->isTrait()     => 'trait',
            $ref?->isEnum()      => 'enum',
            default              => null
        };
    }

    /**
     * Get name.
     *
     * @param  object|string $object
     * @param  bool          $clean
     * @return string
     */
    public static function getName(object|string $object, bool $clean = false): string
    {
        $name = is_object($object) ? $object::class : $object;

        // Anons.
        $clean && $name = str_replace("\0", "", $name);

        // @cancel
        // if (str_contains($name, '@')) {
        //     $name = preg_replace(
        //         '~(.+)@anonymous\0*(.+)\:(.+)\$.*~i',
        //         '\1@anonymous@\2:\3',
        //         $name
        //     );
        // }

        return $name;
    }

    /**
     * Get short name.
     *
     * @param  object|string $object
     * @param  bool          $clean
     * @return string
     */
    public static function getShortName(object|string $object, bool $clean = false): string
    {
        $name = self::getName($object, $clean);
        $spos = strrpos($name, '\\');

        return substr($name, ($spos > 0 ? $spos + 1 : 0));
    }

    /**
     * Get real name if aliased.
     *
     * @param  string $class
     * @return string
     * @since  6.0
     */
    public static function getRealName(string $class): string
    {
        $ref = self::reflect($class);
        if ($ref && $ref->name != $class) {
            return $ref->name;
        }

        return $class;
    }

    /**
     * Get namespace.
     *
     * @param  object|string $object
     * @param  bool          $baseOnly
     * @return string
     */
    public static function getNamespace(object|string $object, bool $baseOnly = false): string
    {
        $name = self::getName($object);
        $spos = $baseOnly ? strpos($name, '\\') : strrpos($name, '\\');

        return substr($name, 0, ($spos > 0 ? $spos : 0));
    }

    /**
     * Has constant.
     *
     * @param  object|string $object
     * @param  string        $name
     * @return bool|null
     */
    public static function hasConstant(object|string $object, string $name): bool|null
    {
        return self::reflect($object)?->hasConstant($name);
    }

    /**
     * Get constant.
     *
     * @param  object|string $object
     * @param  string        $name
     * @return array|null
     */
    public static function getConstant(object|string $object, string $name): array|null
    {
        return self::getConstants($object, true, $name)[$name] ?? null;
    }

    /**
     * Get constant value.
     *
     * @param  object|string $object
     * @param  string        $name
     * @return mixed|null
     */
    public static function getConstantValue(object|string $object, string $name): mixed
    {
        return self::getConstants($object, true, $name)[$name]['value'] ?? null;
    }

    /**
     * Get constants.
     *
     * @param  object|string $object
     * @param  bool          $all
     * @param  string|null   $_name @internal
     * @return array|null
     */
    public static function getConstants(object|string $object, bool $all = true, string $_name = null): array|null
    {
        $ref = self::reflect($object);
        if (!$ref) {
            return null;
        }

        $ret = [];
        foreach ($ref->getReflectionConstants() as $constant) {
            if ($_name && $_name != $constant->name) {
                continue;
            }
            if (!$all && !$constant->isPublic()) {
                continue;
            }

            $modifiers = Reflection::getModifierNames($constant->getModifiers());
            $interface = null;
            $class     = $constant->getDeclaringClass()->name;

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
                'reflection' => $constant,
            ];
        }

        return $ret;
    }

    /**
     * Get constant names.
     *
     * @param  object|string $object
     * @param  bool          $all
     * @return array|null
     */
    public static function getConstantNames(object|string $object, bool $all = true): array|null
    {
        $ref = self::reflect($object);
        if (!$ref) {
            return null;
        }

        if ($all) {
            // Seems doesn't matter constant visibility for getConstants().
            $ret = array_keys($ref->getConstants());
        } else {
            $ret = [];
            foreach ($ref->getReflectionConstants() as $constant) {
                if ($constant->isPublic()) {
                    $ret[] = $constant->name;
                }
            }
        }

        return $ret;
    }

    /**
     * Get constant values.
     *
     * @param  object|string $object
     * @param  bool          $all
     * @param  bool          $assoc
     * @return array|null
     */
    public static function getConstantValues(object|string $object, bool $all = true, bool $assoc = false): array|null
    {
        $ref = self::reflect($object);
        if (!$ref) {
            return null;
        }

        if ($all) {
            // Seems doesn't matter constant visibility for getConstants().
            $ret = $ref->getConstants();
        } else {
            $ret = [];
            foreach ($ref->getReflectionConstants() as $constant) {
                if ($constant->isPublic()) {
                    $ret[$constant->name] = $constant->getValue();
                }
            }
        }

        $assoc || $ret = array_values($ret);

        return $ret;
    }

    /**
     * Has property.
     *
     * @param  object|string $object
     * @param  string        $name
     * @return bool|null
     */
    public static function hasProperty(object|string $object, string $name): bool|null
    {
        return self::reflect($object)?->hasProperty($name);
    }

    /**
     * Get property.
     *
     * @param  object|string $object
     * @param  string        $name
     * @return array|null
     */
    public static function getProperty(object|string $object, string $name): array|null
    {
        return self::getProperties($object, true, $name)[$name] ?? null;
    }

    /**
     * Get property value.
     *
     * @param  object|string $object
     * @param  string        $name
     * @return mixed|null
     */
    public static function getPropertyValue(object|string $object, string $name): mixed
    {
        return self::reflect($object)?->getProperty($name)?->getValue($object);
    }

    /**
     * Get properties.
     *
     * @param  object|string $object
     * @param  bool          $all
     * @param  string|null   $_name @internal
     * @return array|null
     */
    public static function getProperties(object|string $object, bool $all = true, string $_name = null): array|null
    {
        $ref = self::reflect($object);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 = all, 1 = public & public static only.
        $filter = $all ? -1 : 1;

        $ret = [];

        foreach ($ref->getProperties($filter) as $property) {
            // Only getters used, name/property cannot overriden.
            $propertyName  = $property->getName();
            $propertyClass = $property->getClass();

            if ($_name && $_name != $propertyName) {
                continue;
            }

            $type = $nullable = $trait = null;

            if ($propertyType = $property->getType()) {
                $type     = $propertyType->getName();
                $nullable = $propertyType->allowsNull();
            }
            if ($traits = self::getTraits($propertyClass)) {
                foreach ($traits as $traitName) {
                    // No break, searching the real definer but not user trait.
                    if (property_exists($traitName, $propertyName)) {
                        $trait = $traitName;
                    }
                }
            }

            $modifiers   = $property->getModifierNames();
            $visibility  = $property->getVisibility();
            $initialized = is_object($object) && $property->isInitialized($object);

            // Using name as key, since all names will be overridden internally in children.
            $ret[$propertyName] = [
                'name'        => $propertyName,           'class'      => $propertyClass,
                'trait'       => $trait,
                'type'        => $type,                   'nullable'   => $nullable,
                'static'      => $property->isStatic(),   'dynamic'    => $property->isDynamic(),
                'internal'    => $property->isInternal(), 'visibility' => $visibility,
                'initialized' => $initialized,            'modifiers'  => $modifiers,
                'reflection'  => $property,
            ];
        }

        return $ret;
    }

    /**
     * Get property names.
     *
     * @param  object|string $object
     * @param  bool          $all
     * @return array|null
     */
    public static function getPropertyNames(object|string $object, bool $all = true): array|null
    {
        $ref = self::reflect($object);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 = all, 1 = public & public static only.
        $filter = $all ? -1 : 1;

        return $ref->getPropertyNames($filter);
    }

    /**
     * Get property values.
     *
     * @param  object|string $object
     * @param  bool          $all
     * @param  bool          $assoc
     * @return array|null
     */
    public static function getPropertyValues(object|string $object, bool $all = true, bool $assoc = false): array|null
    {
        $ref = self::reflect($object);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 = all, 1 = public & public static only.
        $filter = $all ? -1 : 1;

        return $ref->getPropertyValues($filter, $assoc);
    }

    /**
     * Has method.
     *
     * @param  object|string $object
     * @param  string        $name
     * @return bool|null
     * @since  5.0
     */
    public static function hasMethod(object|string $object, string $name): bool|null
    {
        return self::reflect($object)?->hasMethod($name);
    }

    /**
     * Get method.
     *
     * @param  object|string $object
     * @param  string        $name
     * @return array|null
     */
    public static function getMethod(object|string $object, string $name): array|null
    {
        return self::getMethods($object, true, $name)[$name] ?? null;
    }

    /**
     * Get methods.
     *
     * @param  object|string $object
     * @param  bool          $all
     * @param  string|null   $_name @internal
     * @return array|null
     */
    public static function getMethods(object|string $object, bool $all = true, string $_name = null): array|null
    {
        $ref = self::reflect($object);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        $ret = [];

        foreach ($ref->getMethods($filter) as $method) {
            $methodName  = $method->getName();
            $methodClass = $method->getClass();

            if ($_name && $_name != $method->name) {
                continue;
            }

            $return = null; $parameters = [];

            if ($returnType = $method->getReturnType()) {
                $return = $returnType->getName();
            }  elseif ($docComment = $method->getDocComment()) {
                preg_match('~(?=@(returns?|alias *(?:Of|To|For)?) *([^\s]+))~i', $docComment, $match);
                if ($match) {
                    $return = strsrc($match[1], 'alias')  // Alias stuff.
                        ? '@see '. $match[2] : $match[2];
                }
            }

            if ($params = $method->getParameters()) {
                foreach ($params as $param) {
                    $parameter = [
                        'name'     => $param->getName(),    'type'    => $param->getType()?->getName(),
                        'nullable' => $param->allowsNull(), 'default' => $param->getDefaultValue(),
                    ];

                    if ($param->isVariadic()) {
                        $parameter['type'] = $parameter['type'] ? $parameter['type'] .' ...' : '...';
                    }

                    $parameters[] = $parameter;
                }
            }

            $modifiers   = $method->getModifierNames();
            $visibility  = $method->getVisibility();
            $trait       = last($method->getTraitNames());
            $interface   = last($method->getInterfaceNames());

            // Using method name as key, since all names will be overridden internally in children.
            $ret[$methodName] = [
                'name'       => $methodName,           'class'      => $methodClass,
                'trait'      => $trait,                'interface'  => $interface,
                'return'     => $return,               'visibility' => $visibility,
                'static'     => $method->isStatic(),   'final'      => $method->isFinal(),
                'internal'   => $method->isInternal(), 'modifiers'  => $modifiers,
                'parameters' => $parameters,
                'reflection' => $method,
            ];
        }

        return $ret;
    }

    /**
     * Get method names.
     *
     * @param  object|string $object
     * @param  bool          $all
     * @return array|null
     */
    public static function getMethodNames(object|string $object, bool $all = true): array|null
    {
        $ref = self::reflect($object);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        return $ref->getMethodNames($filter);
    }

    /**
     * Get parent.
     *
     * @param  object|string $object
     * @return string|null
     * @since  6.0
     */
    public static function getParent(object|string $object): string|null
    {
        try {
            return get_parent_class($object) ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Get parents.
     *
     * @param  object|string $object
     * @return array|null
     */
    public static function getParents(object|string $object, bool $reverse = false): array|null
    {
        $ret =@ class_parents($object);
        if ($ret !== false) {
            $ret = array_keys($ret);
            $reverse && ($ret = array_reverse($ret));
            return $ret;
        }
        return null;
    }

    /**
     * Get interfaces.
     *
     * @param  object|string $object
     * @return array|null
     */
    public static function getInterfaces(object|string $object, bool $reverse = false): array|null
    {
        // Note: this function does not follow real inheritance.
        // For example A,B,C,D order B->A, C->B, D->C return D,B,A,C.
        $ret =@ class_implements($object);
        if ($ret !== false) {
            $ret = array_keys($ret);
            $reverse && ($ret = array_reverse($ret));
            return $ret;
        }
        return null;
    }

    /**
     * Get traits.
     *
     * @param  object|string $object
     * @param  bool          $all
     * @return array|null
     */
    public static function getTraits(object|string $object, bool $reverse = false, bool $all = true): array|null
    {
        $ret =@ class_uses($object);
        if ($ret !== false) {
            $ret = array_keys($ret);
            $reverse && ($ret = array_reverse($ret));
            if ($all) {
                foreach ((array) self::getParents($object) as $parent) {
                    $ret = array_merge($ret, (array) self::getTraits($parent, $reverse, true));
                }

                // Really all..
                if ($ret) {
                    foreach ($ret as $re) {
                        $ret = array_merge($ret, (array) self::getTraits($re, $reverse, true));
                    }
                    $ret = array_unique($ret);
                }
            }
            return $ret;
        }
        return null;
    }

    /**
     * Set vars.
     *
     * @param object          $object
     * @param object|iterable $vars
     * @return object
     * @since  6.0
     */
    public static function setVars(object $object, object|iterable $vars): object
    {
        return set_object_vars($object, $vars);
    }

    /**
     * Get vars.
     *
     * @param  object|string $object
     * @param  bool          $namesOnly
     * @return array|null
     * @since  6.0
     */
    public static function getVars(object|string $object, bool $namesOnly = false): array|null
    {
        try {
            $vars = is_object($object) ? get_object_vars($object) : get_class_vars($object);
            return $namesOnly ? array_keys($vars) : $vars;
        } catch (\Throwable) {
            return null;
        }
    }
}
