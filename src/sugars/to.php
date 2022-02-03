<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Make an array with given input.
 *
 * @param  array|object $in
 * @param  bool         $deep
 * @return array
 */
function to_array(array|object $in, bool $deep = true): array
{
    if ($in && is_object($in)) {
        $out = (array) (
            is_iterator($in) ? iterator_to_array($in) : (
                is_callable([$in, 'toArray']) ? $in->toArray() : (
                    get_object_vars($in)
                )
            )
        );
    } else {
        $out = (array) $in;
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
 * @param  array|object $in
 * @param  bool         $deep
 * @return object
 */
function to_object(array|object $in, bool $deep = true): object
{
    if ($in && is_object($in)) {
        $out = (object) (
            is_iterator($in) ? iterator_to_array($in) : (
                is_callable([$in, 'toArray']) ? $in->toArray() : (
                    get_object_vars($in)
                )
            )
        );
    } else {
        $out = (object) $in;
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
        if ($argc != null) {
            $args = array_slice($args, 0, $argc);
        }
        return $func(...$args);
    };
}
