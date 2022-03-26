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
        // When a list of states given (eg: ["a" => 1, ..]).
        if ($states && is_list($states)) {
            $states = $states[0];
        }

        parent::__construct(...$states);
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
}
