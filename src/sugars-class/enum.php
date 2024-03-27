<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * An enum class with some utilities.
 *
 * @package global
 * @class   Enum
 * @author  Kerem Güneş
 * @since   7.14
 */
class Enum implements Stringable
{
    /**
     * Constant cache.
     * @var array<string, Map>
     */
    private static array $cache;

    /** Enum unit instance. */
    public readonly EnumUnit $unit;

    /**
     * Constructor.
     *
     * @param  string|null                $name
     * @param  int|float|string|bool|null $value
     * @param  bool                       $check
     * @throws EnumError
     */
    public function __construct(string|null $name, int|float|string|bool|null $value = null, bool $check = false)
    {
        $name || throw new EnumError('Empty/null name given');

        // Auto-detect value from given name.
        if (func_num_args() === 1 && self::hasName($name)) {
            $value = self::valueOf($name);
        }

        if ($check) {
            if (!self::hasName($name)) {
                throw new EnumError(format('Undefined name %q', $name));
            } elseif (self::valueOf($name) !== $value) {
                throw new EnumError(
                    ($value === null)
                        ? format('Undefined name %q for value null', $name)
                        : format('Undefined name %q for value %s', $name, new EnumUnit($name, $value))
                );
            }
        }

        $this->unit = new EnumUnit($name, $value);
    }

    /**
     * @magic
     */
    public function __toString()
    {
        return $this->unit->__toString();
    }

    /**
     * @throws EnumError
     * @magic
     */
    public function __get(string $name): int|float|string|bool|null
    {
        return match ($name) {
            'name'  => $this->unit->name,
            'value' => $this->unit->value,
            default => throw new EnumError(format(
                'Undefined property %S::$%s', static::class, $name
            ))
        };
    }

    /**
     * Check if this value equals given value.
     *
     * @param  int|float|string|bool|null|EnumUnit $value
     * @return bool
     */
    public function equals(int|float|string|bool|null|EnumUnit $value): bool
    {
        if ($value instanceof EnumUnit) {
            return $this->unit->name  === $value->name
                && $this->unit->value === $value->value;
        }

        return $this->unit->value === $value;
    }

    /**
     * Get all constants as assoc array.
     *
     * @return array<string, ?>
     * @alias  toArray()
     */
    public static function all(): array
    {
        return self::toArray();
    }

    /**
     * Get all constants as list array consisting of enum units.
     *
     * @return array<EnumUnit>
     */
    public static function units(): array
    {
        foreach (self::toArray() as $name => $value) {
            $ret[] = new EnumUnit($name, $value);
        }

        return $ret ?? [];
    }

    /**
     * Get constant names.
     *
     * @return array
     */
    public static function names(): array
    {
        return self::map()->keys();
    }

    /**
     * Get constant values.
     *
     * @return array
     */
    public static function values(): array
    {
        return self::map()->values();
    }

    /**
     * Get name of given value.
     *
     * @param  int|float|string|bool|null $value
     * @return string|null
     */
    public static function nameOf(int|float|string|bool|null $value): string|null
    {
        return self::map()->keyOf($value);
    }

    /**
     * Get value of given name.
     *
     * @param  string $name
     * @return int|float|string|bool|null
     */
    public static function valueOf(string $name): int|float|string|bool|null
    {
        return self::map()->valueOf($name);
    }

    /**
     * Check if given name exists.
     *
     * @param  string $name
     * @return bool
     */
    public static function hasName(string $name): bool
    {
        return self::map()->has($name);
    }

    /**
     * Check if given value exists.
     *
     * @param  int|float|string|bool|null $value
     * @return bool
     */
    public static function hasValue(int|float|string|bool|null $value): bool
    {
        return self::map()->hasValue($value);
    }

    /**
     * Static initializer with given name.
     *
     * @param  string $name
     * @return static
     * @causes EnumError
     */
    public static function fromName(string $name): static
    {
        return new static($name, self::valueOf($name));
    }

    /**
     * Static initializer with given value.
     *
     * @param  int|float|string|bool|null $value
     * @return static
     * @causes EnumError
     */
    public static function fromValue(int|float|string|bool|null $value): static
    {
        return new static(self::nameOf($value), $value);
    }

    /**
     * Static initializer with given unit.
     *
     * @param  EnumUnit $unit
     * @return static
     * @causes EnumError
     */
    public static function fromUnit(EnumUnit $unit): static
    {
        return new static($unit->name, $unit->value);
    }

    /**
     * Get constant array.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return self::map()->toArray();
    }

    /**
     * Get constant map.
     *
     * @return Map
     */
    public static function toMap(): Map
    {
        return self::map();
    }

    /**
     * Get (public) constant map of subclass, or return cached map if any.
     *
     * @internal
     */
    private static function map(): Map
    {
        return self::$cache[static::class] ??= new Map(
            (new ReflectionClass(static::class))
                ->getConstants(ReflectionClassConstant::IS_PUBLIC)
        );
    }
}

/**
 * An enum unit class as data holder.
 *
 * @package global
 * @class   EnumUnit
 * @author  Kerem Güneş
 * @since   7.14
 */
class EnumUnit implements Stringable
{
    /**
     * @constructor
     */
    public function __construct(
        public readonly string $name,
        public readonly int|float|string|bool|null $value
    )
    {}

    /**
     * @magic
     */
    public function __toString(): string
    {
        return match (true) {
            is_string($this->value) => $this->value,
            is_number($this->value) => format_number($this->value, true, '.', ''),
            is_bool($this->value)   => format_bool($this->value, false),
            default                 => '', // Null.
        };
    }
}
