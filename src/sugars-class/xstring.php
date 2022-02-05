<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\util\Strings;

/**
 * X-String.
 *
 * A class for playing with strings in OOP-way.
 *
 * @package froq\util
 * @object  XString
 * @author  Kerem Güneş
 * @since   6.0
 */
class XString implements IteratorAggregate, JsonSerializable, ArrayAccess
{
    /** @var string */
    private string $data;

    /** @var string|null */
    private string|null $encoding = 'UTF-8';

    /**
     * Constructor.
     *
     * @param string      $data
     * @param string|null $encoding
     */
    public function __construct(string $data = '', string|null $encoding = '')
    {
        $this->data = $data;

        if ($encoding !== '') {
            $this->encoding = $encoding;
        }
    }

    /** @magic __toString() */
    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * Get data.
     *
     * @return string
     */
    public function data(): string
    {
        return $this->data;
    }

    /**
     * Get encoding.
     *
     * @return string
     */
    public function encoding(): string
    {
        return $this->encoding;
    }

    /**
     * Length.
     *
     * @return int
     */
    public function length(): int
    {
        return mb_strlen($this->data, $this->encoding);
    }

    /**
     * @aliasOf length()
     */
    public function len()
    {
        return $this->length();
    }

    /**
     * Trim.
     *
     * @param  string $characters
     * @return self
     */
    public function trim(string $characters = " \n\r\t\v\0"): self
    {
        $this->data = trim($this->data, $characters);

        return $this;
    }

    /**
     * Trim left.
     *
     * @param  string $characters
     * @return self
     */
    public function trimLeft(string $characters = " \n\r\t\v\0"): self
    {
        $this->data = ltrim($this->data, $characters);

        return $this;
    }

    /**
     * Trim right.
     *
     * @param  string $characters
     * @return self
     */
    public function trimRight(string $characters = " \n\r\t\v\0"): self
    {
        $this->data = rtrim($this->data, $characters);

        return $this;
    }

    /**
     * Substr.
     *
     * @param  int      $start
     * @param  int|null $length
     * @return self
     */
    public function substr(int $start, int $length = null): self
    {
        $this->data = mb_substr($this->data, $start, $length, $this->encoding);

        return $this;
    }

    /**
     * @aliasOf substr()
     */
    public function sub(...$args)
    {
        return $this->substr(...$args);
    }

    /**
     * @aliasOf substr()
     */
    public function substring(...$args)
    {
        return $this->substr(...$args);
    }

    /**
     * Slice.
     *
     * @param  int      $start
     * @param  int|null $length
     * @param  bool     $init
     * @return static|string
     */
    public function slice(int $start, int $length = null, bool $init = true): static|string
    {
        $ret = mb_substr($this->data, $start, $length, $this->encoding);

        return $init ? new static($ret) : $ret;
    }

    /**
     * Splice.
     *
     * @param  int         $start
     * @param  int|null    $length
     * @param  string|null $replace
     * @param  string|null $replaced
     * @return self
     */
    public function splice(int $start, int $length = null, string $replace = null, string &$replaced = null): self
    {
        $data = mb_str_split($this->data, 1, $this->encoding);
        $repl = array_splice($data, $start, $length, (array) $replace);
        $repl && $replaced = join($repl);

        $this->data = join($data);

        // Buggy.. @cancel
        // $length ??= $this->length();
        // if ($start < 0) {
        //     $start += $this->length();
        //     if ($start < 0) {
        //         $start = 0;
        //     }
        // }

        // $this->data = $this->slice(0, $start, false)
        //             . $replace
        //             . $this->slice($start + $length, null, false);

        return $this;
    }

    /**
     * Char-at, like Javascript's charAt().
     *
     * @param  int $index
     * @return string|null
     */
    public function charAt(int $index): string|null
    {
        if ($index < 0 || $index >= $this->length()) {
            return null;
        }

        return $this->slice($index, 1, false);
    }

    /**
     * Char code-at, like Javascript's charCodeAt().
     *
     * @param  int $index
     * @return int|null
     */
    public function charCodeAt(int $index): int|null
    {
        if ($index < 0 || $index >= $this->length()) {
            return null;
        }

        $chr = $this->slice($index, 1, false);
        if ($chr === '') {
            return null;
        }

        return Strings::ord($chr);
    }

