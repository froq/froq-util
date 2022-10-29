<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\common\Error;
use froq\common\trait\ThrowableTrait as ErrorTrait;

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
 * @since  6.5
 */
class UrlError extends Error
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

    /** Causing function data. */
    private ?array $call = null;

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

                // Fill call data.
                $this->call = [
                    'file' => $error['file'],
                    'line' => $error['line'],
                    'name' => $error['function'],
                    'path' => $error['file'] .':'. $error['line'],
                ];
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
     * @return ?array
     */
    public function getCall(): ?array
    {
        return $this->call;
    }

    /**
     * Get call file.
     *
     * @return ?string
     */
    public function getCallFile(): ?string
    {
        return $this->call['file'] ?? null;
    }

    /**
     * Get call line.
     *
     * @return ?int
     */
    public function getCallLine(): ?int
    {
        return $this->call['line'] ?? null;
    }

    /**
     * Get call name.
     *
     * @return ?string
     */
    public function getCallName(): ?string
    {
        return $this->call['name'] ?? null;
    }

    /**
     * Get call path.
     *
     * @return ?string
     */
    public function getCallPath(): ?string
    {
        return $this->call['path'] ?? null;
    }
}
