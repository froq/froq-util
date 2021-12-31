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
            case 'NULL';
                return '*NULL';
            case 'integer':
                return 'int: '. $input;

            case 'double':
                // Check decimals for appending 0 to 1.0 (as expected).
                $decimals = 1;
                if ($remainds = strstr((string) $input, '.')) {
                    $decimals = strlen($remainds) - 1;
                }

                return 'float: '. format_number($input, $decimals);

            case 'string':
                // Add multibyte length (as expected).
                $length = mb_strlen($input);

                return 'string('. $length .') "'. $input .'"';

            case 'boolean':
                return 'bool: '. format_bool($input);

            case 'array':
            case 'object':
                if ($input == null()) {
                    return '**NULL';
                } elseif ($input == void()) {
                    return '**VOID';
                }

                $tabs += 1;

                if ($type == 'array') {
                    $type = $input ? (is_list($input) ? 'list' : 'map') : '?';
                    $count = count($input);
                    $output = format('array(%d) <%s> {', $count, $type);
                    if ($count) {
                        $output .= "\n";
                    }

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

                        // Fix null/none keys.
                        if ($key === '') {
                            $key = "''";
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
                    static $prep; $prep ??= function ($dump) {
                        $dump = slice(split("\n", $dump), 1, -1); // Drop "array(1) <..> {" and "}" parts.
                        $dump = join("\n", $dump); // Join back elements/properties.
                        return $dump;
                    };

                    [$objectType, $objectId] = split('#', Objects::getId($input));

                    // Handle special objects.
                    if ($input instanceof \SplFixedArray) {
                        $output = format('object(%d) <%s>#%s {', $input->count(), $objectType, $objectId);

                        if ($input->count()) {
                            $dump = self::dump($input->toArray(), $tab, $tabs - 1);
                            $output .= "\n". $prep($dump) ."\n";
                            $output .= str_repeat($tab, $tabs - 1);
                        }

                        $output .= '}';

                        return $output;
                    }

                    // Handle spacial cases for debug info.
                    if (method_exists($input, '__debugInfo')) {
                        $info = $input->__debugInfo();

                        // In case: "[\0ArrayObject\0storage] => ..".
                        $info = array_map_keys($info, function ($key) {
                            if (strsrc((string) $key, "\0")) {
                                $key = array_last(split("\0", $key));
                                $key = $key .' [private]';
                            }
                            return $key;
                        });

                        $count = count($info);
                        $output = format('object(%d) <%s>#%s {', $count, $objectType, $objectId);

                        if ($info) {
                            $dump = self::dump($info, $tab, $tabs - 1);
                            $output .= "\n". $prep($dump) ."\n";
                            $output .= str_repeat($tab, $tabs - 1);
                        }

                        $output .= '}';

                        return $output;
                    }

                    $properties = Objects::getProperties($input);
                    $count = count($properties);
                    $output = format('object(%d) <%s>#%s {', $count, $objectType, $objectId);
                    if ($count) {
                        $output .= "\n";
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

                        $output .= format('%s%s => ', $property['name'], (
                            $propertyInfo ? ' ['. join(':', $propertyInfo) .']' : ''
                        ));

                        // To get value, this reflection must come from Objects call.
                        $ref = $property['reflection'];
                        if (is_false($property['initialized'])) {
                            if ($ref->hasDefaultValue()) {
                                $output .= self::dump($ref->getDefaultValue(), $tab, $tabs);
                            } else {
                                $output .= '*UNINITIALIZED';
                            }
                        } else {
                            $value = $ref->getValue();
                            if (is_null($value)) {
                                $output .= '*NULL';
                            } elseif (
                                is_object($value)
                                && is_class_of($value, 'DOMNode') // Danger & safe.
                                && !is_class_of($value, 'DOMImplementation', /* 'DOMProcessingInstruction', ... */
                            )) {
                                // Problematic...
                                $output .= format('<%s>#%s { ... }', ...split('#', get_object_id($value)));
                            } else {
                                $output .= self::dump($value, $tab, $tabs);
                            }
                        }

                        $output .= "\n";
                    }
                }

                $tabs -= 1;

                if ($count) {
                    $output .= str_repeat($tab, $tabs);
                }
                $output .= '}';

                return $output;

            case 'resource':
            case 'resource (closed)':
                return get_type($input) .' #'. get_resource_id($input);
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

            // Untick.
            unset($input[$inputKey]);
        } else {
            static $recursions = [];
            static $lastTracePack, $lastTracePath;

            // Reset for each different call.
            $trace = debug_backtrace();
            [$first, $last] = [current($trace), end($trace)];
            [$tracePack, $tracePath] = [$first, ($last['file'] . $last['line'])];
            unset($trace);

            // Check ticks & clear recursions.
            if (($lastTracePack || $lastTracePath) &&
                ($lastTracePack != $tracePack || $lastTracePath != $tracePath)) {
                $recursions = [];
            }

            $id  = Objects::getId($input);
            $key = $id . $propertyName;
            if (isset($recursions[$key]) && $recursions[$key] === $input) {
                return '*RECURSION('. $id .')';
            }

            // Ticks.
            $recursions[$key] = $input;
            $lastTracePack = $tracePack;
            $lastTracePath = $tracePath;

            // @nope
            // json_encode($trace[1]['args']);
            // if (($error = json_error_message()) && strsrc($error, 'recursion', icase: true)) {
            //     return '*RECURSION('. $id .')';
            // }
        }

        return null;
    }
}
