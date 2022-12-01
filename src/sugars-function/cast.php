<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Cast functions.
 */
function int    (mixed $var) { return (int)    $var; }
function float  (mixed $var) { return (float)  $var; }
function string (mixed $var) { return (string) $var; }
function bool   (mixed $var) { return (bool)   $var; }
// function array  (mixed $var) { return (array)  $var; } :((
function object (mixed $var) { return (object) $var; }
