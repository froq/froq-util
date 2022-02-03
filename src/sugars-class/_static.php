<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Static Class.
 *
 * An uninitializable static class that forbid initializations of the extender classes.
 * I wish it was part of PHP but not (@see http://wiki.php.net/rfc/static-classes).
 *
 * Note: not abstract'ed, letting the error in constructor.
 *
 * @package froq\util
 * @object  StaticClass
 * @author  Kerem Güneş
 * @since   4.0, 6.0
 */
class StaticClass
{
    use StaticClassTrait;
}

/**
 * Static Class Trait.
 *
 * A trait entity which is able to forbid initialions on user object.
 *
 * @package froq\util
 * @object  StaticClassTrait
 * @author  Kerem Güneş
 * @since   4.3, 6.0
 */
trait StaticClassTrait
{
    /**
     * Constructor.
     *
     * @throws Error
     */
    public final function __construct()
    {
        throw new Error('Cannot initialize static class ' . static::class);
    }
}
