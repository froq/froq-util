<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Make an array with given input.
 *
 * @param  array|object $input
 * @param  bool         $deep
 * @return array
 */
function to_array(array|object $input, bool $deep = true): array
{
    if ($input && is_object($input)) {
        $out = (array) (
            is_iterator($input) ? iterator_to_array($input) : (
                is_callable([$input, 'toArray']) ? $input->toArray() : (
                    get_object_vars($input)
                )
            )
        );
    } else {
        $out = (array) $input;
    }

    if ($deep) {
        foreach ($out as $key => $value) {
            $out[$key] = is_iterable($value) || is_object($value)
                ? to_array($value, true) : $value;
        }
    }

    return $out;
}

/**
 * Make an object with given input.
 *
 * @param  array|object $input
 * @param  bool         $deep
 * @return object
 */
function to_object(array|object $input, bool $deep = true): object
{
    if ($input && is_object($input)) {
        $out = (object) (
            is_iterator($input) ? iterator_to_array($input) : (
                is_callable([$input, 'toArray']) ? $input->toArray() : (
                    get_object_vars($input)
                )
            )
        );
    } else {
        $out = (object) $input;
    }

    if ($deep) {
        foreach ($out as $key => $value) {
            $out->{$key} = is_iterable($value) || is_object($value)
                ? to_object($value, true) : $value;
        }
    }

    return $out;
}

/**
 * Make a closure with given input.
 *
 * @param  string   $func
 * @param  int|null $argc
 * @return Closure
 */
function to_closure(string $func, int $argc = null): Closure
{
    return function (...$args) use ($func, $argc) {
        if ($argc) {
            $args = array_slice($args, 0, $argc);
        }
        return $func(...$args);
    };
}
