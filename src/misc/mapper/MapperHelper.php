<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\mapper;

/**
 * @package froq\util\mapper
 * @class   froq\util\mapper\MapperHelper
 * @author  Kerem Güneş
 * @since   7.0
 */
class MapperHelper
{
    /**
     * Get list class from given attributes if available.
     *
     * Examples:
     * ```
     * #[meta(list:'Foo[]')]
     * #[meta(list:'array<Foo>')]
     * #[meta(list:'iterable<Foo>')]
     * #[meta(list:'FooList<Foo>')]
     * ```
     *
     * @param  array<ReflectionAttribute> $attributes
     * @return string|null
     */
    public static function getListClass(array $attributes): string|null
    {
        foreach ($attributes as $attribute) {
            $name = get_class_name(
                $attribute->getName(), short: true
            );

            if (strtolower($name) === 'meta') {
                return $attribute->getArguments()['list'] ?? null;
            }
        }

        return null;
    }
}