    /**
     * Code point-at, like Javascript's codePointAt().
     *
     * @param  int  $index
     * @param  bool $hex
     * @return int|string|null
     */
    public function codePointAt(int $index, bool $hex = false): int|string|null
    {
        $length = $this->length();
        if ($index < 0 || $index >= $length) {
            return null;
        }

        /** @thanks https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/codePointAt#polyfill */
        $point = null;
        $first = $this->charCodeAt($index);
        if ($first >= 0xD800 && $first <= 0xDBFF && $length > $index + 1) {
            $second = $this->charCodeAt($index + 1);
            if ($second >= 0xDC00 && $second <= 0xDFFF) {
                $point = ($first - 0xD800) * 0x400 + $second - 0xDC00 + 0x10000;
            }
        }

        $point ??= $first;
        if ($hex && $point !== null) {
            $point = dechex($point);
        }

        return $point;
    }

    /**
     * Index, with case-insensitive option.
     *
     * @param  string $search
     * @param  bool   $icase
     * @param  int    $offset
     * @return int|null
     */
    public function index(self|string $search, bool $icase = false, int $offset = 0): int|null
    {
        $index = $icase ? mb_stripos($this->data, (string) $search, $offset, $this->encoding)
                        : mb_strpos($this->data, (string) $search, $offset, $this->encoding);

        return ($index !== false) ? $index : null;
    }

    /**
     * Index-of, like Javascript's indexOf().
     *
     * @param  self|string $search
     * @param  int         $offset
     * @return int|null
     */
    public function indexOf(self|string $search, int $offset = 0): int|null
    {
        $index = mb_strpos($this->data, (string) $search, $offset, $this->encoding);

        return ($index !== false) ? $index : null;
    }

    /**
     * Last index-of, like Javascript's lastIndexOf().
     *
     * @param  self|string $search
     * @param  int         $offset
     * @return int|null
     */
    public function lastIndexOf(self|string $search, int $offset = 0): int|null
    {
        $index = mb_strrpos($this->data, (string) $search, $offset, $this->encoding);

        return ($index !== false) ? $index : null;
    }

    /**
     * Upper (case).
     *
     * @param  bool $tr
     * @return self
     */
    public function upper(bool $tr = false): self
    {
        return $this->case(MB_CASE_UPPER_SIMPLE, $tr);
    }

    /**
     * Lower (case).
     *
     * @param  bool $tr
     * @return self
     */
    public function lower(bool $tr = false): self
    {
        return $this->case(MB_CASE_LOWER_SIMPLE, $tr);
    }

    /**
     * Title (case).
     *
     * @param  bool $tr
     * @return self
     */
    public function title(bool $tr = false): self
    {
        return $this->case(MB_CASE_TITLE_SIMPLE, $tr);
    }

    /**
     * Case converter.
     *
     * @param  int  $case
     * @param  bool $tr
     * @return self
     */
    public function case(int $case, bool $tr = false): self
    {
        switch ($case) {
            case MB_CASE_UPPER:
            case MB_CASE_UPPER_SIMPLE:
                $tr && $this->replace(['ı', 'i'], ['I', 'İ']);
                break;
            case MB_CASE_LOWER:
            case MB_CASE_LOWER_SIMPLE:
                $tr && $this->replace(['I', 'İ'], ['ı', 'i']);
                break;
            case MB_CASE_TITLE:
            case MB_CASE_TITLE_SIMPLE:
                if ($tr) for ($i = 0, $il = $this->length(); $i < $il; $i++) {
                    $char = $this->charAt($i);
                    if ($char === 'i') {
                        $this->splice($i, 1, 'İ');
                    }
                    if ($char === 'I') {
                        $this->splice($i, 1, 'ı');
                    }
                }
        }

        $this->data = mb_convert_case($this->data, $case, $this->encoding);

        return $this;
    }

    /**
     * Upper-first, like ucfirst() but unicode.
     *
     * @param  bool $tr
     * @return self
     */
    public function upperFirst(bool $tr = false): self
    {
        $this->data = mb_ucfirst($this->data, $tr, $this->encoding);

        return $this;
    }

    /**
     * Lower-first, like lcfirst() but unicode.
     *
     * @param  bool $tr
     * @return self
     */
    public function lowerFirst(bool $tr = false): self
    {
        $this->data = mb_lcfirst($this->data, $tr, $this->encoding);

        return $this;
    }

