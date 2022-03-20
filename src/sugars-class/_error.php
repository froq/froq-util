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
    /**
     * To get rid of calling get*() methods for those readonly properties.
     */
    public function __get(string $property): mixed
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        if ($property == 'trace') {
            return $this->getTrace();
        }

        // Act as original.
        trigger_error(sprintf(
            'Undefined property: %s::$%s', static::class, $property
        ), E_USER_WARNING);

        return null;
    }

    /**
     * To get a proper string representation with code.
     */
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
 * @package froq\util
 * @object  ArgumentError
 * @author  Kerem Güneş
 * @since   6.0
 */
class ArgumentError extends Error
{
    use ErrorTrait;
}

/**
 * @package froq\util
 * @object  UnknownError
 * @author  Kerem Güneş
 * @since   6.0
 */
class UnknownError extends Error
{
    use ErrorTrait;
}

/**
 * @package froq\util
 * @object  UnsupportedError
 * @author  Kerem Güneş
 * @since   6.0
 */
class UnsupportedError extends Error
{
    use ErrorTrait;
}

/**
 * @package froq\util
 * @object  UnimplementedError
 * @author  Kerem Güneş
 * @since   6.0
 */
class UnimplementedError extends Error
{
    use ErrorTrait;
}

/**
 * @package froq\util
 * @object  ReadonlyError
 * @author  Kerem Güneş
 * @since   6.0
 */
class ReadonlyError extends Error
{
    use ErrorTrait;

    /**
     * Constructor.
     *
     * @param string|object $class
     * @param string|null   $property
     */
    public function __construct(string|object $class, string $property = null)
    {
        if (func_num_args() == 1) {
            parent::__construct(sprintf(
                'Cannot modify readonly class %s', get_class_name($class)
            ));
        } else {
            parent::__construct(sprintf(
                'Cannot modify readonly property %s::$%s', get_class_name($class), $property
            ));
        }
    }
}

/**
 * @package froq\util
 * @object  ReadonlyClassError
 * @author  Kerem Güneş
 * @since   6.0
 */
class ReadonlyClassError extends ReadonlyError
{
    /**
     * Constructor.
     *
     * @param string|object $class
     */
    public function __construct(string|object $class)
    {
        parent::__construct($class);
    }
}

/**
 * @package froq\util
 * @object  ReadonlyPropertyError
 * @author  Kerem Güneş
 * @since   6.0
 */
class ReadonlyPropertyError extends ReadonlyError
{
    /**
     * Constructor.
     *
     * @param string|object $class
     * @param string        $property
     */
    public function __construct(string|object $class, string $property)
    {
        parent::__construct($class, $property);
    }
}

/**
 * @package froq\util
 * @object  UndefinedConstantError
 * @author  Kerem Güneş
 * @since   6.0
 */
class UndefinedConstantError extends Error
{
    use ErrorTrait;

    /**
     * Constructor.
     *
     * @param string|object|null $class
     * @param string             $constant
     */
    public function __construct(string|object|null $class, string $constant)
    {
        if ($class === null) {
            parent::__construct(sprintf(
                'Undefined constant %s', $constant
            ));
        } else {
            parent::__construct(sprintf(
                'Undefined class constant %s::%s', get_class_name($class), $constant
            ));
        }
    }
}

/**
 * @package froq\util
 * @object  UndefinedPropertyError
 * @author  Kerem Güneş
 * @since   6.0
 */
class UndefinedPropertyError extends Error
{
    use ErrorTrait;

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
 * @package froq\util
 * @object  UndefinedMethodError
 * @author  Kerem Güneş
 * @since   6.0
 */
class UndefinedMethodError extends Error
{
    use ErrorTrait;

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

/**
 * @package froq\util
 * @object  LastError
 * @author  Kerem Güneş
 * @since   6.0
 */
class LastError extends Error
{
    use ErrorTrait;

    /** @var ?string */
    private readonly ?string $call;

    /** @var ?string */
    private readonly ?string $callPath;

    /**
     * Constructor.
     */
    public function __construct()
    {
        if ($error = get_error()) {
            ['type' => $code, 'message' => $message] = $error;
            $this->call = $error['function'];
            $this->callPath = $error['file'] .':'. $error['line'];
        } else {
            // Invalid call/throw.
            [$code, $message] = [-1, 'No error'];
            $this->call = $this->callPath = null;
        }

        parent::__construct($message, $code);
    }

    /**
     * Get call.
     *
     * @return ?string
     */
    public function getCall(): ?string
    {
        return $this->call;
    }

    /**
     * Get call path.
     *
     * @return ?string
     */
    public function getCallPath(): ?string
    {
        return $this->callPath;
    }
}
