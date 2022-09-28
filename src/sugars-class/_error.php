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
 * @author Kerem Güneş
 * @since  6.0
 */
trait ErrorTrait
{
    /**
     * Constructor.
     *
     * @param string|Throwable|null $message
     * @param mixed|null            $messageParams
     * @param int|null              $code
     * @param Throwable|null        $previous
     * @param Throwable|null        $cause          Not used.
     */
    public function __construct(string $message = null, mixed $messageParams = null, int $code = null,
        Throwable $previous = null, /* Throwable $cause = null */)
    {
        // Formattable message with params.
        if ($message && func_num_args() > 1) {
            $message = format($message, ...array_values(
                is_array($messageParams) || is_scalar($messageParams)
                    ? (array) $messageParams : [$messageParams]
            ));
        }

        parent::__construct((string) $message, (int) $code, $previous);
    }

    /**
     * To get rid of calling get*() methods for those readonly properties.
     */
    public function __get(string $property): mixed
    {
        switch ($property) {
            case 'trace':
                return $this->getTrace();
            case 'traceString':
                return $this->getTraceAsString();
        }

        // Note: Subclasses must define properties as "protected".
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        // Act as original.
        $message = sprintf('Undefined property: %s::$%s', $this::class, $property);
        trigger_error($message, E_USER_WARNING);

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
 * @author Kerem Güneş
 * @since  5.25
 */
class KeyError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class JsonError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class LocaleError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UuidError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class RegExpError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class RangeError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class ArgumentError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UnknownError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UnsupportedError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UnimplementedError extends Error
{
    use ErrorTrait;
}

/**
 * @author Kerem Güneş
 * @since  6.0
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
 * @author Kerem Güneş
 * @since  6.0
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
 * @author Kerem Güneş
 * @since  6.0
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
 * @author Kerem Güneş
 * @since  6.0
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
 * @author Kerem Güneş
 * @since  6.0
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
 * @author Kerem Güneş
 * @since  6.0
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
 * @author Kerem Güneş
 * @since  6.0
 */
class LastError extends Error
{
    use ErrorTrait;

    /** Causing function name. */
    private ?string $call = null;

    /** Caused file/line info. */
    private ?string $callPath = null;

    /**
     * Constructor.
     *
     * @param string|null $message
     * @param int|null    $code
     */
    public function __construct(string $message = null, int $code = null)
    {
        // Normal process with last error when no arguments given.
        if (!func_num_args()) {
            if ($error = get_error()) {
                ['type' => $code, 'message' => $message] = $error;
                $this->call = $error['function'];
                $this->callPath = $error['file'] .':'. $error['line'];
            } else {
                // Invalid call/throw.
                [$code, $message] = [-1, 'No error'];
            }
        }

        parent::__construct((string) $message, (int) $code);
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
