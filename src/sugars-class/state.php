<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A class for states as reference.
 *
 * @package froq\util
 * @object  State
 * @author  Kerem Güneş
 * @since   6.0
 */
class State extends PlainObject
{
    /**
     * Constructor.
     *
     * @param mixed ...$states
     */
    public function __construct(mixed ...$states)
    {
        parent::__construct(...$this->prepare($states));
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
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->$name ?? $default;
    }

    /**
     * Reset given states.
     *
     * @param  mixed ...$states
     * @return self
     */
    public function reset(mixed ...$states): self
    {
        // With no existence check.
        foreach ($this->prepare($states) as $name => $value) {
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * Prepare states, when a list given use list[0] else all named params.
     */
    private function prepare(array $states): array
    {
        // When a list of states given (eg: ["a" => 1, ..]).
        if ($states && is_list($states)) {
            $states = $states[0];
        }

        return $states;
    }
}
