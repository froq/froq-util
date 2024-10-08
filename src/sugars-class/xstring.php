<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\{Stringable, Arrayable, Lengthable};
use froq\util\{Strings, Util};

/**
 * A class for playing with strings in OOP-way.
 *
 * @package global
 * @class   XString
 * @author  Kerem Güneş
 * @since   6.0
 */
class XString implements \Stringable, Stringable, Arrayable, Lengthable, IteratorAggregate, JsonSerializable, ArrayAccess
{
    /** Data. */
    protected string $data;

    /** Encoding. */
    protected string|null $encoding = 'UTF-8';

    /**
     * Constructor.
     *
     * @param string|Stringable $data
     * @param string|null       $encoding
     */
    public function __construct(string|\Stringable $data = '', string|null $encoding = '')
    {
        // Allow null (for internal encoding).
        if ($encoding !== '') $this->encoding = $encoding;

        $this->data = (string) $data;
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return $this->toString();
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
     * Set length (shrink data size).
     *
     * @param  int $length
     * @return self
     * @throws ArgumentError
     */
    public function setLength(int $length): self
    {
        if ($length < 0) {
            throw new ArgumentError('Argument $length cannot be negative');
        }

        $this->data = mb_substr($this->data, 0, $length, $this->encoding);

        return $this;
    }

    /**
     * Get length.
     *
     * @return int
     */
    public function getLength(): int
    {
        return mb_strlen($this->data, $this->encoding);
    }

    /**
     * Trim.
     *
     * @param  string $characters
     * @return self
     */
    public function trim(string $characters = TRIM_CHARACTERS): self
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
    public function trimLeft(string $characters = TRIM_CHARACTERS): self
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
    public function trimRight(string $characters = TRIM_CHARACTERS): self
    {
        $this->data = rtrim($this->data, $characters);

        return $this;
    }

    /**
     * Sub string for self.
     *
     * @param  int      $start
     * @param  int|null $length
     * @return self
     */
    public function sub(int $start, int $length = null): self
    {
        $this->data = $this->subs($start, $length);

        return $this;
    }

    /**
     * Sub string for return.
     *
     * @param  int      $start
     * @param  int|null $length
     * @return string
     */
    public function subs(int $start, int $length = null): string
    {
        return mb_substr($this->data, $start, $length, $this->encoding);
    }

    /**
     * Cut string for self.
     *
     * @param  int $length
     * @return self
     */
    public function cut(int $length): self
    {
        $this->data = $this->cuts($length);

        return $this;
    }

    /**
     * Cut string for return.
     *
     * @param  int $length
     * @return string
     */
    public function cuts(int $length): string
    {
        return ($length >= 0) ? mb_substr($this->data, 0, $length, $this->encoding)
                              : mb_substr($this->data, $length, null, $this->encoding);
    }

    /**
     * Splice.
     *
     * @param  int                $start
     * @param  int|null           $length
     * @param  string|array|null  $replace
     * @param  string|array|null &$replaced
     * @return self
     */
    public function splice(int $start, int $length = null, string|array $replace = null, string|array &$replaced = null): self
    {
        $charlist = mb_str_split($this->data, 1, $this->encoding);
        $replaced = array_splice($charlist, $start, $length, (array) $replace);

        $this->data = join($charlist);

        // @cancel: Buggy.
        // $length ??= $this->getLength();
        // if ($start < 0) {
        //     $start += $this->getLength();
        //     if ($start < 0) {
        //         $start = 0;
        //     }
        // }
        // $this->data = $this->subs(0, $start)
        //     . $replace . $this->subs($start + $length);

        return $this;
    }

    /**
     * Slice.
     *
     * @param  int      $start
     * @param  int|null $length
     * @return self
     */
    public function slice(int $start, int $length = null): self
    {
        $this->data = mb_substr($this->data, $start, $length, $this->encoding);

        return $this;
    }

    /**
     * Slice before.
     *
     * @param  self|string $search
     * @param  int|null    $length
     * @param  bool        $icase
     * @param  bool        $last
     * @param  int         $offset
     * @return self
     */
    public function sliceBefore(self|string $search, int $length = null, bool $icase = false, bool $last = false,
        int $offset = 0): self
    {
        $index = $this->index($search, $icase, $last, $offset);

        if ($index !== null) {
            $this->data = mb_substr($this->data, 0, $index, $this->encoding);
            if ($length !== null) {
                $this->data = ($length >= 0) ? mb_substr($this->data, 0, $length, $this->encoding)
                    : mb_substr($this->data, $length, null, $this->encoding);
            }
        } else {
            $this->data = ''; // Not found.
        }

        return $this;
    }

    /**
     * Slice after.
     *
     * @param  self|string $search
     * @param  int|null    $length
     * @param  bool        $icase
     * @param  bool        $last
     * @param  int         $offset
     * @return self
     */
    public function sliceAfter(self|string $search, int $length = null, bool $icase = false, bool $last = false,
        int $offset = 0): self
    {
        $index = $this->index($search, $icase, $last, $offset);

        if ($index !== null) {
            $this->data = mb_substr($this->data, $index + mb_strlen($search, $this->encoding), null, $this->encoding);
            if ($length !== null) {
                $this->data = ($length >= 0) ? mb_substr($this->data, 0, $length, $this->encoding)
                                             : mb_substr($this->data, $length, null, $this->encoding);
            }
        } else {
            $this->data = ''; // Not found.
        }

        return $this;
    }

    /**
     * @alias sliceBefore()
     */
    public function before(...$args)
    {
        return $this->sliceBefore(...$args);
    }

    /**
     * @alias sliceAfter()
     */
    public function after(...$args)
    {
        return $this->sliceAfter(...$args);
    }

    /**
     * Char, for single characters but returns null if index is exceeded.
     *
     * @param  int $index
     * @return string|null
     */
    public function char(int $index): string|null
    {
        // Exceeding nagetive indexes must return "", like positives.
        if ($index < 0 && -$index > $this->getLength()) {
            return null;
        }

        $char = mb_substr($this->data, $index, 1, $this->encoding);

        return ($char !== '') ? $char : null;
    }

    /**
     * Char-at, like String.charAt() in JavaScript but accepts negative indexes.
     *
     * @param  int $index
     * @return string|null
     */
    public function charAt(int $index): string|null
    {
        return ($char = $this->char($index)) !== null ? $char : null;
    }

    /**
     * Char code-at, like String.charCodeAt() in JavaScript but accepts negative indexes.
     *
     * @param  int $index
     * @return int|null
     */
    public function charCodeAt(int $index): int|null
    {
        return ($char = $this->char($index)) !== null ? Strings::ord($char) : null;
    }

    /**
     * @alias charAt()
     */
    public function chr(...$args)
    {
        return $this->charAt(...$args);
    }
    /**
     * @alias charCodeAt()
     */
    public function ord(...$args)
    {
        return $this->charCodeAt(...$args);
    }

    /**
     * Code point-at, like String.codePointAt() in JavaScript.
     *
     * @param  int  $index
     * @param  bool $hex
     * @return int|string|null
     */
    public function codePointAt(int $index, bool $hex = false): int|string|null
    {
        /** @thanks https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/codePointAt#polyfill */
        $point = null;

        $first = $this->charCodeAt($index);
        if ($first !== null) {
            if ($first >= 0xD800 && $first <= 0xDBFF && $this->getLength() > $index + 1) {
                $second = $this->charCodeAt($index + 1);
                if ($second >= 0xDC00 && $second <= 0xDFFF) {
                    $point = ($first - 0xD800) * 0x400 + $second - 0xDC00 + 0x10000;
                }
            }
        }

        $point ??= $first;
        if ($hex && $point !== null) {
            $point = dechex($point);
        }

        return $point;
    }

    /**
     * Index, with case-insensitive/last options.
     *
     * @param  self|string $search
     * @param  bool        $icase
     * @param  bool        $last
     * @param  int         $offset
     * @return int|null
     */
    public function index(self|string $search, bool $icase = false, bool $last = false, int $offset = 0): int|null
    {
        if ($last) {
            $index = $icase ? mb_strripos($this->data, (string) $search, $offset, $this->encoding)
                            : mb_strrpos($this->data, (string) $search, $offset, $this->encoding);
        } else {
            $index = $icase ? mb_stripos($this->data, (string) $search, $offset, $this->encoding)
                            : mb_strpos($this->data, (string) $search, $offset, $this->encoding);
        }

        return ($index !== false) ? $index : null;
    }

    /**
     * Index-of, like String.indexOf() in JavaScript.
     *
     * @param  self|string $search
     * @param  bool        $icase
     * @param  int         $offset
     * @return int|null
     */
    public function indexOf(self|string $search, bool $icase = false, int $offset = 0): int|null
    {
        return $this->index($search, $icase, false, $offset);
    }

    /**
     * Last index-of, like String.lastIndexOf() in JavaScript.
     *
     * @param  self|string $search
     * @param  bool        $icase
     * @param  int         $offset
     * @return int|null
     */
    public function lastIndexOf(self|string $search, bool $icase = false, int $offset = 0): int|null
    {
        return $this->index($search, $icase, true, $offset);
    }

    /**
     * Upper (case).
     *
     * @param  int|null $index N-index stuff (kinda ucfirst/lcfirst).
     * @param  bool     $tr    For Turkish characters.
     * @return self
     */
    public function upper(int $index = null, bool $tr = false): self
    {
        return $this->case(MB_CASE_UPPER_SIMPLE, $index, $tr);
    }

    /**
     * Lower (case).
     *
     * @param  int|null $index N-index stuff (kinda ucfirst/lcfirst).
     * @param  bool     $tr    For Turkish characters.
     * @return self
     */
    public function lower(int $index = null, bool $tr = false): self
    {
        return $this->case(MB_CASE_LOWER_SIMPLE, $index, $tr);
    }

    /**
     * Title (case).
     *
     * @param  bool $tr For Turkish characters.
     * @return self
     */
    public function title(bool $tr = false): self
    {
        return $this->case(MB_CASE_TITLE_SIMPLE, null, $tr);
    }

    /**
     * Case converter.
     *
     * @param  int      $case
     * @param  int|null $index N-index stuff (kinda ucfirst/lcfirst, for upper/lower only).
     * @param  bool     $tr    For Turkish characters.
     * @return self
     */
    public function case(int $case, int $index = null, bool $tr = false): self
    {
        switch ($case) {
            case MB_CASE_UPPER:
            case MB_CASE_UPPER_SIMPLE:
                if ($index !== null) {
                    // Some speed.
                    if ($index === 0) {
                        return $this->upperFirst($tr);
                    }

                    $char = $this->char($index);
                    if ($char !== null) {
                        $char = new self($char, $this->encoding);
                        $char->case($case, null, $tr);
                        $this->splice($index, 1, $char->data);
                    }

                    return $this;
                }

                $tr && $this->replace(['ı', 'i'], ['I', 'İ']);
                break;
            case MB_CASE_LOWER:
            case MB_CASE_LOWER_SIMPLE:
                if ($index !== null) {
                    // Some speed.
                    if ($index === 0) {
                        return $this->lowerFirst($tr);
                    }

                    $char = $this->char($index);
                    if ($char !== null) {
                        $char = new self($char, $this->encoding);
                        $char->case($case, null, $tr);
                        $this->splice($index, 1, $char->data);
                    }

                    return $this;
                }

                $tr && $this->replace(['I', 'İ'], ['ı', 'i']);
                break;
            case MB_CASE_TITLE:
            case MB_CASE_TITLE_SIMPLE:
                if ($tr) for ($i = 0, $il = $this->getLength(); $i < $il; $i++) {
                    $char = $this->char($i);
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
     * @param  string|array|RegExp   $search
     * @param  string|array|callable $replace
     * @param  bool                  $icase
     * @param  int                   $limit
     * @param  int|null              &$count
     * @param  int                   $flags
     * @param  string|null           $class
     * @param  bool                  $re @internal
     * @return self
     */
    public function replace(string|array|RegExp $search, string|array|callable $replace, bool $icase = false,
        int $limit = -1, int &$count = null, int $flags = 0, string $class = null, bool $re = false): self
    {
        if ($search instanceof RegExp) {
            $this->data = $search->replace($this->data, $replace, $limit, $count, $flags, $class);
        } elseif ($re || (is_string($search) && is_callable($replace))) {
            $this->data = RegExp::fromPattern($search)->replace($this->data, $replace, $limit, $count, $flags, $class);
        } else {
            $this->data = $icase ? str_ireplace($search, $replace, $this->data, $count)
                                 : str_replace($search, $replace, $this->data, $count);
        }

        return $this;
    }

    /**
     * Replace-callback, for callback replacement.
     *
     * @param  string|RegExp $search
     * @param  callable      $callback
     * @param  int           $limit
     * @param  int|null      &$count
     * @param  int           $flags
     * @param  string|null   $class
     * @return self
     */
    public function replaceCallback(string|RegExp $search, callable $callback, int $limit = -1, int &$count = null,
        int $flags = 0, string $class = null): self
    {
        return $this->replace($search, $callback, false, $limit, $count, $flags, $class, true);
    }

    /**
     * Substring replace.
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
     * Substring count.
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
     * Substring compare.
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
     * @param  string|array|RegExp $search
     * @param  bool                $icase
     * @param  int                 $limit
     * @param  int|null            &$count
     * @param  bool                $re @internal
     * @return self
     */
    public function remove(string|array|RegExp $search, bool $icase = false, int $limit = -1, int &$count = null,
        bool $re = false): self
    {
        if ($search instanceof RegExp) {
            $this->data = $search->remove($this->data, $limit, $count);
        } elseif ($re) {
            $this->data = RegExp::fromPattern($search)->remove($this->data, $limit, $count);
        } else {
            $this->replace($search, '', $icase, $limit, $count);
        }

        return $this;
    }

    /**
     * Remove spaces.
     *
     * @param  bool $trim
     * @return self
     */
    public function removeSpaces(bool $trim = true): self
    {
        $this->replace(new RegExp('\s+'), '');
        $trim && $this->trim();

        return $this;
    }

    /**
     * Reduce spaces.
     *
     * @param  bool $trim
     * @return self
     */
    public function reduceSpaces(bool $trim = true): self
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
     * @param  string      $left
     * @param  string|null $right
     * @return self
     */
    public function wrap(string $left, string $right = null): self
    {
        $this->data = $left . $this->data . ($right ?? $left);

        return $this;
    }

    /**
     * Unwrap.
     *
     * @param  string      $left
     * @param  string|null $right
     * @return self
     */
    public function unwrap(string $left, string $right = null): self
    {
        $pattern = vsprintf('^(%s)+|(%s)+$', [
            RegExp::escape($left), RegExp::escape($right ?? $left)
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
     * @param  self|string $data
     * @return self
     */
    public function append(self|string $data): self
    {
        $this->data .= $data;

        return $this;
    }

    /**
     * Prepend.
     *
     * @param  self|string $data
     * @return self
     */
    public function prepend(self|string $data): self
    {
        $this->data = $data . $this->data;

        return $this;
    }

    /**
     * Chunk, like chunk_split() but unicode.
     *
     * @param  int          $length
     * @param  string|false $separator
     * @param  bool         $join
     * @param  bool         $chop
     * @return self|array
     */
    public function chunk(int $length = 76, string|false $separator = "\r\n", bool $join = true, bool $chop = false): self|array
    {
        $chunk = str_chunk($this->data, $length, $separator, $join, $chop);

        if ($join && $separator !== false) {
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
     * @throws ArgumentError
     */
    public function repeat(string|null $data, int $count, bool $append = false): self
    {
        if ($append) {
            if ($data === null) {
                throw new ArgumentError('No data given to append');
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
     * @param  string $string
     * @param  int    $type
     * @return self
     */
    public function pad(int $length, string $string = ' ', int $type = STR_PAD_RIGHT): self
    {
        $this->data = Strings::pad($this->data, $length, $string, $type, $this->encoding);

        return $this;
    }

    /**
     * Pad left.
     *
     * @param  int    $length
     * @param  string $string
     * @return self
     */
    public function padLeft(int $length, string $string = ' '): self
    {
        return $this->pad($length, $string, STR_PAD_LEFT);
    }

    /**
     * Pad right.
     *
     * @param  int    $length
     * @param  string $string
     * @return self
     */
    public function padRight(int $length, string $string = ' '): self
    {
        return $this->pad($length, $string, STR_PAD_RIGHT);
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
     * @param  self|string $data
     * @param  string      $locale
     * @return int
     */
    public function compareLocale(self|string $data, string $locale): int
    {
        return Strings::compareLocale($this->data, (string) $data, $locale);
    }

    /**
     * Equals checker.
     *
     * @param  self|string|array<self|string> $data
     * @param  bool                           $icase
     * @return bool
     */
    public function equals(self|string|array $data, bool $icase = false): bool
    {
        is_array($data) || $data = [$data];

        foreach ($data as $dat) {
            if (str_compare($this->data, (string) $dat, $icase) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Includes checker.
     *
     * @param  self|string|array<self|string> $chars
     * @return bool
     */
    public function includes(self|string|array $chars): bool
    {
        is_array($chars) || $chars = [$chars];

        return strpbrk($this->data, join($chars)) !== false;
    }

    /**
     * Contains checker.
     *
     * @param  self|string|array<self|string> $search
     * @param  bool                           $icase
     * @return bool
     */
    public function contains(self|string|array $search, bool $icase = false): bool
    {
        is_array($search) || $search = [$search];

        return str_has($this->data, $search, $icase);
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

        return str_has_prefix($this->data, $search, $icase);
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

        return str_has_suffix($this->data, $search, $icase);
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
     * String split for XArray class.
     *
     * @param  int $length
     * @return XArray
     */
    public function xsplits(int $length = 1): XArray
    {
        return $this->splits($length, XArray::class);
    }

    /**
     * RegExp split.
     *
     * @param  string|RegExp $pattern
     * @param  int           $limit
     * @param  int           $flags
     * @param  string|null   $class
     * @param  array|null    $options
     * @return iterable|null
     */
    public function split(string|RegExp $pattern, int $limit = -1, int $flags = 0, string $class = null,
        array $options = null): iterable|null
    {
        if (is_string($pattern)) {
            // Prepare single chars.
            if (strlen($pattern) === 1) {
                // @tome: See escape in sugars' split().
                $pattern = RegExp::prepare($pattern, 'u', quote: true);
            }
            $pattern = RegExp::fromPattern($pattern);
        }

        return $pattern->split($this->data, $limit, $flags, $class, $options);
    }

    /**
     * RegExp split to iterable class.
     *
     * @param  string|RegExp $pattern
     * @param  string        $class
     * @param  int           $limit
     * @param  int           $flags
     * @param  array|null    $options
     * @return iterable
     */
    public function splitTo(string|RegExp $pattern, string $class, int $limit = -1, int $flags = 0, array $options = null): iterable
    {
        return $this->split($pattern, $limit, $flags, $class, $options);
    }

    /**
     * RegExp split to XArray class.
     *
     * @param  string|RegExp $pattern
     * @param  int           $limit
     * @param  int           $flags
     * @param  array|null    $options
     * @return XArray
     */
    public function xsplit(string|RegExp $pattern, int $limit = -1, int $flags = 0, array $options = null): XArray
    {
        return $this->split($pattern, $limit, $flags, XArray::class, $options);
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
            $pattern = RegExp::fromPattern($pattern);
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
            $pattern = RegExp::fromPattern($pattern);
        }

        return $pattern->matchAll($this->data, $flags, $offset, $class);
    }

    /**
     * Match possible names.
     *
     * @param  string|RegExp $pattern
     * @param  int           $flags
     * @param  int           $offset
     * @param  string|null   $class
     * @return iterable|null
     */
    public function matchNames(string|RegExp $pattern, int $flags = 0, int $offset = 0, string $class = null): iterable|null
    {
        if (is_string($pattern)) {
            $pattern = RegExp::fromPattern($pattern);
        }

        return $pattern->matchNames($this->data, $flags, $offset, $class);
    }

    /**
     * Match all possible names.
     *
     * @param  string|RegExp $pattern
     * @param  int           $flags
     * @param  int           $offset
     * @param  string|null   $class
     * @return iterable|null
     */
    public function matchAllNames(string|RegExp $pattern, int $flags = 0, int $offset = 0, string $class = null): iterable|null
    {
        if (is_string($pattern)) {
            $pattern = RegExp::fromPattern($pattern);
        }

        return $pattern->matchAllNames($this->data, $flags, $offset, $class);
    }

    /**
     * Grep.
     *
     * @param  string|RegExp $pattern
     * @param  bool          $named
     * @param  string|null   $class
     * @return string|iterable|null
     */
    public function grep(string|RegExp $pattern, bool $named = false, string $class = null): string|iterable|null
    {
        if (is_string($pattern)) {
            $pattern = RegExp::fromPattern($pattern);
        }

        return $pattern->grep($this->data, $named, $class);
    }

    /**
     * Grep all.
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
            $pattern = RegExp::fromPattern($pattern);
        }

        return $pattern->grepAll($this->data, $named, $uniform, $class);
    }

    /**
     * X-Grep all.
     *
     * @param  string|RegExp $pattern
     * @param  bool          $named
     * @return XString|XArray|null
     */
    public function xgrep(string|RegExp $pattern, bool $named = false): XString|XArray|null
    {
        $ret = $this->grep($pattern, $named);

        return match (true) {
            is_string($ret) => new XString($ret),
            is_array($ret)  => new XArray($ret),
            default         => null
        };
    }

    /**
     * X-Grep all.
     *
     * @param  string|RegExp $pattern
     * @param  bool          $named
     * @param  bool          $uniform
     * @return XString|XArray|null
     */
    public function xgrepAll(string|RegExp $pattern, bool $named = false, bool $uniform = false): XString|XArray|null
    {
        $ret = $this->grepAll($pattern, $named, $uniform);

        return match (true) {
            is_string($ret) => new XString($ret),
            is_array($ret)  => new XArray($ret),
            default         => null
        };
    }

    /**
     * Test, like String.test() in JavaScript.
     *
     * @param  string|RegExp $pattern
     * @return bool
     */
    public function test(string|RegExp $pattern): bool
    {
        if (is_string($pattern)) {
            $pattern = RegExp::fromPattern($pattern);
        }

        return $pattern->test($this->data);
    }

    /**
     * Search, like String.search() in JavaScript.
     *
     * @param  string|RegExp $pattern
     * @return int
     */
    public function search(string|RegExp $pattern): int
    {
        if (is_string($pattern)) {
            $pattern = RegExp::fromPattern($pattern);
        }

        return $pattern->search($this->data);
    }

    /**
     * Slug.
     *
     * @param  string $preserve
     * @param  string $replace
     * @param  bool   $trim
     * @param  bool   $lower
     * @return string
     */
    public function slug(string $preserve = '', string $replace = '-', bool $trim = true, bool $lower = true): string
    {
        return slug($this->data, $preserve, $replace, $trim, $lower);
    }

    /**
     * X-Slug.
     *
     * @param  string $preserve
     * @param  string $replace
     * @param  bool   $trim
     * @param  bool   $lower
     * @return string
     */
    public function xslug(string $preserve = '', string $replace = '-', bool $trim = true, bool $lower = true): XString
    {
        return new XString(slug($this->data, $preserve, $replace, $trim, $lower));
    }

    /**
     * Interface to sscanf().
     *
     * @param  string    $format
     * @param  mixed  ...$vars
     * @return int|array
     */
    public function scan(string $format, mixed &...$vars): int|array
    {
        return str_scan($this->data, $format, ...$vars);
    }

    /**
     * Interface to str_word_count(), but unicode.
     *
     * @param  int $format
     * @return int|array
     */
    public function wordCount(int $format = 0): int|array
    {
        return str_wordcount($this->data, $format);
    }

    /**
     * Interface to wordwrap(), but unicode.
     *
     * @param  int    $width
     * @param  string $break
     * @param  bool   $cut
     * @return self
     */
    public function wordWrap(int $width = 75, string $break = "\n", bool $cut = false): self
    {
        $this->data = str_wordwrap($this->data, $width, $break, $cut);

        return $this;
    }

    /**
     * Interface to quotemeta().
     *
     * @return self
     */
    public function quoteMeta(): self
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
        $this->data = mb_str_shuffle($this->data, $this->encoding);

        return $this;
    }

    /**
     * Interface to strtok(), but unicode.
     *
     * @param  string $pattern
     * @return array|null
     */
    public function token(string $pattern): array|null
    {
        // No strtok(), corrupting data.
        $data = null;

        if ($pattern !== '') {
            $data = $this->split($pattern);

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
     * Note: For "icase" multiple chars must be provided (eg: iİ).
     *
     * @param  string $chars
     * @return static
     */
    public function find(string $chars): static
    {
        $data = strpbrk($this->data, $chars);

        return new static((string) $data, $this->encoding);
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

        return new static((string) $data, $this->encoding);
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

        return new static((string) $data, $this->encoding);
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
            $allowed = Set::fromSplit('\s*,\s*', $allowed)
                ->map(fn(string $tag): string => trim($tag, '<>'))
                ->toArray();
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
            $allowed = Set::fromSplit('\s*,\s*', $allowed)
                ->map(fn(string $tag): string => strtolower(trim($tag, '<>')))
                ->toArray();
        }

        /* @cancel: Slow & memo-expensive.
        $doc = new DOMDocument();
        $doc->loadXML('<?xml version="1.0" encoding="utf-8"?><xstring>' . $this->data . '</xstring>');
        $data = '';
        foreach ($doc->firstChild->childNodes as $node) {
            if ($allowed && in_array($node->nodeName, $allowed, true)) {
                $temp  = new DOMDocument();
                $data .= $temp->saveXML($temp->importNode($node, true));
                unset($temp);
            } elseif ($node->nodeName === '#text') {
                $data .= $node->nodeValue;
            }
        }
        $this->data = $data;
        return $this; */

        static $pattern = '~<(\w[\w-]*)\b[^>]*/?>(?:.*?</\1>)?~isu';

        if (!$allowed) {
            $data = preg_remove($pattern, $this->data);
        } else {
            $data = $this->data;
            if (preg_match_all($pattern, $data, $match)) {
                foreach ($match[0] as $i => $content) {
                    if (!in_array(/* $tag = */ strtolower($match[1][$i]), $allowed, true)) {
                        $data = preg_remove('~' . preg_quote($content) . '~isu', $data, 1);
                    }
                }
            }
        }

        $this->data = $data;

        return $this;
    }

    /**
     * As a syntactic sugar for *Encode() methods.
     */
    public function encode(string $func, mixed ...$funcArgs): self
    {
        $func .= 'Encode';

        return $this->$func(...$funcArgs);
    }

    /**
     * As a syntactic sugar for *Decode() methods.
     */
    public function decode(string $func, mixed ...$funcArgs): self
    {
        $func .= 'Decode';

        return $this->$func(...$funcArgs);
    }

    /**
     * Encode URL characters.
     *
     * @param  bool $raw
     * @return self
     */
    public function urlEncode(bool $raw = false): self
    {
        $this->data = $raw ? rawurlencode($this->data) : urlencode($this->data);

        return $this;
    }

    /**
     * Decode URL characters.
     *
     * @param  bool $raw
     * @return self
     */
    public function urlDecode(bool $raw = false): self
    {
        $this->data = $raw ? rawurldecode($this->data) : urldecode($this->data);

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
     * Base64 encode.
     *
     * @return self
     */
    public function base64Encode(): self
    {
        $this->data = base64_encode($this->data);

        return $this;
    }

    /**
     * Base64 decode.
     *
     * @param  bool $strict
     * @return self
     */
    public function base64Decode(bool $strict = false): self
    {
        $this->data = (string) base64_decode($this->data, $strict);

        return $this;
    }

    /**
     * Format.
     *
     * @param  mixed ...$arguments
     * @return self
     */
    public function format(mixed $input, mixed ...$arguments): self
    {
        $this->data = format($this->data, ...$arguments);

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
     * Apply given function binding this instance.
     *
     * @param  callable    $func
     * @param  mixed    ...$funcArgs
     * @return self
     */
    public function apply(callable $func, mixed ...$funcArgs): self
    {
        $func = Util::makeClosure($func, $this);
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
     * Hex data as hexadecimal string.
     *
     * @return string
     */
    public function toHex(): string
    {
        return bin2hex($this->data);
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
     * Get data as char codes.
     *
     * @param  string|null $class
     * @return iterable
     */
    public function toCharCodes(string $class = null): iterable
    {
        $data = [];
        for ($i = 0, $il = $this->getLength(); $i < $il; $i++) {
            $data[] = $this->charCodeAt($i);
        }

        return $class ? new $class($data) : $data;
    }

    /**
     * Get data as code points.
     *
     * @param  string|null $class
     * @return iterable
     */
    public function toCodePoints(string $class = null): iterable
    {
        $data = [];
        for ($i = 0, $il = $this->getLength(); $i < $il; $i++) {
            $data[] = $this->codePointAt($i);
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
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->data;
    }

    /**
     * @return StringBuffer
     */
    public function toStringBuffer(): StringBuffer
    {
        return new StringBuffer($this->data, $this->encoding);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->splits();
    }

    /**
     * @return XArray
     */
    public function toXArray(): XArray
    {
        return $this->xsplits();
    }

    /**
     * @inheritDoc
     */
    public function length(): int
    {
        return $this->getLength();
    }

    /**
     * @inheritDoc IteratorAggregate
     */
    public function getIterator(): Generator|Traversable
    {
        for ($i = 0, $il = $this->getLength(); $i < $il; $i++) {
            yield $i => $this->char($i);
        }
    }

    /**
     * @inheritDoc JsonSerializable
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->char($offset) !== null;
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet(mixed $offset): string|null
    {
        return $this->char($offset);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetSet(mixed $offset, mixed $_): never
    {
        throw new UnimplementedError();
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $offset): never
    {
        throw new UnimplementedError();
    }

    /**
     * Get a copy instance.
     *
     * @return static
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Create an instance from given data and encoding.
     *
     * @param  string|Stringable $data
     * @param  string|null       $encoding
     * @return static
     */
    public static function from(string|\Stringable $data, string|null $encoding = ''): static
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
        return new static(random_string($length, $puncted), 'ascii');
    }

    /**
     * Create an instance from random bytes.
     *
     * @param  int $length
     * @return static
     */
    public static function fromRandomBytes(int $length): static
    {
        return new static(random_bytes($length), 'ascii');
    }

    /**
     * Create an instance from given chars.
     *
     * @param  array       $chars
     * @param  string|null $encoding
     * @return static
     */
    public static function fromChars(array $chars, string|null $encoding = ''): static
    {
        return new static(join($chars), $encoding);
    }

    /**
     * Create an instance from given char codes.
     *
     * @param  int ...$codes
     * @return static
     */
    public static function fromCharCodes(int ...$codes): static
    {
        return new static(join(array_map(fn(int $code): ?string => Strings::chr($code), $codes)));
    }
}

/**
 * XString initializer.
 *
 * @param  string|Stringable $data
 * @param  string|null       $encoding
 * @return XString
 */
function xstring(string|\Stringable $data = '', string|null $encoding = ''): XString
{
    return new XString($data, $encoding);
}
