<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Objects;

/**
 * X-Class.
 *
 * A class for playing with classes in OOP-way.
 *
 * @package froq\util
 * @object  XClass
 * @author  Kerem Güneş
 * @since   6.0
 */
class XClass implements Stringable
{
    /** @var string */
    public readonly string $name;

    /** @var bool */
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
            // Help for autoloader & shortcut for methods.
            $this->exists = class_exists($class, true);
        } else {
            $this->name   = $class::class;
            $this->exists = true;
        }
    }

    /** @magic */
    public function __toString(): string
    {
        return $this->name;
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return ['name' => $this->name];
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get short name.
     *
     * @return string
     */
    public function getShortName(): string
    {
        return Objects::getShortName($this->name);
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
     * Exists state checker.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->exists;
    }

    /**
     * Constant existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function constantExists(string $name): bool
    {
        return $this->exists && constant_exists($this->name, $name);
    }

    /**
     * Property existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function propertyExists(string $name): bool
    {
        return $this->exists && property_exists($this->name, $name);
    }

    /**
     * Method existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function methodExists(string $name): bool
    {
        return $this->exists && method_exists($this->name, $name);
    }

    /** @alias constantExists() */
    public function hasConstant(string $name): bool
    {
        return $this->constantExists($name);
    }

    /** @alias propertyExists() */
    public function hasProperty(string $name): bool
    {
        return $this->propertyExists($name);
    }

    /** @alias methodExists() */
    public function hasMethod(string $name): bool
    {
        return $this->methodExists($name);
    }

    /**
     * Extends state checker.
     *
     * @param  string $parent
     * @param  bool   $parentOnly
     * @return bool
     */
    public function extends(string $parent, bool $parentOnly = false): bool
    {
        return $this->exists && class_extends($this->name, $parent, $parentOnly);
    }

    /**
     * Implements state checker.
     *
     * @param  string $interface
     * @return bool
     */
    public function implements(string $interface): bool
    {
        return $this->exists && in_array($interface, (array) $this->getInterfaces(), true);
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
        return $this->exists ? get_class_vars($this->name) : null;
    }

    /**
     * Get constants.
     *
     * @return array|null
     */
    public function getConstants(): array|null
    {
        return $this->exists ? get_class_constants($this->name) : null;
    }

    /**
     * Get properties.
     *
     * @return array|null
     */
    public function getProperties(): array|null
    {
        return $this->exists ? get_class_properties($this->name) : null;
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
     * @return string|null
     */
    public function getParent(): string|null
    {
        return $this->exists ? get_parent_class($this->name) : null;
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
     * Valid name checker.
     *
     * @return bool
     */
    public function isValidName(): bool
    {
        // Not for anonyms.
        return preg_test('~^[a-z][a-z0-9\\\]+$~i', $this->name);
    }

    /**
     * Alias name checker.
     *
     * @return bool
     */
    public function isAliasName(): bool
    {
        return $this->name !== $this->reflect()?->name;
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

    /** @alias extends() */
    public function isSubclassOf(string $class, bool $parentOnly = false): bool
    {
        return $this->extends($class, $parentOnly);
    }

    /** @alias extends() */
    public function isExtenderOf(string $class, bool $parentOnly = false): bool
    {
        return $this->extends($class, $parentOnly);
    }

    /** @alias implements() */
    public function isImplementerOf(string $interface): bool
    {
        return $this->implements($interface);
    }

    /** @alias uses() */
    public function isUserOf(string $trait): bool
    {
        return $this->uses($trait);
    }

    /**
     * Create a new instance this class.
     *
     * @param  mixed ...$args
     * @return object
     * @causes Error|Exception
     */
    public function init(mixed ...$args): object
    {
        return new $this->name(...$args);
    }

    /**
     * Reflect & return reflection, or null (error).
     *
     * @param  bool $extended
     * @return ReflectionClass|ReflectionClassExtended|null
     */
    public function reflect(bool $extended = false): ReflectionClass|ReflectionClassExtended|null
    {
        try {
            return !$extended
                 ? new ReflectionClass($this->name)
                 : new ReflectionClassExtended($this->name);
        } catch (ReflectionException) {
            return null;
        }
    }
}
