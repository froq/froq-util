<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

/**
 * Object utility class.
 *
 * @package froq\util
 * @class   froq\util\Objects
 * @author  Kerem Güneş
 * @since   4.0
 * @static
 */
final class Objects extends \StaticClass
{
    /**
     * Reflect.
     *
     * @param  object|string $target
     * @return XReflectionObject|XReflectionClass|null
     */
    public static function reflect(object|string $target): \XReflectionObject|\XReflectionClass|null
    {
        try {
            return is_object($target) ? new \XReflectionObject($target) : new \XReflectionClass($target);
        } catch (\ReflectionException) {
            return null;
        }
    }

    /**
     * Get id.
     *
     * @param  object $target
     * @param  bool   $withName
     * @return int|string
     */
    public static function getId(object $target, bool $withName = true): int|string
    {
        $id = spl_object_id($target);

        return $withName ? $target::class .'#'. $id : $id;
    }

    /**
     * Get hash.
     *
     * @param  object $target
     * @param  bool   $withName
     * @param  bool   $withRehash
     * @return string
     */
    public static function getHash(object $target, bool $withName = true, bool $withRehash = false): string
    {
        $hash = spl_object_hash($target);

        // Pack "000..." stuff.
        $withRehash && $hash = hash('crc32', $hash);

        return $withName ? $target::class .'#'. $hash : $hash;
    }

    /**
     * Get serialized hash.
     *
     * @param  object $target
     * @param  bool   $withName
     * @return string
     */
    public static function getSerializedHash(object $target, bool $withName = true): string
    {
        $hash = hash('crc32', self::getHash($target) . serialize($target));

        return $withName ? $target::class .'#'. $hash : $hash;
    }

