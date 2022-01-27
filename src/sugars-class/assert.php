<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\common\{Error, Exception};

/**
 * Assert.
 *
 * As assert(ion) class with some utility methods.
 *
 * @package froq\util
 * @object  Assert
 * @author  Kerem Güneş
 * @since   6.0
 */
final class Assert
{
    /** @var string Default exception class. */
    public static string $exception = AssertException::class;

    /**
     * Constructor.
     *
     * @param string|null $exception
     */
    public function __construct(string $exception = null)
    {
        if ($exception !== null) {
            self::setException($exception);
        } else {
            self::resetException();
        }
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        self::resetException();
    }

    /**
     * Set exception.
     *
     * @param  string $exception
     * @return void
     */
    public static function setException(string $exception): void
    {
        self::$exception = $exception;
    }

    /**
     * Get exception.
     *
     * @return string
     */
    public static function getException(): string
    {
        return self::$exception;
    }

    /**
     * Reset exception.
     *
     * @return void
     */
    public static function resetException(): void
    {
        self::$exception = AssertException::class;
    }

    /**
     * Validate exception.
     *
     * @param  string $exception
     * @return bool
     */
    public static function validateException(string $exception): bool
    {
        return is_subclass_of($exception, Throwable::class);
    }

    /**
     * Message preparer.
     *
     * @param  array<string,Throwable>|string|Throwable $message
     * @return Throwable
     * @throws AssertException
     */
    public static function message(array|string|Throwable $message): Throwable
    {
        if (is_array($message)) {
            @ [$message, $exception] = $message;

            $exception = (string) $exception;
            if (!$exception || !self::validateException($exception)) {
                throw new AssertException('Invalid exception: ' . ($exception ?: 'null'));
            }

            // Add cause for froq error/exception stuff.
            if (!is_class_of($exception, Error::class, Exception::class)) {
                $message = new $exception((string) $message, cause: new AssertException());
            } else {
                $message = new $exception((string) $message);
            }
        } elseif (is_string($message)) {
            $exception = self::$exception;
            if (!$exception || !self::validateException($exception)) {
                throw new AssertException('Invalid exception: ' . ($exception ?: 'null'));
            }

            $message = new $exception($message);
        }

        return $message;
    }

    /**
     * Throw given message when assertion fails.
     *
     * @param  bool                                     $assertion
     * @param  array<string,Throwable>|string|Throwable $message
     * @return never
     * @throws Throwable|AssertException
     */
    public static function throw(bool $assertion, array|string|Throwable $message): never
    {
        if (!$assertion) {
            throw self::message($message);
        }
    }

    /**
     * Check & throw given message/throwable optionally when assertion fails.
     *
     * @param  bool                                          $assertion
     * @param  array<string,Throwable>|string|Throwable|null $message
     * @return bool
     * @throws Throwable|AssertException
     */
    public static function check(bool $assertion, array|string|Throwable $message = null): bool
    {
        if (!$assertion && $message !== null) {
            throw self::message($message);
        }

        return $assertion;
    }

    /**
     * Check given input type & throw given message/throwable optionally when assertion fails.
     *
     * @param  mixed                                         $input
     * @param  string                                        $type
     * @param  array<string,Throwable>|string|Throwable|null $message
     * @return bool
     * @causes Throwable|AssertException
     */
    public static function checkType(mixed $input, string $type, array|string|Throwable $message = null): bool
    {
        $assertion = is_type_of($input, ...(
            split('\|', $type) // Eg: int or int|float.
        ));

        return self::check($assertion, $message);
    }

    /**
     * Check given input class & throw given message/throwable optionally when assertion fails.
     *
     * @param  mixed                                         $input
     * @param  string                                        $class
     * @param  array<string,Throwable>|string|Throwable|null $message
     * @return bool
     * @causes Throwable|AssertException
     */
    public static function checkClass(mixed $input, string $class, array|string|Throwable $message = null): bool
    {
        $assertion = is_type_of($input, 'object', 'string') && is_class_of($input, $class);

        return self::check($assertion, $message);
    }

    /**
     * Check given input instance & throw given message/throwable optionally when assertion fails.
     *
     * @param  mixed                                         $input
     * @param  string                                        $class
     * @param  array<string,Throwable>|string|Throwable|null $message
     * @return bool
     * @causes Throwable|AssertException
     */
    public static function checkInstance(mixed $input, string|object $class, array|string|Throwable $message = null): bool
    {
        $assertion = is_type_of($input, 'object') && ($input instanceof $class);

        return self::check($assertion, $message);
    }

    /**
     * Check given input pattern & throw given message/throwable optionally when assertion fails.
     *
     * @param  string                                        $input
     * @param  string                                        $pattern
     * @param  array<string,Throwable>|string|Throwable|null $message
     * @return bool
     * @causes Throwable|AssertException
     */
    public static function checkRegExp(string $input, string $pattern, array|string|Throwable $message = null): bool
    {
        $assertion = preg_test($pattern, $input);

        return self::check($assertion, $message);
    }

    /** @aliasof check() */
    public static function ok(...$args) { return self::check(...$args); }

    /** @aliasof check() */
    public static function bool(...$args) { return self::check(...$args); }

    /** @aliasof checkType() */
    public static function type(...$args) { return self::checkType(...$args); }

    /** @aliasof checkClass() */
    public static function class(...$args) { return self::checkClass(...$args); }

    /** @aliasof checkInstance() */
    public static function instance(...$args) { return self::checkInstance(...$args); }

    /** @aliasof checkRegExp() */
    public static function regExp(...$args) { return self::checkRegExp(...$args); }
}

/**
 * Assert Exception.
 *
 * @package froq\util
 * @object  AssertException
 * @author  Kerem Güneş
 * @since   6.0
 */
class AssertException extends Exception {}

