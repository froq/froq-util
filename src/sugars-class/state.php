<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * A class for dynamic states as reference.
 *
 * @package global
 * @class   State
 * @author  Kerem Güneş
 * @since   6.0
 */
class State extends PlainArrayObject
{
    /**
     * Constructor.
     *
     * @param mixed ...$states Map of named arguments.
     * @override
     */
    public function __construct(mixed ...$states)
    {
        parent::__construct(...$this->prepare($states));
    }

    /**
     * Check a state.
     *
     * @param  string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return property_exists($this, $name);
    }

    /**
     * Set a state.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return self
     */
    public function set(string $name, mixed $value): self
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * Get a state or default.
     *
     * @param  string     $name
     * @param  mixed|null $default
     * @return mixed|null
     */
    public function &get(string $name, mixed $default = null): mixed
    {
        $value = &$this->$name ?? $default;

        return $value;
    }

    /**
     * Remove given states.
     *
     * @param  string ...$names
     * @return self
     */
    public function remove(string ...$names): self
    {
        foreach ($names as $name) {
            unset($this->$name);
        }

        return $this;
    }

    /**
     * Update given states.
     *
     * @param  mixed ...$states
     * @return self
     */
    public function update(mixed ...$states): self
    {
        // With no existence check.
        foreach ($this->prepare($states) as $name => $value) {
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * Clear all states.
     *
     * @return void
     */
    public function clear(): void
    {
        foreach ($this->getVarNames() as $name) {
            unset($this->$name);
        }
    }

    /**
     * Prepare states, when a list given use list[0] else all named params.
     */
    private function prepare(array $states): array
    {
        // When no named params given.
        if ($states && is_list($states)) {
            $states = first($states);
        }

        return (array) $states;
    }
}
