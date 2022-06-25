<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A class for references.
 *
 * @package froq\util
 * @object  Reference
 * @author  Kerem Güneş
 * @since   6.0
 */
class Reference extends PlainObject
{
    /**
     * Clear all references (properties).
     *
     * @return void
     */
    public function clear(): void
    {
        foreach ($this as $name => $_) {
            unset($this->$name);
        }
    }
}
