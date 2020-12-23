<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Nil/nils (null/null string).
 * @const null
 * @since 4.0
 */
const nil  = null,
      nils = '';

/**
 * Yes/no (true/false booleans).
 * @const bool
 * @since 4.0
 */
const yes = true,
      no  = false;

/**
 * Dirsep/patsep (directory/path separators).
 * @const null
 * @since 4.0
 */
const __dirsep = DIRECTORY_SEPARATOR,
      __patsep = PATH_SEPARATOR;

/**
 * Cases (0/1 already defined as CASSE_LOWER/CASE_UPPER).
 * @const string
 * @since 4.19
 */
const CASE_TITLE = 2, CASE_DASH  = 3,
      CASE_SNAKE = 4, CASE_CAMEL = 5;
