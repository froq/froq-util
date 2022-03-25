<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A class for dynamic properties.
 *
 * @package froq\util
 * @object  PlainObject
 * @author  Kerem Güneş
 * @since   6.0
 */
class PlainObject extends stdClass
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
}
