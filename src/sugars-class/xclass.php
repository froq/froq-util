<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\Objects;

/**
 * A class for playing with classes in OOP-way.
 *
 * @package global
 * @class   XClass
 * @author  Kerem Güneş
 * @since   6.0
 */
class XClass implements Stringable
{
    /** Target class. */
    public readonly string $name;

    /** Exists state. */
    public readonly bool $exists;

    /**
     * Constructor.
     *
     * @param string|object $class
     */
    public function __construct(string|object $class)
    {
        if (is_string($class)) {
            $this->name   = $class;
            // Help autoloader & shortcut for methods.
            $this->exists = class_exists($class, true);
        } else {
            $this->name   = $class::class;
            $this->exists = true;
        }
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @magic
     */
    public function __debugInfo(): array
    {
        return ['name' => $this->name];
    }

    /**
     * Get name.
     *
     * @param  bool $escape
     * @return string
     */
    public function getName(bool $escape = false): string
    {
        return Objects::getName($this->name, $escape);
    }

    /**
     * Get short name.
     *
     * @param  bool $escape
     * @return string
     */
    public function getShortName(bool $escape = false): string
    {
        return Objects::getShortName($this->name, $escape);
    }

    /**
     * Get real name (for aliases).
     *
     * @return string
     */
    public function getRealName(): string
    {
        return Objects::getRealName($this->name);
    }

    /**
     * Get namespace.
     *
     * @param  bool $baseOnly
     * @return string
     */
    public function getNamespace(bool $baseOnly = false): string
    {
        return Objects::getNamespace($this->name, $baseOnly);
    }

    /**
     * Get file.
     *
     * @return string|false|null
     */
    public function getFile(): string|false|null
    {
        return $this->exists ? Objects::getFile($this->name) : null;
    }

    /**
     * Get directory.
     *
     * @return string|false|null
     */
    public function getDirectory(): string|false|null
    {
        return $this->exists ? Objects::getDirectory($this->name) : null;
    }

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType(): string|null
    {
        return $this->exists ? Objects::getType($this->name) : null;
    }

    /**
     * Exists state checker.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->exists;
    }

    /**
     * Extends state checker.
     *
     * @param  string $class      Class or interface (both).
     * @param  bool   $parentOnly
     * @return bool
     */
    public function extends(string $class, bool $parentOnly = false): bool
    {
        return $this->exists && class_extends($this->name, $class, $parentOnly);
    }

    /**
     * Implements state checker.
     *
     * @param  string $interface
     * @return bool
     */
    public function implements(string $interface): bool
    {
        return $this->exists && is_a($this->name, $interface, true); // Faster.
        // return $this->exists && in_array($interface, (array) $this->getInterfaces(), true);
    }

    /**
     * Uses state checker.
     *
     * @param  string $trait
     * @return bool
     */
    public function uses(string $trait): bool
    {
        return $this->exists && in_array($trait, (array) $this->getTraits(), true);
    }

    /**
     * Get vars.
     *
     * @return array|null
     */
    public function getVars(): array|null
    {
        return $this->exists ? Objects::getVars($this->name) : null;
    }

    /**
     * Get var names.
     *
     * @return array|null
     */
    public function getVarNames(): array|null
    {
        return $this->exists ? Objects::getVars($this->name, true) : null;
    }

    /**
     * Get constants.
     *
     * @param  bool $namesOnly
     * @param  bool $assoc
     * @return array|null
     */
    public function getConstants(bool $namesOnly = false, bool $assoc = true): array|null
    {
        return $this->exists ? get_class_constants($this->name, false, $namesOnly, $assoc) : null;
    }

    /**
     * Get properties.
     *
     * @param  bool $namesOnly
     * @param  bool $assoc
     * @return array|null
     */
    public function getProperties(bool $namesOnly = false, bool $assoc = true): array|null
    {
        return $this->exists ? get_class_properties($this->name, false, $namesOnly, $assoc) : null;
    }

    /**
     * Get methods.
     *
     * @return array|null
     */
    public function getMethods(): array|null
    {
        return $this->exists ? get_class_methods($this->name) : null;
    }

    /**
     * Get parent.
     *
     * @param  bool $top
     * @return string|null
     */
    public function getParent(bool $top = false): string|null
    {
        return $this->exists ? Objects::getParent($this->name, $top) : null;
    }

    /**
     * Get parents.
     *
     * @return array|null
     */
    public function getParents(): array|null
    {
        return $this->exists ? Objects::getParents($this->name) : null;
    }

    /**
     * Get interfaces.
     *
     * @return array|null
     */
    public function getInterfaces(): array|null
    {
        return $this->exists ? Objects::getInterfaces($this->name) : null;
    }

    /**
     * Get traits.
     *
     * @return array|null
     */
    public function getTraits(): array|null
    {
        return $this->exists ? Objects::getTraits($this->name) : null;
    }

    /**
     * Constant existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function hasConstant(string $name): bool
    {
        return $this->exists && constant_exists($this->name, $name);
    }

    /**
     * Property existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function hasProperty(string $name): bool
    {
        return $this->exists && property_exists($this->name, $name);
    }

    /**
     * Method existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function hasMethod(string $name): bool
    {
        return $this->exists && method_exists($this->name, $name);
    }

    /**
     * Valid name checker (not for anonyms).
     *
     * @return bool
     */
    public function hasValidName(): bool
    {
        return preg_test('~^([\\\]?[a-z_][a-z0-9_\\\]*)$~i', $this->name);
    }

    /**
     * Alias name checker.
     *
     * @return bool
     */
    public function hasAliasName(): bool
    {
        return $this->name !== ($this->reflect()?->name ?? $this->name);
    }

    /**
     * Class-of checker.
     *
     * @param  string    $class
     * @param  string ...$classes
     * @return bool
     */
    public function isClassOf(string $class, string ...$classes): bool
    {
        return is_class_of($this->name, $class, ...$classes);
    }

    /**
     * Subclass-of checker.
     *
     * @param  string $class
     * @return bool
     */
    public function isSubclassOf(string $class): bool
    {
        return is_subclass_of($this->name, $class);
    }

    /**
     * @alias extends()
     */
    public function isExtenderOf(...$args)
    {
        return $this->extends(...$args);
    }

    /**
     * @alias implements()
     */
    public function isImplementerOf(...$args)
    {
        return $this->implements(...$args);
    }

    /**
     * @alias uses()
     */
    public function isUserOf(...$args)
    {
        return $this->uses(...$args);
    }

    /**
     * Create an instance of this class with constructor.
     *
     * @param  mixed ...$args
     * @return object
     * @causes Exception
     */
    public function init(mixed ...$args): object
    {
        return Objects::init($this->name, ...$args);
    }

    /**
     * Create an instance of this class without constructor.
     *
     * @param  mixed ...$vars
     * @return object
     * @causes Exception
     */
    public function sample(mixed ...$vars): object
    {
        return Objects::sample($this->name, ...$vars);
    }

    /**
     * Reflect & return reflection, or null (error).
     *
     * @param  bool $extended
     * @return ReflectionClass|XReflectionClass|null
     */
    public function reflect(bool $extended = false)
        : ReflectionClass|XReflectionClass|null
    {
        try {
            return !$extended ? new ReflectionClass($this->name)
                : new XReflectionClass($this->name);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * Reflect a constant & return reflection, or null (error).
     *
     * @param  string $name
     * @param  bool   $extended
     * @return ReflectionClassConstant|XReflectionClassConstant|null
     */
    public function reflectConstant(string $name, bool $extended = false)
        : ReflectionClassConstant|XReflectionClassConstant|null
    {
        try {
            return !$extended ? new ReflectionClassConstant($this->name, $name)
                : new XReflectionClassConstant($this->name, $name);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * Reflect a property & return reflection, or null (error).
     *
     * @param  string $name
     * @param  bool   $extended
     * @return ReflectionProperty|XReflectionProperty|null
     */
    public function reflectProperty(string $name, bool $extended = false)
        : ReflectionProperty|XReflectionProperty|null
    {
        try {
            return !$extended ? new ReflectionProperty($this->name, $name)
                : new XReflectionProperty($this->name, $name);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * Reflect a method & return reflection, or null (error).
     *
     * @param  string $name
     * @param  bool   $extended
     * @return ReflectionMethod|XReflectionMethod|null
     */
    public function reflectMethod(string $name, bool $extended = false)
        : ReflectionMethod|XReflectionMethod|null
    {
        try {
            return !$extended ? new ReflectionMethod($this->name, $name)
                : new XReflectionMethod($this->name, $name);
        } catch (ReflectionException) {
            return null;
        }
    }
}
