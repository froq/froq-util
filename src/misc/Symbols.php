<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

/**
 * A static class defined / declared symbols.
 *
 * @package froq\util
 * @class   froq\util\Symbols
 * @author  Kerem Güneş
 * @since   7.0
 * @static
 */
class Symbols
{
    /**
     * Get declared classes.
     *
     * @param  string|null $prefix
     * @param  bool|int    $sort
     * @return array
     */
    public static function getDeclaredClasses(string $prefix = null, bool|int $sort = false): array
    {
        $ret = ($prefix === null) ? get_declared_classes() :
            array_filter(get_declared_classes(), fn($name) => strpfx($name, $prefix));

        if ($sort) {
            $ret = sorted($ret, (int) $sort);
        }

        return $ret;
    }

    /**
     * Get declared interfaces.
     *
     * @param  string|null $prefix
     * @param  bool|int    $sort
     * @return array
     */
    public static function getDeclaredInterfaces(string $prefix = null, bool|int $sort = false): array
    {
        $ret = ($prefix === null) ? get_declared_interfaces() :
            array_filter(get_declared_interfaces(), fn($name) => strpfx($name, $prefix));

        if ($sort) {
            $ret = sorted($ret, (int) $sort);
        }

        return $ret;
    }

    /**
     * Get declared traits.
     *
     * @param  string|null $prefix
     * @param  bool|int    $sort
     * @return array
     */
    public static function getDeclaredTraits(string $prefix = null, bool|int $sort = false): array
    {
        $ret = ($prefix === null) ? get_declared_traits() :
            array_filter(get_declared_traits(), fn($name) => strpfx($name, $prefix));

        if ($sort) {
            $ret = sorted($ret, (int) $sort);
        }

        return $ret;
    }

    /**
     * Get defined constants.
     *
     * @param  string|null $prefix
     * @param  string|null $category
     * @param  bool|int    $sort
     * @return array
     */
    public static function getDefinedConstants(string $prefix = null, string $category = null, bool|int $sort = false): array
    {
        $ret = get_defined_constants(true);

        if ($category === null) {
            $ret = array_flat($ret, true);
        } else {
            $ret = $ret[$category] ?? [];
        }

        if ($prefix !== null) {
            $ret = array_filter($ret, fn($name) => strpfx($name, $prefix), 2);
        }

        if ($sort) {
            $ret = sorted($ret, (int) $sort, key: true);
        }

        return $ret;
    }

    /**
     * Get defined functions.
     *
     * @param  string|null $prefix
     * @param  string|null $category
     * @param  bool|int    $sort
     * @param  bool        $excludeDisabled
     * @return array
     */
    public static function getDefinedFunctions(string $prefix = null, string $category = null, bool|int $sort = false,
        bool $excludeDisabled = true): array
    {
        $ret = get_defined_functions($excludeDisabled);

        if ($category === null) {
            $ret = array_merge($ret['internal'], $ret['user']);
        } else {
            $ret = $ret[$category] ?? [];
        }

        if ($prefix !== null) {
            $ret = array_filter($ret, fn($name) => strpfx($name, $prefix));
        }

        if ($sort) {
            $ret = sorted($ret, (int) $sort);
        }

        return $ret;
    }
}
