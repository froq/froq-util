<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\misc\Dumper;

/**
 * Dumpers.
 */
function dd(...$args) {
    Dumper::dump(...$args);
}
function de(...$args) {
    return Dumper::echo(...$args);
}
function dp(...$args) {
    return Dumper::echoPre(...$args);
}
