<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * An assert(ion) class with some utility methods.
 *
 * @package global
 * @class   Assert
 * @author  Kerem Güneş
 * @since   6.0
 * @static
 */
class Assert
{
    /**
     * Assert & throw given message/throwable optionally if assertion fails.
     *
     * @param  bool                  $assertion
     * @param  string|Throwable|null $message
     * @return bool
     * @throws Error|Exception|AssertException
     */
    public static function assert(bool $assertion, string|Throwable $message = null): bool
    {
        if (!$assertion && $message !== null) {
            if (is_string($message)) {
                $message = new AssertException($message);
            }

            throw $message;
        }

        return $assertion;
    }

    /**
     * Assert given input whether is true & throw given message/throwable optionally if assertion fails.
     *
     * @param  mixed                 $input
     * @param  string|Throwable|null $message
     * @return bool
     * @causes Error|Exception|AssertException
     */
    public static function true(mixed $input, string|Throwable $message = null): bool
    {
        return self::assert($input === true, $message);
    }

    /**
     * Assert given input whether is false & throw given message/throwable optionally if assertion fails.
     *
     * @param  mixed                 $input
     * @param  string|Throwable|null $message
     * @return bool
     * @causes Error|Exception|AssertException
     */
    public static function false(mixed $input, string|Throwable $message = null): bool
    {
        return self::assert($input === false, $message);
    }

    /**
     * Assert given input equality & throw given message/throwable optionally if assertion fails.
     *
     * @param  mixed                 $input
     * @param  mixed                 $inputs
     * @param  string|Throwable|null $message
     * @return bool
     * @causes Error|Exception|AssertException
     */
    public static function equals(mixed $input, mixed $inputs, string|Throwable $message = null): bool
    {
        $assertion = equals($input, ...(is_array($inputs) ? $inputs : [$inputs]));

        return self::assert($assertion, $message);
    }

    /**
     * Assert given input type & throw given message/throwable optionally if assertion fails.
     *
     * @param  mixed                 $input
     * @param  string                $type
     * @param  string|Throwable|null $message
     * @return bool
     * @causes Error|Exception|AssertException
     */
    public static function type(mixed $input, string $type, string|Throwable $message = null): bool
    {
        // Eg: "int" or "int|float".
        $assertion = is_type_of($input, $type);

        return self::assert($assertion, $message);
    }

    /**
     * Assert given input class & throw given message/throwable optionally if assertion fails.
     *
     * @param  mixed                 $input
     * @param  string                $class
     * @param  string|Throwable|null $message
     * @return bool
     * @causes Error|Exception|AssertException
     */
    public static function class(mixed $input, string $class, string|Throwable $message = null): bool
    {
        $assertion = is_type_of($input, 'object|string') && is_class_of($input, $class);

        return self::assert($assertion, $message);
    }

    /**
     * Assert given input instance & throw given message/throwable optionally if assertion fails.
     *
     * @param  mixed                 $input
     * @param  string                $class
     * @param  string|Throwable|null $message
     * @return bool
     * @causes Error|Exception|AssertException
     */
    public static function instance(mixed $input, string|object $class, string|Throwable $message = null): bool
    {
        $assertion = is_type_of($input, 'object') && ($input instanceof $class);

        return self::assert($assertion, $message);
    }

    /**
     * Assert given input pattern & throw given message/throwable optionally if assertion fails.
     *
     * @param  string                $input
     * @param  string                $pattern
     * @param  string|Throwable|null $message
     * @return bool
     * @causes Error|Exception|AssertException
     */
    public static function regexp(string $input, string $pattern, string|Throwable $message = null): bool
    {
        $assertion = preg_test($pattern, $input);

        return self::assert($assertion, $message);
    }
}

/**
 * @package global
 * @class   AssertException
 * @author  Kerem Güneş
 * @since   6.0
 */
class AssertException extends froq\common\Exception {}

