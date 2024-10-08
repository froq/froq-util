<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * A class for playing with types in OOP-way.
 *
 * @package global
 * @class   Type
 * @author  Kerem Güneş
 * @since   6.0
 */
class Type implements Stringable
{
    /** Type name. */
    public readonly string $name;

    /** Reflected variable. */
    public readonly mixed $var;

    /** Type reflection instance. */
    private readonly XReflectionType $reflection;

    /**
     * Constructor.
     *
     * @param mixed $var
     */
    public function __construct(mixed $var)
    {
        $this->name = get_type($var);
        $this->var  = $var;
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
     * Null check.
     *
     * @return bool
     */
    public function isNull(): bool
    {
        return is_null($this->var);
    }

    /**
     * Array check.
     *
     * @return bool
     */
    public function isArray(): bool
    {
        return is_array($this->var);
    }

    /**
     * Object check.
     *
     * @return bool
     */
    public function isObject(): bool
    {
        return is_object($this->var);
    }

    /**
     * Int check.
     *
     * @return bool
     */
    public function isInt(): bool
    {
        return is_int($this->var);
    }

    /**
     * Float check.
     *
     * @return bool
     */
    public function isFloat(): bool
    {
        return is_float($this->var);
    }

    /**
     * String check.
     *
     * @return bool
     */
    public function isString(): bool
    {
        return is_string($this->var);
    }

    /**
     * Bool check.
     *
     * @return bool
     */
    public function isBool(): bool
    {
        return is_bool($this->var);
    }

    /**
     * Callable check.
     *
     * @return bool
     */
    public function isCallable(): bool
    {
        return is_callable($this->var);
    }

    /**
     * Countable check.
     *
     * @return bool
     */
    public function isCountable(): bool
    {
        return is_countable($this->var);
    }

    /**
     * Iterable check.
     *
     * @return bool
     */
    public function isIterable(): bool
    {
        return is_iterable($this->var);
    }

    /**
     * Numeric check.
     *
     * @return bool
     */
    public function isNumeric(): bool
    {
        return is_numeric($this->var);
    }

    /**
     * Scalar check.
     *
     * @return bool
     */
    public function isScalar(): bool
    {
        return is_scalar($this->var);
    }

    /**
     * Resource check.
     *
     * @param  string|null $type
     * @return bool
     */
    public function isResource(string $type = null): bool
    {
        return ($type === null) ? is_resource($this->var) :
            is_resource($this->var) && get_resource_type($this->var) === $type;
    }

    /**
     * List check.
     *
     * @return bool
     */
    public function isList(): bool
    {
        return is_list($this->var);
    }

    /**
     * Number check.
     *
     * @return bool
     */
    public function isNumber(): bool
    {
        return is_number($this->var);
    }

    /**
     * Stream check.
     *
     * @return bool
     */
    public function isStream(): bool
    {
        return is_stream($this->var);
    }

    /**
     * Closure check.
     *
     * @return bool
     */
    public function isClosure(): bool
    {
        return is_closure($this->var);
    }

    /**
     * Primitive check.
     *
     * @return bool
     */
    public function isPrimitive(): bool
    {
        return is_primitive($this->var);
    }

    /**
     * Arrayable check.
     *
     * @param  bool|null &$native
     * @return bool
     */
    public function isArrayable(bool &$native = null): bool
    {
        return is_arrayable($this->var, $native);
    }

    /**
     * Stringable check.
     *
     * @param  bool|null &$native
     * @return bool
     */
    public function isStringable(bool &$native = null): bool
    {
        return is_stringable($this->var, $native);
    }

    /**
     * Plain object check.
     *
     * @return bool
     */
    public function isPlainObject(): bool
    {
        return ($this->var instanceof stdClass);
    }

    /**
     * Array-like check.
     *
     * @return bool
     */
    public function isArrayLike(): bool
    {
        return is_array($this->var) || ($this->var instanceof stdClass);
    }

    /**
     * Iterable-like check.
     *
     * @return bool
     */
    public function isIterableLike(): bool
    {
        return is_iterable($this->var) || ($this->var instanceof stdClass);
    }

    /**
     * Type-of check.
     *
     * @param  string ...$types
     * @return bool
     */
    public function isTypeOf(string ...$types): bool
    {
        return is_type_of($this->var, ...$types);
    }

    /**
     * Class-of check.
     *
     * @param  string ...$classes
     * @return bool
     */
    public function isClassOf(string ...$classes): bool
    {
        return is_object($this->var) && is_class_of($this->var, ...$classes);
    }

    /**
     * Subclass-of check.
     *
     * @param  string $class
     * @return bool
     */
    public function isSubclassOf(string $class): bool
    {
        return is_object($this->var) && is_subclass_of($this->var, $class);
    }

    /**
     * Instance-of check (faster).
     *
     * @param  string|object ...$classes
     * @return bool
     */
    public function isInstanceOf(string|object ...$classes): bool
    {
        return array_test($classes, fn($class): bool => $this->var instanceof $class);
    }

    /**
     * Reflect & return reflection of self var type.
     *
     * @return XReflectionType
     */
    public function reflect(): XReflectionType
    {
        return $this->reflection ??= XReflectionType::of($this->var);
    }

    /**
     * Static initializer.
     *
     * @param  mixed $var
     * @return Type
     */
    public static function of(mixed $var): Type
    {
        return new Type($var);
    }
}