    /**
     * Replace.
     *
     * @param  string|RegExp          $search
     * @param  string|array|callable  $replace
     * @param  bool                   $icase
     * @param  int                    $limit
     * @param  int|null              &$count
     * @return int
     */
    public function replace(string|array|RegExp $search, string|array|callable $replace, bool $icase = false, int $limit = -1, int &$count = null): self
    {
        if ($search instanceof RegExp) {
            $this->data = $search->replace($this->data, $replace, $limit, $count);
        } else {
            $this->data = $icase ? str_ireplace($search, $replace, $this->data, $count)
                                 : str_replace($search, $replace, $this->data, $count);
        }

        return $this;
    }

    /**
     * Sub-string replace.
     *
     * @param  string   $replace
     * @param  int      $offset
     * @param  int|null $length
     * @return self
     */
    public function substrReplace(string $replace, int $offset, int $length = null): self
    {
        $this->splice($offset, $length, $replace);

        return $this;
    }

    /**
     * Sub-string count.
     *
     * @param  string   $search
     * @param  int      $offset
     * @param  int|null $length
     * @return int
     */
    public function substrCount(string $search, int $offset = 0, int $length = null): int
    {
        return substr_count($this->data, $search, $offset, $length);
    }

    /**
     * Sub-string compare.
     *
     * @param  string   $search
     * @param  int      $offset
     * @param  int|null $length
     * @param  bool     $icase
     * @return int
     */
    public function substrCompare(string $search, int $offset = 0, int $length = null, bool $icase = false): int
    {
        return substr_compare($this->data, $search, $offset, $length, $icase);
    }

    /**
     * Remove.
     *
     * @param  string|RegExp  $search
     * @param  bool           $icase
     * @param  int            $limit
     * @param  int|null      &$count
     * @return self
     */
    public function remove(string|array|RegExp $search, bool $icase = false, int $limit = -1, int &$count = null): self
    {
        if ($search instanceof RegExp) {
            $this->data = $search->remove($this->data, $limit, $count);
        } else {
            $this->replace($search, '', $icase, $limit, $count);
        }

        return $this;
    }

    /**
     * Remove whitespaces.
     *
     * @param  bool $trim
     * @return self
     */
    public function removeWhitespaces(bool $trim = true): self
    {
        $this->replace(new RegExp('\s+'), '');
        $trim && $this->trim();

        return $this;
    }

    /**
     * Reduce whitespaces.
     *
     * @param  bool $trim
     * @return self
     */
    public function reduceWhitespaces(bool $trim = true): self
    {
        $this->replace(new RegExp('\s+'), ' ');
        $trim && $this->trim();

        return $this;
    }

    /**
     * Quote.
     *
     * @param  string $tick
     * @return self
     */
    public function quote(string $tick = "'"): self
    {
        $this->data = $tick . trim($this->data, $tick) . $tick;

        return $this;
    }

    /**
     * Unquote.
     *
     * @param  string $tick
     * @return self
     */
    public function unquote(string $tick = "'"): self
    {
        $this->data = trim($this->data, $tick);

        return $this;
    }

    /**
     * Wrap.
     *
     * @param  string      $start
     * @param  string|null $end
     * @return self
     */
    public function wrap(string $start, string $end = null): self
    {
        if ($end === null || $end === '') {
            $end = $start;
        }

        $this->data = $start . $this->data . $end;

        return $this;
    }

    /**
     * Unwrap.
     *
     * @param  string      $start
     * @param  string|null $end
     * @return self
     */
    public function unwrap(string $start, string $end = null): self
    {
        if ($end === null || $end === '') {
            $end = $start;
        }

        $pattern = vsprintf('^(%s)+|(%s)+$', [
            RegExp::escape($start), RegExp::escape($end)
        ]);

        $this->data = RegExp::from($pattern, 'u')->remove($this->data);

        return $this;
    }

    /**
     * Escape, addcslashes() bridge.
     *
     * @param  string $chars
     * @return self
     */
    public function escape(string $chars = "\0\'\"\\"): self
    {
        $this->data = addcslashes($this->data, $chars);

        return $this;
    }

    /**
     * Unescape, stripcslashes() bridge.
     *
     * @return self
     */
    public function unescape(): self
    {
        $this->data = stripcslashes($this->data);

        return $this;
    }

    /**
     * Append.
     *
     * @param  string $data
     * @return self
     */
    public function append(string $data): self
    {
        $this->data .= $data;

        return $this;
    }

