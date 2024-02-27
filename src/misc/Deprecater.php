<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

/**
 * Deprecation trigger class.
 *
 * @package froq\util
 * @class   froq\util\Deprecater
 * @author  Kerem Güneş
 * @since   7.0
 * @static
 */
class Deprecater
{
    /**
     * Trigger a deprecation message for given class.
     *
     * @param  string      $class
     * @param  string|null $version
     * @param  string|null $use
     * @return void
     */
    public static function class(string $class, string $version = null, string $use = null): void
    {
        $message = sprintf('Class %s is deprecated', $class);

        if ($version) {
            $message .= ' as of ' . $version;
        }
        if ($use) {
            $message .= ', use ' . $use . ' instead';
        }

        self::trigger($message);
    }

    /**
     * Trigger a deprecation message for given method.
     *
     * @param  string      $method
     * @param  string|null $version
     * @param  string|null $use
     * @return void
     */
    public static function method(string|array $method, string $version = null, string|array $use = null): void
    {
        if (is_array($method)) {
            [$class, $method] = [get_class_name($method[0]), $method[1]];
            $method = join('::', [$class, $method]);
        }
        if ($use && is_array($use)) {
            [$class, $use] = [get_class_name($use[0]), $use[1]];
            $use = join('::', [$class, $use]);
        }

        $message = sprintf('Method %s() is deprecated', $method);

        if ($version) {
            $message .= ' as of ' . $version;
        }
        if ($use) {
            $message .= ', use ' . $use . '() instead';
        }

        self::trigger($message);
    }

    /**
     * Trigger a deprecation message for given function.
     *
     * @param  string      $function
     * @param  string|null $version
     * @param  string|null $use
     * @return void
     */
    public static function function(string $function, string $version = null, string $use = null): void
    {
        $message = sprintf('Function %s() is deprecated', $function);

        if ($version) {
            $message .= ' as of ' . $version;
        }
        if ($use) {
            $message .= ', use ' . $use . '() instead';
        }

        self::trigger($message);
    }

    /**
     * Trigger given message appending a call path.
     */
    private static function trigger(string $message): void
    {
        $trace = @get_trace(slice: 2)[0];
        if (isset($trace['file'], $trace['line'])) {
            $message .= "\nCalled at: {$trace['file']}:{$trace['line']}\n";
        }

        trigger_error($message, E_USER_DEPRECATED);
    }
}
