<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Cast functions for scalars.
 */
function int    (mixed $var): int    { return (int)    $var; }
function float  (mixed $var): float  { return (float)  $var; }
function string (mixed $var): string { return (string) $var; }
function bool   (mixed $var): bool   { return (bool)   $var; }

// function array  (mixed $var) { return (array)  $var; } :(
// function object (mixed $var) { return (object) $var; }

/**
 * Object caster (usefull for named arguments).
 *
 * @param  mixed ...$vars
 * @return object
 */
function object(mixed ...$vars): object
{
    // For object(array) or object(1,2,..).
    if (is_list($vars) && count($vars) === 1) {
        $vars = current($vars);

        // Drop [scalar] => ...
        if (is_scalar($vars)) {
            $vars = [$vars];
        }
    }

    return (object) $vars;
}
