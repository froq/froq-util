<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

use Throwable, Error, ReflectionClass, XReflectionType;

/**
 * Initializer class for safe object initializations.
 *
 * @package froq\util
 * @class   froq\util\Initializer
 * @author  Kerem Güneş
 * @since   7.0
 * @static
 */
class Initializer
{
    /**
     * Init a class with/without given class (constructor) arguments.
     *
     * @param  string   $class
     * @param  mixed ...$classArgs
     * @return object
     * @throws Error
     */
    public static function init(string $class, mixed ...$classArgs): object
    {
        try {
            $ref = new ReflectionClass($class);
        } catch (Throwable $e) {
            throw new Error($e->getMessage(), $e->getCode());
        }

        // Check constructor parameters.
        if ($cref = $ref->getConstructor()) {
            $params = [];

            foreach ($cref->getParameters() as $pref) {
                // If no related param given, skip.
                if (!is_array_key($classArgs, $pref->name)) {
                    $ptype = XReflectionType::from($pref->getType());

                    // If no required param given, init class param.
                    if (!$pref->isOptional() && $ptype?->isClass()) {
                        $pclass = $ptype->getPureName();

                        if (!class_exists($pclass)) {
                            throw new Error(format(
                                'Class %q not found, see %s::__construct(..., %s $%s)',
                                $pclass, $cref->class, $pclass, $pref->name,
                            ));
                        }

                        $params[$pref->name] = new $pclass();
                    }

                    continue;
                }

                $value = $classArgs[$pref->name];
                $vtype = self::getValueType($value);
                $ptype = XReflectionType::from($pref->getType());

                // Null/type check.
                if ($value === null && $ptype?->allowsNull() === false) {
                    continue;
                }
                if ($ptype?->isMixed() === false && !$ptype->contains($vtype)) {
                    continue;
                }

                $params[$pref->name] = $classArgs[$pref->name];
            }

            $paramsCount = count($params);

            if ($paramsCount && $cref->isPublic() && (
                $paramsCount === $cref->getNumberOfParameters() ||
                $paramsCount === $cref->getNumberOfRequiredParameters()
            )) {
                $object = $ref->newInstance(...$params);
            } else {
                $object = $ref->newInstanceWithoutConstructor();
                $params = []; $paramsCount = 0;
            }
        } else {
            $object = $ref->newInstanceWithoutConstructor();
            $params = []; $paramsCount = 0;
        }

        $classArgsCount = count($classArgs);

        // Check if all args were set to props when given.
        if ($classArgs && $classArgsCount > $paramsCount) {
            $diffArgs = $classArgsCount > $paramsCount
                      ? array_diff_assoc($classArgs, $params)
                      : array_diff_assoc($params, $classArgs);

            foreach ($diffArgs as $name => $value) {
                if ($ref->hasProperty($name)) {
                    $pref  = $ref->getProperty($name);
                    $vtype = self::getValueType($value);
                    $ptype = XReflectionType::from($pref->getType());

                    // Null/type check.
                    if ($value === null && $ptype?->allowsNull() === false) {
                        continue;
                    }
                    if ($ptype?->isMixed() === false && !$ptype->contains($vtype)) {
                        continue;
                    }

                    $pref->setValue($object, $value);
                }
            }
        }

        return $object;
    }

    /**
     * Get type(s) of given value.
     */
    private static function getValueType(mixed $value): array
    {
        $ret = [get_type($value)];

        // Can't take 'object' type from get_type().
        is_object($value) && $ret[] = 'object';

        return $ret;
    }
}
