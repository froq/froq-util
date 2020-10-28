<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
 * Base 10/16/36/62 characters.
 * @const string
 * @since 4.0, 4.1
 */
const BASE_10_CHARS  = '0123456789',
      BASE_16_CHARS  = '0123456789abcdef',
      BASE_36_CHARS  = '0123456789abcdefghijklmnopqrstuvwxyz',
      BASE_62_CHARS  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
      BASE_62N_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

/**
 * Cases (0/1 already defined as CASSE_LOWER/UPPER).
 * @const string
 * @since 4.19
 */
const CASE_TITLE = 2,
      CASE_DASH  = 3,
      CASE_CAMEL = 4,
      CASE_SNAKE = 5;
