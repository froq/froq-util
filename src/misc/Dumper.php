<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

use froq\util\Objects;

/**
 * Dumper.
 *
 * Represents a dumper class that provides a dumping interface via `dump()` method or printing
 * options via `echo()` or `echoPre()` methods.
 *
 * @package froq\util\misc
 * @object  froq\util\misc\Dumper
 * @author  Kerem Güneş
 * @since   5.7
 */
final class Dumper
{
    /**
     * Dump given input.
     * Note: Be careful while dumping recursions with arrays.
     *
     * @param  mixed  $input
     * @param  string $tab  Indent.
     * @param  int    $tabs Indent count. @internal
     * @return string
     */
    public static function dump(mixed $input, string $tab = '  ', int $tabs = 0): string
    {
        $type = gettype($input);

        switch ($type) {
            case 'integer':
                return 'int: '. $input;

            case 'double':
                // Append ".0" for single vals (eg: 1.0).
                if (strlen((string) $input) == 1) {
                    $input .= '.0';
                }
                return 'float: '. $input;

            case 'string':
                return 'string('. strlen($input) .'): "'. $input .'"';

            case 'boolean':
                return 'bool: '. ($input ? 'true' : 'false');

            case 'array':
            case 'object':
                $tabs += 1;

                if ($type == 'array') {
                    $arrayType = $input ? (is_list($input) ? 'list' : 'map') : '';

                    $output = sprintf('array(%d) <%s> {', count($input), $arrayType) . "\n";

                    // // For space gaps.
                    // $maxKeyLen = max(array_map(
                    //     fn($k) => strlen((string) $k),
                    //     array_keys($input)
                    // ));

                    foreach ($input as $key => $value) {
                        $recursion = null;
                        if (is_array($value)) {
                            $recursion = self::checkRecursion($value);
                        }

                        $space = '';
                        // $spaceGap = $maxKeyLen - strlen((string) $key);
                        // if ($spaceGap > 0) {
                        //     $space = str_repeat(' ', $spaceGap);
                        // }

                        $output .= str_repeat($tab, $tabs);
                        $output .= $key . $space .' => '. (
                            $recursion ?: self::dump($value, $tab, $tabs)
                        );
                        $output .= "\n";
                    }
                } else {
                    $properties = Objects::getProperties($input);
                    [$objectType, $objectId] = split('#', Objects::getId($input));

                    $output = sprintf('object(%d) <%s>#%s {', count($properties), $objectType, $objectId) . "\n";

                    // Handle spacial cases for debug info.
                    if (method_exists($input, '__debugInfo')) {
                        $properties = $input->__debugInfo();
                        if (is_array($properties)) {
                            $properties = self::dump($properties, $tab, $tabs - 1);

                            // Drop "array(1) <..> {..}" parts.
                            $properties = slice(split("\n", $properties), 1, -1);

                            // Append back properties.
                            $output .= join("\n", $properties) . "\n";

                            $output .= str_repeat($tab, $tabs - 1);
                            $output .= '}';

                            return $output;
                        }
                    }

                    foreach ($properties as $property) {
                        $recursion = self::checkRecursion($input, $property['name']);
                        if ($recursion) {
                            return $recursion;
                        }

                        $output .= str_repeat($tab, $tabs);

                        // Drop public & join.
                        $property['modifiers'] = join('|', array_delete(
                            $property['modifiers'], 'public'
                        ));

                        $propertyInfo = array_filter([
                            $property['modifiers'],
                            $property['type'],
                        ]);

                        $output .= sprintf('%s%s => ', $property['name'], (
                            $propertyInfo ? ' ['. join(':', $propertyInfo) .']' : ''
                        ));

                        if (is_false($property['initialized'])) {
                            // Cannot get the (default) value when unset() applied on the property.
                            $ref = new \ReflectionProperty($property['class'], $property['name']);
                            if ($ref->hasDefaultValue()) {
                                $output .= self::dump($ref->getDefaultValue(), $tab, $tabs);
                            } else {
                                $output .= '*UNINITIALIZED';
                            }
                        } elseif (is_null($property['value'])) {
                            $output .= '*NULL';
                        } else {
                            $output .= self::dump($property['value'], $tab, $tabs);
                        }

                        $output .= "\n";
                    }
                }

                $tabs -= 1;

                $output .= str_repeat($tab, $tabs);
                $output .= '}';

                return $output;

            case 'resource':
            case 'resource (closed)':
                return get_debug_type($input) .' #'. get_resource_id($input);
        }

        return $type;
    }

    /**
     * Echo given input's dump.
     *
     * @param  mixed  $input
     * @param  bool   $exit
     * @param  string $tab
     * @return void
     */
    public static function echo(mixed $input, bool $exit = false, string $tab = '  '): void
    {
        echo "", self::dump($input, $tab), "\n";
        $exit && exit(0);
    }

    /**
     * Echo given input's dump in `pre` tag.
     *
     * @param  mixed  $input
     * @param  bool   $exit
     * @param  string $tab
     * @return void
     */
    public static function echoPre(mixed $input, bool $exit = false, string $tab = '  '): void
    {
        echo "<pre>", self::dump($input, $tab), "</pre>\n";
        $exit && exit(0);
    }

    /**
     * Check for recursions.
     *
     * @param  array|object &$input
     * @param  string|null   $propertyName
     * @return string|null
     */
    private static function checkRecursion(array|object &$input, string $propertyName = null): string|null
    {
        if (is_array($input)) {
            static $inputKey = '**RECURSION';

            if (isset($input[$inputKey])) {
                return '*RECURSION(array)';
            }

            $input[$inputKey] = 1; // Tick.

            foreach ($input as $key => &$value) {
                if ($key !== $inputKey && is_array($value) && self::checkRecursion($value)) {
                    return '*RECURSION(array)';
                }
            }

            // Drop recursion tick.
            unset($input[$inputKey]);
        } else {
            static $recursions = [];

            $id  = Objects::getId($input);
            $key = $id . $propertyName;
            if (isset($recursions[$key]) && $recursions[$key] === $input) {
                return '*RECURSION('. $id .')';
            }

            $recursions[$key] = $input; // Tick.
        }

        return null;
    }
}
