<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Error Trait.
 *
 * A trait for a proper string representation & used by errors below.
 *
 * @package froq\util
 * @object  ErrorTrait
 * @author  Kerem Güneş
 * @since   6.0
 */
trait ErrorTrait
{
    /** @magic __toString() */
    public function __toString(): string
    {
        // Eg: Error: ... => Error(123): ...
        return preg_replace('~^(.+?): *(.+)~', '\1('. $this->code .'): \2',
            parent::__toString());
    }
}

/**
 * Key Error.
 *
 * An error class for invalid keys (which is missing internally).
 *
 * @package froq\util
 * @object  KeyError
 * @author  Kerem Güneş
 * @since   5.25
 */
class KeyError extends Error
{
    use ErrorTrait;
}

/**
 * Json Error.
 *
 * An error class for for JSONs (which is missing internally, suppose).
 *
 * @package froq\util
 * @object  JsonError
 * @author  Kerem Güneş
 * @since   6.0
 */
class JsonError extends Error
{
    use ErrorTrait;
}

/**
 * RegExp Error.
 *
 * An error class for for RegExp (which is missing internally, suppose).
 *
 * @package froq\util
 * @object  RegExpErrorError
 * @author  Kerem Güneş
 * @since   6.0
 */
class RegExpError extends Error
{
    use ErrorTrait;
}