    /**
     * Prepend.
     *
     * @param  string $data
     * @return self
     */
    public function prepend(string $data): self
    {
        $this->data = $data . $this->data;

        return $this;
    }

    /**
     * Chunk, like chunk_split() but unicode.
     *
     * @param  int    $length
     * @param  string $separator
     * @param  bool   $join
     * @return self|array
     */
    public function chunk(int $length = 76, string $separator = "\r\n", bool $join = true): self|array
    {
        $chunk = str_chunk($this->data, $length, $separator, $join);

        if ($join) {
            $this->data = $chunk;

            return $this;
        }

        return $chunk;
    }

    /**
     * Concat.
     *
     * @param  string    $data
     * @param  string ...$datas
     * @return self
     */
    public function concat(string $data, string ...$datas): self
    {
        $this->data = str_concat($this->data, $data, ...$datas);

        return $this;
    }

    /**
     * Repeat.
     *
     * @param  string|null $data
     * @param  int         $count
     * @param  bool        $append
     * @return self
     * @throws ValueError
     */
    public function repeat(string $data = null, int $count, bool $append = false): self
    {
        if ($append) {
            if ($data === null) {
                throw new ValueError('No data given to append');
            }

            $this->data .= str_repeat($data, $count);
        } else {
            $this->data = str_repeat($data ?? $this->data, $count);
        }

        return $this;
    }

    /**
     * Reverse.
     *
     * @return self
     */
    public function reverse(): self
    {
        $this->data = mb_strrev($this->data, $this->encoding);

        return $this;
    }

    /**
     * Pad, left/right/both.
     *
     * @param  int    $length
     * @param  string $pad
     * @param  int    $padType
     * @return self
     */
    public function pad(int $length, string $pad = ' ', int $padType = STR_PAD_RIGHT): self
    {
        $this->data = Strings::pad($this->data, $length, $pad, $padType, $this->encoding);

        return $this;
    }

    /**
     * Pad left.
     *
     * @param  int    $length
     * @param  string $pad
     * @return self
     */
    public function padLeft(int $length, string $pad = ' '): self
    {
        return $this->pad($length, $pad, STR_PAD_LEFT);
    }

    /**
     * Pad right.
     *
     * @param  int    $length
     * @param  string $pad
     * @return self
     */
    public function padRight(int $length, string $pad = ' '): self
    {
        return $this->pad($length, $pad, STR_PAD_RIGHT);
    }

    /**
     * Compare.
     *
     * @param  self|string $data
     * @param  bool        $icase
     * @param  int|null    $length
     * @return int
     */
    public function compare(self|string $data, bool $icase = false, int $length = null): int
    {
        return Strings::compare($this->data, (string) $data, $icase, $length, $this->encoding);
    }

    /**
     * Compare with locale.
     *
     * @param  self|string $search
     * @param  bool        $icase
     * @return int
     */
    public function compareLocale(self|string $data, string $locale): int
    {
        return Strings::compareLocale($this->data, (string) $data, $locale);
    }

    /**
     * Equals checker.
     *
     * @param  self|string|array<self|string> $search
     * @param  bool                           $icase
     * @return bool
     */
    public function equals(self|string|array $data, bool $icase = false): bool
    {
        is_array($data) || $data = [$data];

        foreach ($data as $data) {
            $data = (string) $data;
            $ok = (!$icase || $data === '') ? $this->data === $data
                : mb_stripos($this->data, $data, 0, $this->encoding) === 0;

            if (!$ok) {
                return false;
            }
        }
        return true;
    }

