<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * A class for references.
 *
 * @package global
 * @class   Reference
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
        foreach ($this->getVarNames() as $name) {
            unset($this->$name);
        }
    }
}
