<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

// Load top function files.
require 'sugars-function/_array.php';
require 'sugars-function/_string.php';

// Load other function files.
require 'sugars-function/cast.php';
require 'sugars-function/conv.php';
require 'sugars-function/dump.php';
require 'sugars-function/file.php';
require 'sugars-function/http.php';
require 'sugars-function/json.php';
require 'sugars-function/preg.php';

/**
 * State initializer.
 *
 * @param  mixed ...$states
 * @return State
 */
function state(mixed ...$states): State
{
    return new State(...$states);
}

/**
 * Reflection initializer.
 *
 * @param  object|string $target
 * @param  string|null   $type
 * @return Reflector|null
 */
function reflect(object|string $target, string $type = null): Reflector|null
{
    if (is_object($target)) {
        return new XReflectionObject($target);
    }

    switch ($type) {
        case 'class':
            return new XReflectionClass($target);
        case 'function':
            return new XReflectionFunction($target);

        case 'trait':
            return new ReflectionTrait($target);
        case 'interface':
            return new ReflectionInterface($target);
        case 'namespace':
            return new ReflectionNamespace($target);

        case 'callable':
            return new ReflectionCallable($target);

        default:
            // Eg: Foo@bar or Foo::bar
            $target = replace($target, '@', '::');

            switch ($type) {
                case 'constant':
                case 'class-constant':
                    return new XReflectionClassConstant($target);
                case 'property':
                case 'class-property':
                    return new XReflectionProperty($target);
                case 'method':
                case 'class-method':
                    return new XReflectionMethod($target);
                case 'class-namespace':
                    $target = get_class_namespace($target);
                    return new ReflectionNamespace($target);

                default:
                    throw new ArgumentError('Invalid type: %q', $type);
            }
    }

    return null;
}
