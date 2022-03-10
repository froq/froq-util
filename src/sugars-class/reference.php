<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Reference.
 *
 * A class for single reference.
 *
 * @package froq\util
 * @object  Reference
 * @author  Kerem Güneş
 * @since   6.0
 */
class Reference
{
    /**
     * Constructor.
     *
     * @param mixed $value
     */
    public function __construct(public mixed $value)
    {}
}

/**
 * References.
 *
 * A class for multiple references.
 *
 * @package froq\util
 * @object  References
 * @author  Kerem Güneş
 * @since   6.0
 */
class References extends stdClass
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
}
