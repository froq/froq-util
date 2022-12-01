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
    public mixed $data = nil
  ) {}
}

function Nil(mixed &$ref = nil): Nil {
  return $ref = new Nil();
}
function Ref(mixed $data = nil): Ref {
  return new Ref($data);
}

/**
 * Nil/nils (null/null string).
 * @since 4.0
 */
const nil = null, nils = '';

/**
 * Multi-byte encoding.
 * @since 6.0
 */
const ENCODING = 'UTF-8';

/**
 * Number precision.
 * @since 5.31
 */
const PRECISION = 14;

/**
 * Cases (0/1 already defined as CASSE_LOWER/CASE_UPPER).
 * @since 4.19
 */
const CASE_TITLE = 2, CASE_DASH  = 3,
      CASE_SNAKE = 4, CASE_CAMEL = 5;

/**
 * Path info.
 * @since 6.0
 */
const PATHINFO_TYPE = 0;

/**
 * Namespace separator.
 * @since 6.0
 */
const NAMESPACE_SEPARATOR = '\\';

/**
 * Base-62 alphabet.
 * @since 5.0
 */
const BASE62_ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
