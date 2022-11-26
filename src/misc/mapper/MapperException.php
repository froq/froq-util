<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\mapper;

/**
 * @package froq\util\mapper
 * @class   froq\util\mapper\MapperException
 * @author  Kerem Güneş
 * @since   7.0
 */
class MapperException extends \froq\common\Exception
{
    /**
     * Create for null object.
     *
     * @return static
     */
    public static function forNullObject(): static
    {
        return new static(
            'No object given yet, call setObject() or pass $object argument to %s()',
            last(get_trace())['function']
        );
    }

    /**
     * Create for absent typed property class.
     *
     * @param  string $class
     * @param  object $object
     * @param  string $property
     * @return static
     */
    public static function forAbsentTypedPropertyClass(string $class, object $object, string $property): static
    {
        return new static(
            'Class %q not found for typed property %S::$%s',
            [$class, $object::class, $property],
            cause: new \UndefinedClassError($class)
        );
    }

    /**
     * Create for absent annotated property class.
     *
     * @param  string $class
     * @param  object $object
     * @param  string $property
     * @return static
     */
    public static function forAbsentAnnotatedPropertyClass(string $class, object $object, string $property): static
    {
        return new static(
            'Class %q not found for annotated property %S::$%s',
            [$class, $object::class, $property],
            cause: new \UndefinedClassError($class)
        );
    }

    /**
     * Create for undefined property.
     *
     * @param  object $object
     * @param  string $property
     * @return static
     */
    public static function forUndefinedProperty(object $object, string $property): static
    {
        $error = new \UndefinedPropertyError($object, $property);
        return new static($error->getMessage(), cause: $error);
    }

    /**
     * Create for invalid annotation / attribute.
     *
     * @param  string $meta
     * @param  string $class
     * @param  string $property
     * @return static
     */
    public static function forInvalidMeta(array $meta, string $class, string $property): static
    {
        return new static(
            'Invalid %s %q on property %S::$%s',
            [$meta['type'], grep('~@var +([^\s]+)~', $meta[0]), $class, $property]
        );
    }
}
