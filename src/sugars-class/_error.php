<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * @author Kerem Güneş
 * @since  5.25
 */
class KeyError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class JsonError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class LocaleError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.5
 */
class UrlError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UuidError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class RegExpError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class RangeError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class ArgumentError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UnknownError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UnsupportedError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UnimplementedError extends froq\common\Error
{}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class ReadonlyError extends froq\common\Error
{
    /**
     * Constructor.
     *
     * @param string|object $class
     * @param string|null   $property
     */
    public function __construct(string|object $class, string $property = null)
    {
        if (func_num_args() === 1) {
            parent::__construct(sprintf(
                'Cannot modify readonly class %s', get_class_name($class, escape: true)
            ));
        } else {
            parent::__construct(sprintf(
                'Cannot modify readonly property %s::$%s', get_class_name($class, escape: true), $property
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
 * @since  7.0
 */
class UndefinedClassError extends froq\common\Error
{
    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        parent::__construct(sprintf('Undefined class: %s', $class));
    }
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UndefinedConstantError extends froq\common\Error
{
    /**
     * Constructor.
     *
     * @param string|object|null $class
     * @param string             $constant
     */
    public function __construct(string|object|null $class, string $constant)
    {
        if ($class === null) {
            parent::__construct(sprintf('Undefined constant: %s', $constant));
        } else {
            parent::__construct(sprintf(
                'Undefined class constant: %s::%s', get_class_name($class, escape: true), $constant
            ));
        }
    }
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UndefinedPropertyError extends froq\common\Error
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
            'Undefined property: %s::$%s', get_class_name($class, escape: true), $property
        ));
    }
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class UndefinedMethodError extends froq\common\Error
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
            'Undefined method: %s::%s()', get_class_name($class, escape: true), $method
        ));
    }
}

/**
 * @author Kerem Güneş
 * @since  6.0
 */
class LastError extends froq\common\Error
{

    /** Causing call info. */
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
     * @return array|null
     */
    public function getCall(): array|null
    {
        return $this->call;
    }

    /**
     * Get call file.
     *
     * @return string|null
     */
    public function getCallFile(): string|null
    {
        return $this->call['file'] ?? null;
    }

    /**
     * Get call line.
     *
     * @return int|null
     */
    public function getCallLine(): int|null
    {
        return $this->call['line'] ?? null;
    }

    /**
     * Get call name.
     *
     * @return string|null
     */
    public function getCallName(): string|null
    {
        return $this->call['name'] ?? null;
    }

    /**
     * Get call path.
     *
     * @return string|null
     */
    public function getCallPath(): string|null
    {
        return $this->call['path'] ?? null;
    }
}
