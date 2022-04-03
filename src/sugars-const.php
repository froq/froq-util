<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * For type-check purposes for null/void(undefined) refs.
 */
final class __null {} function __null() { return new __null; }
final class __void {} function __void() { return new __void; }

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
