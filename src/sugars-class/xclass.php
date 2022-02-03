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
 * A class for playing with classes OOP-way.
 *
 * @package froq\util
 * @object  XClass
 * @author  Kerem Güneş
 * @since   6.0
 */
class XClass
{
    /** @var string */
    public readonly string $name;

    // /** @var string */
    // public readonly string|null $nameAlias;

    /** @var bool */
    private readonly bool $exists;

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

        // Nope.. (use isAliasName() & getRealName()).
        // if ($this->exists) {
        //     $ref = new ReflectionClass($name);
        //     $this->name = ($name !== $ref->name) ? $ref->name : $name;
        //     $this->nameAlias = $name;
        // } else {
        //     $this->name = $name;
        //     $this->nameAlias = null;
        // }
    }

    /** @magic __toString() */
    public function __toString(): string
    {
        return $this->name;
    }

    /** @magic __debugInfo() */
    public function __debugInfo(): array
    {
        return [
            'name' => $this->name,
            // 'nameAlias' => $this->nameAlias
        ];
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
    public function existsConstant(string $name): bool
    {
        return $this->exists && constant_exists($this->name, $name);
    }

    /**
     * Property existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function existsProperty(string $name): bool
    {
        return $this->exists && property_exists($this->name, $name);
    }

    /**
     * Method existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function existsMethod(string $name): bool
    {
        return $this->exists && method_exists($this->name, $name);
    }

    /** @aliasOf existsConstant() */
    public function hasConstant(string $name): bool
    {
        return $this->existsConstant($name);
    }

    /** @aliasOf existsProperty() */
    public function hasProperty(string $name): bool
    {
        return $this->existsProperty($name);
    }

    /** @aliasOf existsMethod() */
    public function hasMethod(string $name): bool
    {
        return $this->existsMethod($name);
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
        return $this->exists && in_array($interface, (array) $this->getInterfaces());
    }

    /**
     * Uses state checker.
     *
     * @param  string $trait
     * @return bool
     */
    public function uses(string $trait): bool
    {
        return $this->exists && in_array($trait, (array) $this->getTraits());
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
     * Type checker.
     *
     * @param  string $class
     * @return bool
     */
    public function isTypeOf(string $class): bool
    {
        return $this->name === $class;
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

    /** @aliasOf extends() */
    public function isSubclassOf(string $class, bool $parentOnly = false): bool
    {
        return $this->extends($class, $parentOnly);
    }

    /** @aliasOf extends() */
    public function isExtenderOf(string $class, bool $parentOnly = false): bool
    {
        return $this->extends($class, $parentOnly);
    }

    /** @aliasOf implements() */
    public function isImplementerOf(string $interface): bool
    {
        return $this->implements($interface);
    }

    /** @aliasOf uses() */
    public function isUserOf(string $trait): bool
    {
        return $this->uses($trait);
    }

    /** @aliasOf init() */
    public function new(mixed ...$args): object
    {
        return $this->init(...$args);
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