    /**
     * Get type.
     *
     * @param  string|object $target
     * @return string|null
     * @since  6.0
     */
    public static function getType(object|string $target): string|null
    {
        $ref = self::reflect($target);

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
     * @param  object|string $target
     * @param  bool          $escape
     * @return string
     */
    public static function getName(object|string $target, bool $escape = false): string
    {
        $name = is_object($target) ? $target::class : $target;

        // Anons (causes issues, eg: drops thrown stack traces).
        $escape && $name = str_replace("\0", "\\0", $name);

        return $name;
    }

    /**
     * Get short name.
     *
     * @param  object|string $target
     * @param  bool          $escape
     * @return string
     */
    public static function getShortName(object|string $target, bool $escape = false): string
    {
        $name = self::getName($target, $escape);
        $spos = strrpos($name, '\\');

        return substr($name, ($spos > 0 ? $spos + 1 : 0));
    }

    /**
     * Get real name if aliased.
     *
     * @param  object|string $target
     * @return string
     * @since  6.0
     */
    public static function getRealName(object|string $target): string
    {
        $name = self::getName($target);

        $ref = self::reflect($target);
        if ($ref && $ref->name !== $name) {
            return $ref->name;
        }

        return $name;
    }

    /**
     * Get namespace.
     *
     * @param  object|string $target
     * @param  bool          $baseOnly
     * @return string
     */
    public static function getNamespace(object|string $target, bool $baseOnly = false): string
    {
        $name = self::getName($target);
        $spos = $baseOnly ? strpos($name, '\\') : strrpos($name, '\\');

        return substr($name, 0, ($spos > 0 ? $spos : 0));
    }

    /**
     * Has constant.
     *
     * @param  object|string $target
     * @param  string        $name
     * @return bool|null
     */
    public static function hasConstant(object|string $target, string $name): bool|null
    {
        return self::reflect($target)?->hasConstant($name);
    }

    /**
     * Get constant.
     *
     * @param  object|string $target
     * @param  string        $name
     * @return array|null
     */
    public static function getConstant(object|string $target, string $name): array|null
    {
        return self::getConstants($target, true, $name)[$name] ?? null;
    }

    /**
     * Get constant value.
     *
     * @param  object|string $target
     * @param  string        $name
     * @return mixed|null
     */
    public static function getConstantValue(object|string $target, string $name): mixed
    {
        return self::getConstants($target, true, $name)[$name]['value'] ?? null;
    }

    /**
     * Get constants.
     *
     * @param  object|string $target
     * @param  bool          $all
     * @param  string|null   $_name @internal
     * @return array|null
     */
    public static function getConstants(object|string $target, bool $all = true, string $_name = null): array|null
    {
        $ref = self::reflect($target);
        if (!$ref) {
            return null;
        }

        $ret = [];
        foreach ($ref->getReflectionConstants() as $constant) {
            if ($_name && $_name !== $constant->name) {
                continue;
            }
            if (!$all && !$constant->isPublic()) {
                continue;
            }

            $constantName  = $constant->name;
            $constantClass = $ref->name;

            // First definers.
            $trait = array_find(
                $ref->getTraits(),
                fn(\ReflectionTrait $r): bool => $r->hasConstant($constantName)
            );
            $interface = array_find(
                $ref->getInterfaces(),
                fn(\ReflectionInterface $r): bool => $r->hasConstant($constantName)
            );

            $modifiers   = $constant->getModifierNames();
            $visibility  = $constant->getVisibility();

            // Sorry, but no method such getType().. @todo: 8.3
            preg_match('~\[ (\w+) (?<type>\w+) .+ \]~', (string) $constant, $match);

            // Using name as key, since all names will be overridden internally in children.
            $ret[$constantName] = [
                'name'       => $constantName,         'class'     => $constantClass,
                'trait'      => $trait?->name,         'interface' => $interface?->name,
                'value'      => $constant->getValue(), 'type'      => $match['type'],
                'visibility' => $visibility,           'modifiers' => $modifiers,
                'reflection' => $constant,
            ];
        }

        return $ret;
    }

    /**
     * Get constant names.
     *
     * @param  object|string $target
     * @param  bool          $all
     * @return array|null
     */
    public static function getConstantNames(object|string $target, bool $all = true): array|null
    {
        $ref = self::reflect($target);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 = all, 1 = public only.
        $filter = $all ? -1 : 1;

        return $ref->getConstantNames($filter);
    }

    /**
     * Get constant values.
     *
     * @param  object|string $target
     * @param  bool          $all
     * @param  bool          $assoc
     * @return array|null
     */
    public static function getConstantValues(object|string $target, bool $all = true, bool $assoc = false): array|null
    {
        $ref = self::reflect($target);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 = all, 1 = public only.
        $filter = $all ? -1 : 1;

        return $ref->getConstantValues($filter, $assoc);
    }

    /**
     * Has property.
     *
     * @param  object|string $target
     * @param  string        $name
     * @return bool|null
     */
    public static function hasProperty(object|string $target, string $name): bool|null
    {
        return self::reflect($target)?->hasProperty($name);
    }

    /**
     * Get property.
     *
     * @param  object|string $target
     * @param  string        $name
     * @return array|null
     */
    public static function getProperty(object|string $target, string $name): array|null
    {
        return self::getProperties($target, true, $name)[$name] ?? null;
    }

    /**
     * Get property value.
     *
     * @param  object|string $target
     * @param  string        $name
     * @return mixed|null
     */
    public static function getPropertyValue(object|string $target, string $name): mixed
    {
        return self::reflect($target)?->getProperty($name)?->getValue($target);
    }

    /**
     * Get properties.
     *
     * @param  object|string $target
     * @param  bool          $all
     * @param  string|null   $_name @internal
     * @return array|null
     */
    public static function getProperties(object|string $target, bool $all = true, string $_name = null): array|null
    {
        $ref = self::reflect($target);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 = all, 1 = public & public static only.
        $filter = $all ? -1 : 1;

        $ret = [];

        foreach ($ref->getProperties($filter) as $property) {
            if ($_name && $_name !== $property->name) {
                continue;
            }

            $propertyName  = $property->name;
            $propertyClass = $property->class;

            // First definer.
            $trait = array_find(
                $ref->getTraits(),
                fn(\ReflectionTrait $r): bool => $r->hasProperty($propertyName)
            );

            // Type & nullable.
            if ($propertyType = $property->getType()) {
                $type     = $propertyType->getName();
                $nullable = $propertyType->allowsNull();
            }

            $modifiers   = $property->getModifierNames();
            $visibility  = $property->getVisibility();
            $initialized = $property->isInitialized(is_object($target) ? $target : null);

            // Using name as key, since all names will be overridden internally in children.
            $ret[$propertyName] = [
                'name'       => $propertyName,         'class'       => $propertyClass,
                'trait'      => $trait?->name,
                'type'       => $type ?? null,         'nullable'    => $nullable ?? true,
                'static'     => $property->isStatic(), 'dynamic'     => $property->isDynamic(),
                'visibility' => $visibility,           'initialized' => $initialized,
                'modifiers'  => $modifiers,            'reflection'  => $property,
            ];
        }

        return $ret;
    }

    /**
     * Get property names.
     *
     * @param  object|string $target
     * @param  bool          $all
     * @return array|null
     */
    public static function getPropertyNames(object|string $target, bool $all = true): array|null
    {
        $ref = self::reflect($target);
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
     * @param  object|string $target
     * @param  bool          $all
     * @param  bool          $assoc
     * @return array|null
     */
    public static function getPropertyValues(object|string $target, bool $all = true, bool $assoc = false): array|null
    {
        $ref = self::reflect($target);
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
     * @param  object|string $target
     * @param  string        $name
     * @return bool|null
     */
    public static function hasMethod(object|string $target, string $name): bool|null
    {
        return self::reflect($target)?->hasMethod($name);
    }

    /**
     * Get method.
     *
     * @param  object|string $target
     * @param  string        $name
     * @return array|null
     */
    public static function getMethod(object|string $target, string $name): array|null
    {
        return self::getMethods($target, true, $name)[$name] ?? null;
    }

    /**
     * Get methods.
     *
     * @param  object|string $target
     * @param  bool          $all
     * @param  string|null   $_name @internal
     * @return array|null
     */
    public static function getMethods(object|string $target, bool $all = true, string $_name = null): array|null
    {
        $ref = self::reflect($target);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        $ret = [];

        foreach ($ref->getMethods($filter) as $method) {
            if ($_name && $_name !== $method->name) {
                continue;
            }

            $methodName  = $method->name;
            $methodClass = $method->class;

            // First definers.
            $trait = array_find(
                $ref->getTraits(),
                fn(\ReflectionTrait $r): bool => $r->hasMethod($methodName)
            );
            $interface = array_find(
                $ref->getInterfaces(),
                fn(\ReflectionInterface $r): bool => $r->hasMethod($methodName)
            );

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

            $modifiers  = $method->getModifierNames();
            $visibility = $method->getVisibility();

            // Using method name as key, since all names will be overridden internally in children.
            $ret[$methodName] = [
                'name'       => $methodName,         'class'      => $methodClass,
                'trait'      => $trait?->name,       'interface'  => $interface?->name,
                'return'     => $return,             'visibility' => $visibility,
                'static'     => $method->isStatic(), 'final'      => $method->isFinal(),
                'modifiers'  => $modifiers,          'parameters' => $parameters,
                'reflection' => $method,
            ];
        }

        return $ret;
    }

    /**
     * Get method names.
     *
     * @param  object|string $target
     * @param  bool          $all
     * @return array|null
     */
    public static function getMethodNames(object|string $target, bool $all = true): array|null
    {
        $ref = self::reflect($target);
        if (!$ref) {
            return null;
        }

        // Shorter: -1 is all, 1 public & public static only.
        $filter = $all ? -1 : 1;

        return $ref->getMethodNames($filter);
    }

    /**
     * Get parent, optionally the base parent only.
     *
     * @param  object|string $target
     * @param  bool          $baseOnly
     * @return string|null
     */
    public static function getParent(object|string $target, bool $baseOnly = false): string|null
    {
        try {
            $ret = [];

            if (!$baseOnly) {
                $ret[] = get_parent_class($target);
            } else {
                $parent = get_parent_class($target);
                while ($parent) {
                    array_push($ret, $parent);
                    $parent = get_parent_class($parent);
                }
            }

            return array_last($ret);
        } catch (\Throwable) { // TypeError? WTF?
            return null;
        }
    }

    /**
     * Get parents.
     *
     * @param  object|string $target
     * @return array|null
     */
    public static function getParents(object|string $target, bool $reverse = false): array|null
    {
        $ret = @class_parents($target);

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
     * @param  object|string $target
     * @return array|null
     */
    public static function getInterfaces(object|string $target, bool $reverse = false): array|null
    {
        // Note: this function does not follow real inheritance.
        // For example A,B,C,D order B->A, C->B, D->C return D,B,A,C.
        $ret = @class_implements($target);

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
     * @param  object|string $target
     * @param  bool          $all
     * @return array|null
     */
    public static function getTraits(object|string $target, bool $reverse = false, bool $all = true): array|null
    {
        $ret = @class_uses($target);

        if ($ret !== false) {
            $ret = array_keys($ret);
            $reverse && ($ret = array_reverse($ret));

            if ($all) {
                foreach ((array) self::getParents($target) as $parent) {
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

            return array_values($ret);
        }

        return null;
    }

    /**
     * Set vars.
     *
     * @param object          $target
     * @param object|iterable $vars
     * @return object
     * @since  6.0
     */
    public static function setVars(object $target, object|iterable $vars): object
    {
        return set_object_vars($target, $vars);
    }

    /**
     * Get vars.
     *
     * @param  object|string $target
     * @param  bool          $namesOnly
     * @return array|null
     * @since  6.0
     */
    public static function getVars(object|string $target, bool $namesOnly = false): array|null
    {
        try {
            $vars = is_object($target) ? get_object_vars($target) : get_class_vars($target);

            return $namesOnly ? array_keys($vars) : $vars;
        } catch (\Throwable) {
            return null;
        }
    }
}
