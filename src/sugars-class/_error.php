<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A trait for property access and a proper string representation,
 * used by errors below.
 *
 * @package froq\util
 * @object  ErrorTrait
 * @author  Kerem Güneş
 * @since   6.0
 */
trait ErrorTrait
{
    /** @magic */
    public function __get(string $property): mixed
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        trigger_error(
            'Undefined property: '. $this::class .'::$'. $property,
            E_USER_WARNING // Act like original.
        );

        return null;
    }

    /** @magic */
    public function __toString(): string
    {
        $ret = trim(parent::__toString());

        // Stack trace: ... => Trace: ...
        $ret = preg_replace('~Stack trace:~', 'Trace:', $ret, 1);

        // Error: ... => Error(123): ...
        return preg_replace('~^([^: ]+):* (.+)~', '\1('. $this->code .'): \2', $ret, 1);
    }
}

/**
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
 * An error class for JSON stuff (which is missing internally, suppose).
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
 * An error class for RegExp (which is missing internally, suppose).
 *
 * @package froq\util
 * @object  RegExpError
 * @author  Kerem Güneş
 * @since   6.0
 */
class RegExpError extends Error
{
    use ErrorTrait;
}

/**
 * An error class for readonly stuff (which is missing internally, suppose).
 *
 * @package froq\util
 * @object  ReadonlyError
 * @author  Kerem Güneş
 * @since   6.0
 */
class ReadonlyError extends Error
{
    use ErrorTrait;
}

/**
 * An error class for some range stuff.
 *
 * @package froq\util
 * @object  RangeError
 * @author  Kerem Güneş
 * @since   6.0
 */
class RangeError extends Error
{
    use ErrorTrait;
}

/**
 * An error class utilies error_get_last() stuff.
 *
 * @package froq\util
 * @object  LastError
 * @author  Kerem Güneş
 * @since   6.0
 */
class LastError extends Error
{
    use ErrorTrait;

    /**
     * Constructor.
     *
     * @param bool $format
     * @param bool $extract
     * @param bool $clear
     */
    public function __construct(bool $format = false, bool $extract = false, bool $clear = false)
    {
        $message = error_message($code, $format, $extract, $clear);

        parent::__construct((string) $message, (int) $code);
    }
}

/**
 * An error class for undefined constants/class constants.
 *
 * @package froq\util
 * @object  UndefinedConstantError
 * @author  Kerem Güneş
 * @since   6.0
 */
class UndefinedConstantError extends Error
{
    /**
     * Constructor.
     *
     * @param string|object|null $class
     * @param string             $constant
     */
    public function __construct(string|object|null $class, string $constant)
    {
        if ($class) {
            parent::__construct(sprintf(
                'Undefined constant %s::%s', get_class_name($class), $constant
            ));
        } else {
            parent::__construct(sprintf(
                'Undefined constant %s', $constant
            ));
        }
    }
}

/**
 * An error class for undefined class properties.
 *
 * @package froq\util
 * @object  UndefinedPropertyError
 * @author  Kerem Güneş
 * @since   6.0
 */
class UndefinedPropertyError extends Error
{
    /**
     * Constructor.
     *
     * @param string|object $class
     * @param string        $property
     */
    public function __construct(string|object $class, string $property)
    {
        parent::__construct(sprintf(
            'Undefined property %s::$%s', get_class_name($class), $property
        ));
    }
}

/**
 * An error class for undefined class methods.
 *
 * @package froq\util
 * @object  UndefinedMethodError
 * @author  Kerem Güneş
 * @since   6.0
 */
class UndefinedMethodError extends Error
{
    /**
     * Constructor.
     *
     * @param string|object $class
     * @param string        $method
     */
    public function __construct(string|object $class, string $method)
    {
        parent::__construct(sprintf(
            'Undefined method %s::%s()', get_class_name($class), $method
        ));
    }
}
