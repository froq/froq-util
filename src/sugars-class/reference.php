<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Reference.
 *
 * A class for references with dynamic properties.
 *
 * @package froq\util
 * @object  Reference
 * @author  Kerem Güneş
 * @since   6.0
 */
class Reference extends stdClass
{
    /**
     * Constructor.
     *
     * @param mixed ...$properties
     */
    public function __construct(mixed ...$properties)
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * For getting properties safely.
     *
     * @param  string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->$name ?? null;
    }

    /**
     * Clear all properties.
     *
     * @return void
     */
    public function clear(): void
    {
        foreach (get_object_vars($this) as $name => $_) {
            unset($this->$name);
        }
    }
}
