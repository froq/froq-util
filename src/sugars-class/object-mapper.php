<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A class for mapping some data as object properties.
 *
 * @package global
 * @object  ObjectMapper
 * @author  Kerem Güneş
 * @since   6.0
 */
class ObjectMapper
{
    /** The target class/object. */
    private string|object $target;

    /** Mapping options. */
    private array $options = [
        'skip'   => null,  // Array of ignored properties.
        'throw'  => false, // For absent property errors.
        'filter' => null,  // For custom filtering to set property values.
        'cast'   => false, // For casting properties as their types if given.
    ];

    /**
     * Constructor.
     *
     * @param  string|object|null $target
     * @param  array|null         $options
     */
    public function __construct(string|object $target = null, array $options = null)
    {
        $target  && $this->target  = $target;
        $options && $this->options = array_options($options, $this->options, map: false);
    }

    /**
     * Set target.
     *
     * @param  string|object $target
     * @return self
     */
    public function setTarget(string|object $target): self
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Get target.
     *
     * @return string|object|null
     */
    public function getTarget(): string|object|null
    {
        return $this->target ?? null;
    }

    /**
     * Set an option.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return self
     */
    public function setOption(string $key, mixed $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Get an option.
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed|null
     */
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Map given data on target.
     *
     * @param  iterable $data
     * @return object
     * @throws Error
     */
    public function map(iterable $data): object
    {
        $target = $this->getTarget()
               ?: throw new Error('No target given yet, call setTarget()');

        if (is_string($target)) {
            $target = new $target();
        }

        foreach ($data as $name => $value) {
            $this->setPropertyValue($target, $name, $value);
        }

        return $target;
    }

    /**
     * Set property value by given/default options.
     *
     * @throws UndefinedPropertyError If options.throws is true (for absent properties).
     */
    private function setPropertyValue(object $object, string $name, mixed $value): void
    {
        // Skip filtered properties (if provied).
        if ($this->options['filter'] &&
            !call_user_func_array($this->options['filter'], [$name, $value])) {
            return;
        }

        // Skip ignored properties (if provied).
        if ($this->options['skip'] &&
            in_array($name, $this->options['skip'], true)) {
            return;
        }

        // Skip absent properties.
        if (!property_exists($object, $name)) {
            if ($this->options['throw']) {
                throw new UndefinedPropertyError($object, $name);
            }
            return;
        }

        $ref = new ReflectionProperty($object, $name);

        // Skip statics.
        if ($ref->isStatic()) {
            return;
        }

        // Apply cast option.
        if ($this->options['cast']) {
            $type = $ref->getType();
            if ($type instanceof ReflectionNamedType && $type->isBuiltin()) {
                settype($value, $type->getName());
            }
        }

        $ref->setValue($object, $value);
    }
}
