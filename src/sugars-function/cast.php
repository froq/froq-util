<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\Numbers;

/**
 * Cast functions for scalars.
 */
function int    (mixed $var): int       { return intval($var);    }
function float  (mixed $var): float     { return floatval($var);  }
function number (mixed $var): int|float { return numberval($var); }
function string (mixed $var): string    { return strval($var);    }
function bool   (mixed $var): bool      { return boolval($var);   }

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

/**
 * Number caster for both numeric (int/float) strings.
 *
 * @param  mixed $var
 * @return int|float
 */
function numberval(mixed $var): int|float
{
    if (is_numeric($var)) {
        $ret = Numbers::convert($var);

        if (!is_nan($ret) && !is_infinite($ret)) {
            return $ret;
        }
    }

    return 0;
}
