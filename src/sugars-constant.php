<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Nil: For null/void/undefined refs.
 */
final class Nil {}

/**
 * Ref: For byref semantics.
 */
final class Ref {
  public function __construct(
    public mixed $data = null
  ) {}
}

function Nil(): Nil {
  return new Nil();
}
function Ref(mixed $data = null): Ref {
  return new Ref($data);
}

/**
 * Nil, alt of null (for defaults etc).
 */
const nil = new Nil();

/**
 * Multi-byte encoding.
 */
const ENCODING = 'UTF-8';

/**
 * Number precision.
 */
const PRECISION = 14;

/**
 * Int max for 32/64 bits.
 */
const INT_MAX_32 = 2147483647,
      INT_MAX_64 = 9223372036854775807;

/**
 * Cases (0/1 already defined as CASSE_LOWER/CASE_UPPER).
 */
const CASE_TITLE = 2, CASE_DASH  = 3,
      CASE_SNAKE = 4, CASE_CAMEL = 5;

/**
 * Path info.
 */
const PATHINFO_TYPE = 0;

/**
 * Namespace separator.
 */
const NAMESPACE_SEPARATOR = '\\';

/**
 * Trim characters.
 */
const TRIM_CHARACTERS = " \n\r\t\v\0";

/**
 * Base-62 alphabet.
 */
const BASE62_ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
