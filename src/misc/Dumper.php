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
     * @param  int    $indent
     * @param  string $indentString
     * @return string
     */
    public static function dump(mixed $input, int $indent = 0, string $indentString = "\t"): string
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
                $indent += 1;

                if ($type == 'array') {
                    $output = sprintf('array(%d) {', count($input)) . "\n";

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

                        $output .= str_repeat($indentString, $indent);
                        $output .= $key . $space .' => '. (
                            $recursion ?: self::dump($value, $indent, $indentString)
                        );
                        $output .= "\n";
                    }
                } else {
                    $output = sprintf('object(%s)#%d {', Objects::getName($input), spl_object_id($input)) . "\n";

                    foreach (Objects::getProperties($input) as $property) {
                        $recursion = self::checkRecursion($input, $property['name']);
                        if ($recursion) {
                            return $recursion;
                        }

                        $output .= str_repeat($indentString, $indent);

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
                                $output .= self::dump($ref->getDefaultValue(), $indent, $indentString);
                            } else {
                                $output .= '*UNINITIALIZED';
                            }
                        } elseif (is_null($property['value'])) {
                            $output .= '*NULL';
                        } else {
                            $output .= self::dump($property['value'], $indent, $indentString);
                        }

                        $output .= "\n";
                    }
                }

                $indent -= 1;

                $output .= str_repeat($indentString, $indent);
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
     * @param  string $indentString
     * @return void
     */
    public static function echo(mixed $input, bool $exit = false, string $indentString = "\t"): void
    {
        echo "", self::dump($input, 0, $indentString), "\n";
        $exit && exit(0);
    }

    /**
     * Echo given input's dump in `pre` tag.
     *
     * @param  mixed  $input
     * @param  bool   $exit
     * @param  string $indentString
     * @return void
     */
    public static function echoPre(mixed $input, bool $exit = false, string $indentString = "\t"): void
    {
        echo "<pre>", self::dump($input, 0, $indentString), "</pre>\n";
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

            $classId   = Objects::getId($input);
            $classKey  = $classId . $propertyName;
            $classHash = Objects::getSerializedHash($input);
            if (isset($recursions[$classKey]) && $recursions[$classKey] === $classHash) {
                return '*RECURSION('. $classId .')';
            }

            $recursions[$classKey] = $classHash; // Tick.
        }

        return null;
    }
}
