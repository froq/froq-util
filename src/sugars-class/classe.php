<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Objects;

/**
 * Classe.
 *
 * A class for playing with classes OOP-way.
 *
 * @package froq\util
 * @object  Classe
 * @author  Kerem Güneş
 * @since   6.0
 */
final class Classe
{
    /** @var string */
    public readonly string $class;

    // /** @var string */
    // public readonly string|null $classAlias;

    /** @var bool */
    private readonly bool $exists;

    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;

        // Help for autoloader & shortcut tick.
        $this->exists = class_exists($class, true);

        // Nope.. (use isAliasName() & getRealName()).
        // if ($this->exists) {
        //     $ref = new ReflectionClass($class);
        //     $this->class = ($class !== $ref->name) ? $ref->name : $class;
        //     $this->classAlias = $class;
        // } else {
        //     $this->class = $class;
        //     $this->classAlias = null;
        // }
    }

    /** @magic __toString() */
    public function __toString(): string
    {
        return $this->class;
    }

    /** @magic __debugInfo() */
    public function __debugInfo(): array
    {
        return [
            'class' => $this->class,
            // 'classAlias' => $this->classAlias
        ];
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->class;
    }

    /**
     * Get short name.
     *
     * @return string
     */
    public function getShortName(): string
    {
        return Objects::getShortName($this->class);
    }

    /**
     * Get real name (for aliases).
     *
     * @return string
     */
    public function getRealName(): string
    {
        return Objects::getRealName($this->class);
    }

    /**
     * Exists state checker.
     *
     * @param  bool $autoload
     * @return bool
     */
    public function exists(bool $autoload = false): bool
    {
        return $autoload ? class_exists($this->class, $autoload) : $this->exists;
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
        return $this->exists && class_extends($this->class, $parent, $parentOnly);
    }

    /**
     * Implements state checker.
     *
     * @param  string $class
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
     * Constant existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function hasConstant(string $name): bool
    {
        return $this->exists && constant_exists($this->class, $name);
    }

    /**
     * Property existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function hasProperty(string $name): bool
    {
        return $this->exists && property_exists($this->class, $name);
    }

    /**
     * Method existence checker.
     *
     * @param  string $name
     * @return bool
     */
    public function hasMethod(string $name): bool
    {
        return $this->exists && method_exists($this->class, $name);
    }

    /**
     * Get vars.
     *
     * @return array|null
     */
    public function getVars(): array|null
    {
        return $this->exists ? get_class_vars($this->class) : null;
    }

    /**
     * Get constants.
     *
     * @return array|null
     */
    public function getConstants(): array|null
    {
        return $this->exists ? get_class_constants($this->class) : null;
    }

    /**
     * Get properties.
     *
     * @return array|null
     */
    public function getProperties(): array|null
    {
        return $this->exists ? get_class_properties($this->class) : null;
    }

    /**
     * Get methods.
     *
     * @return array|null
     */
    public function getMethods(): array|null
    {
        return $this->exists ? get_class_methods($this->class) : null;
    }

    /**
     * Get parent.
     *
     * @return string|null
     */
    public function getParent(): string|null
    {
        return $this->exists ? get_parent_class($this->class) : null;
    }

    /**
     * Get parents.
     *
     * @return array|null
     */
    public function getParents(): array|null
    {
        return $this->exists ? Objects::getParents($this->class) : null;
    }

    /**
     * Get interfaces.
     *
     * @return array|null
     */
    public function getInterfaces(): array|null
    {
        return $this->exists ? Objects::getInterfaces($this->class) : null;
    }

    /**
     * Get traits.
     *
     * @return array|null
     */
    public function getTraits(): array|null
    {
        return $this->exists ? Objects::getTraits($this->class) : null;
    }

    /**
     * Valid name checker.
     *
     * @return bool
     */
    public function isValidName(): bool
    {
        // Not for anonyms.
        return preg_test('~^[a-z][a-z0-9\\\]+$~i', $this->class);
    }

    /**
     * Alias name checker.
     *
     * @return bool
     */
    public function isAliasName(): bool
    {
        return $this->class !== $this->reflect()?->name;
    }

    /**
     * Type checker.
     *
     * @param  string $class
     * @return bool
     */
    public function isTypeOf(string $class): bool
    {
        return $this->class === $class;
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
        return is_class_of($this->class, $class, ...$classes);
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
     * @causes {Error|Exception}
     */
    public function init(mixed ...$args): object
    {
        return new $this->class(...$args);
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
                 ? new ReflectionClass($this->class)
                 : new ReflectionClassExtended($this->class);
        } catch (ReflectionException) {
            return null;
        }
    }
}