    /**
     * Contains-with checker.
     *
     * @param  self|string|array<self|string> $search
     * @param  bool                           $icase
     * @return bool
     */
    public function contains(self|string|array $search, bool $icase = false): bool
    {
        is_array($search) || $search = [$search];

        foreach ($search as $data) {
            $data = (string) $data;
            if (str_has($this->data, $data, $icase)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Starts-with checker.
     *
     * @param  self|string|array<self|string> $search
     * @param  bool                           $icase
     * @return bool
     */
    public function startsWith(self|string|array $search, bool $icase = false): bool
    {
        is_array($search) || $search = [$search];

        foreach ($search as $data) {
            $data = (string) $data;
            if (str_has_prefix($this->data, $data, $icase)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Ends-with checker.
     *
     * @param  self|string|array<self|string> $search
     * @param  bool                           $icase
     * @return bool
     */
    public function endsWith(self|string|array $search, bool $icase = false): bool
    {
        is_array($search) || $search = [$search];

        foreach ($search as $data) {
            $data = (string) $data;
            if (str_has_suffix($this->data, $data, $icase)) {
                return true;
            }
        }
        return false;
    }

    /**
     * String split.
     *
     * @param  int         $length
     * @param  string|null $class
     * @return iterable
     */
    public function splits(int $length = 1, string $class = null): iterable
    {
        $data = mb_str_split($this->data, $length, $this->encoding);

        return ($class !== null) ? new $class($data) : $data;
    }

    /**
     * String split for given iterable class.
     *
     * @param  string $class
     * @param  int    $length
     * @return iterable
     */
    public function splitsTo(string $class, int $length = 1): iterable
    {
        return $this->splits($length, $class);
    }

    /**
     * RegExp split.
     *
     * @param  string|RegExp $pattern
     * @param  int           $limit
     * @param  int           $flags
     * @param  string|null   $class
     * @return iterable|null
     */
    public function split(string|RegExp $pattern, int $limit = -1, int $flags = 0, string $class = null): iterable|null
    {
        if (is_string($pattern)) {
            $pattern = new RegExp($pattern, 'u');
        }
        pre($pattern."");

        return $pattern->split($this->data, $limit, $flags, $class);
    }

    /**
     * RegExp split to iterable class.
     *
     * @param  string|RegExp $pattern
     * @param  string        $class
     * @param  int           $limit
     * @param  int           $flags
     * @return iterable|null
     */
    public function splitTo(string|RegExp $pattern, string $class, int $limit = -1, int $flags = 0): iterable|null
    {
        return $this->split($pattern, $limit, $flags, $class);
    }

    /**
     * Match possibles.
     *
     * @param  string|RegExp $pattern
     * @param  int           $flags
     * @param  int           $offset
     * @param  string|null   $class
     * @return iterable|null
     */
    public function match(string|RegExp $pattern, int $flags = 0, int $offset = 0, string $class = null): iterable|null
    {
        if (is_string($pattern)) {
            $pattern = new RegExp($pattern, 'u');
        }

        return $pattern->match($this->data, $flags, $offset, $class);
    }

    /**
     * Match all possibles.
     *
     * @param  string|RegExp $pattern
     * @param  int           $flags
     * @param  int           $offset
     * @param  string|null   $class
     * @return iterable|null
     */
    public function matchAll(string|RegExp $pattern, int $flags = 0, int $offset = 0, string $class = null): iterable|null
    {
        if (is_string($pattern)) {
            $pattern = new RegExp($pattern, 'u');
        }

        return $pattern->matchAll($this->data, $flags, $offset, $class);
    }

    /**
     * Find possible matches (@see sugars.grep()).
     *
     * @param  string|RegExp $pattern
     * @param  bool          $named
     * @param  string|null   $class
     * @return string|iterable|null
     */
    public function grep(string|RegExp $pattern, bool $named = false, string $class = null): string|iterable|null
    {
        if (is_string($pattern)) {
            $pattern = new RegExp($pattern, 'u');
        }

        return $pattern->grep($this->data, $named, $class);
    }

    /**
     * Find all possible matches (@see sugars.grep_all()).
     *
     * @param  string|RegExp $pattern
     * @param  bool          $named
     * @param  bool          $uniform
     * @param  string|null   $class
     * @return iterable|null
     */
    public function grepAll(string|RegExp $pattern, bool $named = false, bool $uniform = false, string $class = null): iterable|null
    {
        if (is_string($pattern)) {
            $pattern = new RegExp($pattern, 'u');
        }

        return $pattern->grepAll($this->data, $named, $uniform, $class);
    }

    /**
     * Test, like Javascript's test.
     *
     * @param  string|RegExp $pattern
     * @return bool
     */
    public function test(string|RegExp $pattern): bool
    {
        if (is_string($pattern)) {
            $pattern = new RegExp($pattern, 'u');
        }

        return $pattern->test($this->data);
    }

    /**
     * Search, like Javascript's search.
     *
     * @param  string|RegExp $pattern
     * @return int
     */
    public function search(string|RegExp $pattern): int
    {
        if (is_string($pattern)) {
            $pattern = new RegExp($pattern, 'u');
        }

        return $pattern->search($this->data);
    }

    /**
     * Slug (@see sugars.slug()).
     *
     * @param  string $preserve
     * @param  string $replace
     * @return string
     */
    public function slug(string $preserve = '', string $replace = '-'): string
    {
        return slug($this->data, $preserve, $replace);
    }

    /**
     * Interface to sscanf().
     *
     * @param  string    $format
     * @param  mixed  ...$vars
     * @return int|array|null
     */
    public function scan(string $format, mixed &...$vars): int|array|null
    {
        return sscanf($this->data, $format, ...$vars);
    }

    /**
     * Interface to str_word_count(), but unicode.
     *
     * @param  int $format
     * @return int|array
     */
    public function wordCount(int $format = 0): int|array
    {
        $words = $this->split('[^\p{L}\'\-]+');

        return ($format === 0) ? count($words) : $words;
    }

    /**
     * Interface to wordwrap().
     *
     * @param  int    $width
     * @param  string $break
     * @param  bool   $cut
     * @return self
     */
    public function wordWrap(int $width = 75, string $break = "\n", bool $cut = false): self
    {
        $this->data = wordwrap($this->data, $width, $break, $cut);

        return $this;
    }

    /**
     * Interface to quotemeta().
     *
     * @return self
     */
    public function quotemeta(): self
    {
        $this->data = quotemeta($this->data);

        return $this;
    }

    /**
     * Interface to str_shuffle(), but unicode.
     *
     * @return self
     */
    public function shuffle(): self
    {
        $this->data = str_rand($this->data);

        return $this;
    }

    /**
     * Interface to strtok(), but unicode.
     *
     * @param  string $token
     * @return array|null
     */
    public function token(string $token): array|null
    {
        // No strtok(), corrupting data.
        $data = null;

        if ($token !== '') {
            $data = $this->split($token);

            // For invalid parsing.
            if ($data[0] === $this->data) {
                return null;
            }
        }

        return $data;
    }

    /**
     * Interface to strspn().
     *
     * @param  string   $chars
     * @param  int      $offset
     * @param  int|null $length
     * @return int
     */
    public function span(string $chars, int $offset = 0, int $length = null): int
    {
        return strspn($this->data, $chars, $offset, $length);
    }

    /**
     * Interface to strcspn().
     *
     * @param  string   $chars
     * @param  int      $offset
     * @param  int|null $length
     * @return int
     */
    public function cspan(string $chars, int $offset = 0, int $length = null): int
    {
        return strcspn($this->data, $chars, $offset, $length);
    }

    /**
     * Interface to strpbrk(), but unicode.
     *
     * @param  string $chars
     * @param  bool   $icase
     * @return static
     */
    public function find(string $chars, bool $icase = false): static
    {
        // Not like strpbrk(), reduce by given char order.
        $data = '';

        foreach (mb_str_split($chars, 1, $this->encoding) as $char) {
            $start = $icase ? mb_stripos($this->data, $char, 0, $this->encoding)
                            : mb_strpos($this->data, $char, 0, $this->encoding);

            if ($start !== false) {
                $data = mb_substr($this->data, $start, null, $this->encoding);
                break;
            }
        }

        return new static($data);
    }

    /**
     * Interface to mb_stristr().
     *
     * @param  string $search
     * @param  bool   $before
     * @param  bool   $icase
     * @return static
     */
    public function findFirst(string $search, bool $before = false, bool $icase = false): static
    {
        $data = $icase ? mb_stristr($this->data, $search, $before, $this->encoding)
                       : mb_strstr($this->data, $search, $before, $this->encoding);

        return new static((string) $data);
    }

    /**
     * Interface to mb_strrichr().
     *
     * @param  string $search
     * @param  bool   $before
     * @param  bool   $icase
     * @return static
     */
    public function findLast(string $search, bool $before = false, bool $icase = false): static
    {
        $data = $icase ? mb_strrichr($this->data, $search, $before, $this->encoding)
                       : mb_strrchr($this->data, $search, $before, $this->encoding);

        return new static((string) $data);
    }

    /**
     * Strip HTML tags.
     *
     * @param  string|array|null $allowed
     * @param  bool              $decode
     * @return self
     */
    public function stripTags(string|array $allowed = null, bool $decode = false): self
    {
        $decode && $this->htmlDecode();

        if ($allowed && is_string($allowed)) {
            $allowed = Set::fromSplit($allowed, '\s*,\s*')
                ->map(fn($tag) => trim($tag, '<>'))
                ->array();
        }

        $this->data = strip_tags($this->data, $allowed);

        return $this;
    }

    /**
     * Remove HTML tags.
     *
     * @param  string|array|null $allowed
     * @param  bool              $decode
     * @return self
     */
    public function removeTags(string|array $allowed = null, bool $decode = false): self
    {
        $decode && $this->htmlDecode();

        if ($allowed && is_string($allowed)) {
            $allowed = Set::fromSplit($allowed, '\s*,\s*')
                ->map(fn($tag) => trim($tag, '<>'))
                ->array();
            $pattern = '~<(?!(?:' . join('|', $allowed) . ')\b)(\w+)\b[^>]*/?>(?:.*?</\1>)?~isu';
        } else {
            $pattern = '~<(\w+)\b[^>]*/?>(?:.*?</\1>)?~isu';
        }

        $this->data = preg_remove($pattern, $this->data);

        return $this;
    }

    /**
     * Encode HTML characters.
     *
     * @param  bool $simple
     * @return self
     */
    public function htmlEncode(bool $simple = false): self
    {
        $this->data = $simple ? str_replace(['<', '>'], ['&lt;', '&gt;'], $this->data)
            : str_replace(["'", '"', '<', '>'], ['&#39;', '&#34;', '&lt;', '&gt;'], $this->data);

        return $this;
    }

    /**
     * Decode HTML characters.
     *
     * @param  bool $simple
     * @return self
     */
    public function htmlDecode(bool $simple = false): self
    {
        $this->data = $simple ? str_ireplace(['&lt;', '&gt;'], ['<', '>'], $this->data)
             : str_ireplace(['&#39;', '&#34;', '&lt;', '&gt;'], ["'", '"', '<', '>'], $this->data);

        return $this;
    }

    /**
     * Encode HTML entities.
     *
     * @param  int  $flags
     * @param  bool $double
     * @return self
     */
    public function htmlEntityEncode(int $flags = ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401, bool $double = true): self
    {
        $this->data = htmlentities($this->data, $flags, $this->encoding, $double);

        return $this;
    }

    /**
     * Decode HTML entities.
     *
     * @param  int $flags
     * @return self
     */
    public function htmlEntityDecode(int $flags = ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401): self
    {
        $this->data = html_entity_decode($this->data, $flags, $this->encoding);

        return $this;
    }

    /**
     * Encode special HTML characters.
     *
     * @param  int  $flags
     * @param  bool $double
     * @return self
     */
    public function htmlSpecialCharsEncode(int $flags = ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401, bool $double = true): self
    {
        $this->data = htmlspecialchars($this->data, $flags, $this->encoding, $double);

        return $this;
    }

    /**
     * Decode special HTML characters.
     *
     * @param  int $flags
     * @return self
     */
    public function htmlSpecialCharsDecode(int $flags = ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401): self
    {
        $this->data = htmlspecialchars_decode($this->data, $flags);

        return $this;
    }

    /**
     * Formatter.
     *
     * @param  mixed    $input
     * @param  mixed ...$inputs
     * @return self
     */
    public function format(mixed $input, mixed ...$inputs): self
    {
        $this->data = format($this->data, $input, ...$inputs);

        return $this;
    }

    /**
     * Hexifier.
     *
     * @return self
     */
    public function hex(): self
    {
        $data = bin2hex($this->data);
        if ($data !== false) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Binifier.
     *
     * @return self
     */
    public function bin(): self
    {
        $data = hex2bin($this->data);
        if ($data !== false) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Apply interface to any action in.
     *
     * @param  callable    $func
     * @param  mixed    ...$funcArgs
     * @return self
     */
    public function apply(callable $func, mixed ...$funcArgs): self
    {
        $func = $func->bindTo($this, $this);
        $func(...$funcArgs);

        return $this;
    }

    /**
     * Empty data string.
     *
     * @return self
     */
    public function empty(): self
    {
        $this->data = '';

        return $this;
    }

    /**
     * Empty checker.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->data === '';
    }

    /**
     * Equal checker.
     *
     * @param  string $data
     * @return bool
     */
    public function isEqual(string $data): bool
    {
        return $this->data === $data;
    }

    /**
     * UTF checker.
     *
     * @param  int $bits
     * @return bool
     */
    public function isUtf(int $bits = 8): bool
    {
        return Strings::isUtf($this->data, $bits);
    }

    /**
     * ASCII checker.
     *
     * @return bool
     */
    public function isAscii(): bool
    {
        return Strings::isAscii($this->data);
    }

    /**
     * Binary checker.
     *
     * @return bool
     */
    public function isBinary(): bool
    {
        return Strings::isBinary($this->data);
    }

    /**
     * Base64 checker.
     *
     * @return bool
     */
    public function isBase64(): bool
    {
        return Strings::isBase64($this->data);
    }

    /**
     * Newlines to breaks.
     *
     * @param  bool $xhtml
     * @param  bool $clean
     * @return string
     */
    public function toBr(bool $xhtml = false, bool $clean = false): string
    {
        $this->data = nl2br($this->data, $xhtml);
        $clean && $this->remove(new RegExp('[\r\n]'));

        return $this->data;
    }

    /**
     * Hash data as given algo string.
     *
     * @param  string $algo
     * @return string
     */
    public function toHash(string $algo): string
    {
        return hash($algo, $this->data);
    }

    /**
     * Hash data as crypt string.
     *
     * @param  string $algo
     * @return string
     */
    public function toCrypt(string $salt): string
    {
        return crypt($this->data, $salt);
    }

    /**
     * Hash data as password string.
     *
     * @param  string|null $algo
     * @param  array       $options
     * @return string
     */
    public function toPassword(string|int $algo = null, array $options = []): string
    {
        return password_hash($this->data, $algo, $options);
    }

    /**
     * Get data as bytes.
     *
     * @param  string|null $class
     * @return iterable
     */
    public function toBytes(string $class = null): iterable
    {
        $data = [];
        for ($i = 0, $il = $this->length(); $i < $il; $i++) {
            $data[] = $this->charCodeAt($i);
        }

        return $class ? new $class($data) : $data;
    }

    /**
     * Get data as Base64 string.
     *
     * @return string
     */
    public function toBase64(): string
    {
        return base64_encode($this->data);
    }

    /**
     * Get data as string.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->data;
    }

    /** @inheritDoc IteratorAggregate */ #[\ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        for ($i = 0, $il = $this->length(); $i < $il; $i++) {
            yield $i => $this->charAt($i);
        }
    }

    /** @inheritDoc JsonSerializable */
    public function jsonSerialize(): string
    {
        return $this->data;
    }

    /** @inheritDoc ArrayAccess */
    public function offsetExists(mixed $offset): bool
    {
        is_int($offset) || throw new Error('Non-integer offset');

        return $this->slice($offset, 1, false) !== '';
    }

    /** @inheritDoc ArrayAccess */
    public function offsetGet(mixed $offset): string|null
    {
        is_int($offset) || throw new Error('Non-integer offset');

        return $this->slice($offset, 1, false);
    }

    /** @inheritDoc ArrayAccess */
    public function offsetSet(mixed $offset, mixed $char): never
    {
        throw new Error('Not implemented');
    }

    /** @inheritDoc ArrayAccess */
    public function offsetUnset(mixed $offset): never
    {
        throw new Error('Not implemented');
    }

    /**
     * Create an instance from given data (and encoding).
     *
     * @param  string      $data
     * @param  string|null $encoding
     * @return static
     */
    public static function from(string $data = '', string|null $encoding = ''): static
    {
        return new static($data, $encoding);
    }

    /**
     * Create an instance from random strings.
     *
     * @param  int  $length
     * @param  bool $puncted
     * @return static
     */
    public static function fromRandom(int $length, bool $puncted = false): static
    {
        return new static(random_string($length, $puncted));
    }

    /**
     * Create an instance from random bytes.
     *
     * @param  int $length
     * @return static
     */
    public static function fromRandomBytes(int $length): static
    {
        return new static(random_bytes($length));
    }

    /**
     * Create an instance from given char codes.
     *
     * @param  int ...$codes
     * @return static
     */
    public static function fromCharCode(int ...$codes): static
    {
        $data = '';
        foreach ($codes as $code) {
            $data .= Strings::chr($code);
        }

        return new static($data);
    }

    /**
     * Create an instance from given code points.
     *
     * @param  int ...$codes
     * @return static
     */
    public static function fromCodePoint(int ...$codes): static
    {
        $data = '';
        foreach ($codes as $code) {
            $data .= Strings::chr($code);
        }

        return new static($data);
    }
}