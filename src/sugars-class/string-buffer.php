<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\{Stringable, Arrayable, Lengthable};
use froq\util\Strings;

/**
 * A simple string buffer class, inpired by Java's StringBuffer.
 *
 * @package global
 * @class   StringBuffer
 * @author  Kerem Güneş
 * @since   6.0
 */
class StringBuffer implements \Stringable, Stringable, Arrayable, Lengthable, IteratorAggregate, JsonSerializable, ArrayAccess
{
    /** Data. */
    protected array $data = [];

    /** Encoding. */
    protected string|null $encoding = 'UTF-8';

    /**
     * Constructor.
     *
     * @param string|int|array<string> $data
     * @param string|null              $encoding
     */
    public function __construct(string|int|array $data = '', string|null $encoding = '')
    {
        // Allow null (for internal encoding).
        if ($encoding !== '') $this->encoding = $encoding;

        if ($data !== '' && $data !== 0 && $data !== []) {
            $this->data = match (get_type($data)) {
                'string' => mb_str_split($data, 1, $this->encoding),
                'int'    => array_fill(0, $data, ''),
                'array'  => array_map('strval', $data),
            };
        }
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
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Get encoding.
     *
     * @return string|null
     */
    public function encoding(): string|null
    {
        return $this->encoding;
    }

    /**
     * Add some data into buffer data.
     *
     * @param  string $data
     * @return self
     */
    public function add(string $data): self
    {
        array_push($this->data, ...mb_str_split($data, 1, $this->encoding));

        return $this;
    }

    /**
     * Append more data into buffer data.
     *
     * @param  string ...$datas
     * @return self
     */
    public function append(string ...$datas): self
    {
        foreach ($datas as $data) {
            array_push($this->data, ...mb_str_split($data, 1, $this->encoding));
        }

        return $this;
    }

    /**
     * Prepend more data into buffer data.
     *
     * @param  string ...$datas
     * @return self
     */
    public function prepend(string ...$datas): self
    {
        foreach ($datas as $data) {
            array_unshift($this->data, ...mb_str_split($data, 1, $this->encoding));
        }

        return $this;
    }

    /**
     * Insert more data into buffer data.
     *
     * @param  int    $index
     * @param  string $data
     * @return self
     * @throws ArgumentError
     */
    public function insert(int $index, string $data): self
    {
        if ($index < 0 || $index > $this->getLength()) {
            throw new ArgumentError('Argument $index cannot be negative, '.
                'greater than length ' . $this->getLength());
        }

        $left  = array_slice($this->data, 0, $index);
        $right = array_slice($this->data, $index, null);

        array_push($left, ...mb_str_split($data, 1, $this->encoding));

        $this->data = array_merge($left, $right);

        return $this;
    }

    /**
     * Delete some data by given start/end arguments.
     *
     * @param  int $start
     * @param  int $end
     * @return self
     * @throws ArgumentError
     */
    public function delete(int $start, int $end = 1): self
    {
        if ($start < 0 || $start > $end || $start > $this->getLength()) {
            throw new ArgumentError('Argument $start cannot be negative, '.
                'greater than $end or length ' . $this->getLength());
        }

        array_splice($this->data, $start, $end);

        return $this;
    }

    /**
     * Replace given data within start/end arguments.
     *
     * @param  int    $start
     * @param  int    $end
     * @param  string $data
     * @return self
     * @throws ArgumentError
     */
    public function replace(int $start, int $end, string $data): self
    {
        if ($start < 0 || $start > $end || $start > $this->getLength()) {
            throw new ArgumentError('Argument $start cannot be negative, '.
                'greater than $end or length ' . $this->getLength());
        }

        if ($end + $start > $length = $this->getLength()) {
            $end = $end - $start;
        } else {
            $end = $end - $length;
            if ($end === 0) {
                $end = $length;
            }
        }

        array_splice($this->data, $start, $end, mb_str_split($data, 1, $this->encoding));

        return $this;
    }

    /**
     * Reverse buffer data.
     *
     * @return self
     */
    public function reverse(): self
    {
        $this->data = array_reverse($this->data);

        return $this;
    }

    /**
     * Slice buffer data by given start/end arguments and return a static instance.
     *
     * @param  int      $start
     * @param  int|null $length
     * @return static
     */
    public function slice(int $start, int $length = null): self
    {
        $this->data = array_slice($this->data, $start, $length);

        return $this;
    }

    /**
     * Splice buffer data by given start/end arguments.
     *
     * @param  int                $start
     * @param  int|null           $end
     * @param  string|array|null  $replace
     * @param  string|array|null &$replaced
     * @return self
     */
    public function splice(int $start, int $end = null, string|array $replace = null, string|array &$replaced = null): self
    {
        if ($replace !== null) {
            $replace = array_map('strval', is_array($replace) ? $replace : [$replace]);

            $temp = [];
            foreach ($replace as $data) {
                $temp = [...$temp, ...mb_str_split($data, 1, $this->encoding)];
            }

            // Swap & free.
            [$replace, $temp] = [$temp, null];

            if ($end !== null && $end !== count($replace)) {
                $replace = array_pad($replace, $end, '');
            }

            // Drop exceeding replace stuff. @nope
            // $replace = array_slice($replace, 0, $end);
        }

        $replaced = array_splice($this->data, $start, $end, $replace);

        return $this;
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

        if ($length === 0) {
            $this->data = [];
        } elseif ($length < $this->getLength()) {
            $this->data = array_slice($this->data, 0, $length);
        } else {
            $data = array_fill(0, $length - $this->getLength(), '');
            array_push($this->data, ...$data);
        }

        return $this;
    }

    /**
     * Get length.
     *
     * @return int
     */
    public function getLength(): int
    {
        return count($this->data);
    }

    /**
     * Get a char or return null.
     *
     * @param  int $index
     * @return string|null
     */
    public function char(int $index): string|null
    {
        // Catch nagatives.
        if ($index < 0) {
            $index += $this->getLength();
        }

        return $this->data[$index] ?? null;
    }

    /**
     * Get a char or return null (alias for char()).
     *
     * @param  int $index
     * @return string|null
     */
    public function charAt(int $index): string|null
    {
        return $this->char($index);
    }

    /**
     * Get a char code or return null.
     *
     * @param  int $index
     * @return int|null
     */
    public function charCodeAt(int $index): int|null
    {
        return Strings::ord($this->char($index) ?? '');
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
     * Get a code point or return null.
     *
     * @param  int  $index
     * @param  bool $hex
     * @return int|string|null
     */
    public function codePointAt(int $index, bool $hex = false): int|string|null
    {
        return XString::fromChars($this->data, $this->encoding)->codePointAt($index, $hex);
    }

    /**
     * Get a char at given index or return "".
     *
     * @param  int $index
     * @return string
     */
    public function getCharAt(int $index): string
    {
        if ($index < 0 || $index > $this->getLength()) {
            return '';
        }

        return $this->data[$index];
    }

    /**
     * Put a char at given index.
     *
     * @param  int    $index
     * @param  string $char
     * @return self
     */
    public function setCharAt(int $index, string $char): self
    {
        if ($index < 0 || $index > $this->getLength()) {
            return $this;
        }

        // Taking 1 char, so setting single index.
        $this->data[$index] = mb_substr($char, 0, 1, $this->encoding);

        return $this;
    }

    /**
     * Drop given index.
     *
     * @param  int $index
     * @return self
     */
    public function deleteCharAt(int $index): self
    {
        if ($index < 0 || $index > $this->getLength()) {
            return $this;
        }

        array_splice($this->data, $index, 1);

        return $this;
    }

    /**
     * Get all or some chars by given indexes.
     *
     * @param  int ...$indexes
     * @return array<string>
     */
    public function chars(int ...$indexes): array
    {
        if (!$indexes) {
            return $this->data;
        }

        return array_select($this->data, $indexes, array_pad([], count($indexes), null));
    }

    /**
     * Get all or some char codes by given indexes.
     *
     * @param  int ...$indexes
     * @return array<int>
     */
    public function charCodes(int ...$indexes): array
    {
        return XString::fromChars($this->chars(...$indexes), $this->encoding)->toCharCodes();
    }

    /**
     * Get all or some code points by given indexes.
     *
     * @param  int ...$indexes
     * @return array<int>
     */
    public function codePoints(int ...$indexes): array
    {
        return XString::fromChars($this->chars(...$indexes), $this->encoding)->toCodePoints();
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
        return Strings::compare((string) $this, (string) $data, $icase, $length, $this->encoding);
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
        return Strings::compareLocale((string) $this, (string) $data, $locale);
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
        return XString::fromChars($this->data, $this->encoding)->equals($data, $icase);
    }

    /**
     * Includes checker.
     *
     * @param  self|string|array<self|string> $search
     * @return bool
     */
    public function includes(self|string|array $search): bool
    {
        return XString::fromChars($this->data, $this->encoding)->includes($search);
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
        return XString::fromChars($this->data, $this->encoding)->contains($search, $icase);
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
        return XString::fromChars($this->data, $this->encoding)->startsWith($search, $icase);
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
        return XString::fromChars($this->data, $this->encoding)->endsWith($search, $icase);
    }

    /**
     * Get a subsequence from data buffer by given start/end arguments.
     *
     * @param  int      $start
     * @param  int|null $end
     * @return array
     */
    public function subseq(int $start, int $end = null): array
    {
        return array_slice($this->data, $start, $end);
    }

    /**
     * Get a substring from data buffer by given start/end arguments.
     *
     * @param  int      $start
     * @param  int|null $end
     * @return string
     */
    public function substr(int $start, int $end = null): string
    {
        return join(array_slice($this->data, $start, $end));
    }

    /**
     * Get index of given search.
     *
     * @param  self|string $search
     * @param  bool        $icase
     * @param  int         $offset
     * @return int|null
     */
    public function indexOf(self|string $search, bool $icase = false, int $offset = 0): int|null
    {
        return XString::fromChars($this->data, $this->encoding)->indexOf((string) $search, $icase, $offset);
    }

    /**
     * Get last index of given search.
     *
     * @param  self|string $search
     * @param  bool        $icase
     * @param  int         $offset
     * @return int|null
     */
    public function lastIndexOf(self|string $search, bool $icase = false, int $offset = 0): int|null
    {
        return XString::fromChars($this->data, $this->encoding)->lastIndexOf((string) $search, $icase, $offset);
    }

    /**
     * Trim.
     *
     * @param  string $characters
     * @return self
     */
    public function trim(string $characters = " \n\r\t\v\0"): self
    {
        return $this->trimLeft($characters)->trimRight($characters);
    }

    /**
     * Trim left.
     *
     * @param  string $characters
     * @return self
     */
    public function trimLeft(string $characters = " \n\r\t\v\0"): self
    {
        // @cancel: Below is more performant.
        // $this->data = XString::from($this->toString())->trimLeft()->toArray();

        $trimmed = null;
        for ($i = 0, $il = count($this->data); $i < $il; $i++) {
            if (!str_contains($characters, $this->data[$i])) {
                break;
            }

            unset($this->data[$i]);
            $trimmed = true;
        }

        // Reset indexes if trimmed.
        $trimmed && $this->data = array_list($this->data);

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
        // @cancel: Below is more performant.
        // $this->data = XString::from($this->toString())->trimRight()->toArray();

        $trimmed = null;
        for ($i = count($this->data) - 1; $i > -1; $i--) {
            if (!str_contains($characters, $this->data[$i])) {
                break;
            }

            unset($this->data[$i]);
            $trimmed = true;
        }

        // Reset indexes if trimmed.
        $trimmed && $this->data = array_list($this->data);

        return $this;
    }

    /**
     * Empty buffer data.
     *
     * @return void
     */
    public function empty(): void
    {
        $this->data = [];
    }

    /**
     * Check whether buffer data is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Join.
     *
     * @param  string $glue
     * @return string
     */
    public function join(string $glue = ''): string
    {
        return join($glue, $this->data);
    }

    /**
     * X-join.
     *
     * @param  string $glue
     * @return XString
     */
    public function xjoin(string $glue = ''): XString
    {
        return new XString($this->join($glue));
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->join();
    }

    /**
     * @return XString
     */
    public function toXString(): XString
    {
        return new XString($this->join(), $this->encoding);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return XArray
     */
    public function toXArray(): XArray
    {
        return new XArray($this->data);
    }

    /**
     * Each.
     *
     * @param  callable $func
     * @return void
     */
    public function each(callable $func): void
    {
        each($this->data, $func);
    }

    /**
     * Sort.
     *
     * @param  callable|int|null $func
     * @param  int               $flags
     * @param  bool              $key
     * @return self
     */
    public function sort(callable|int $func = null, int $flags = 0, bool $key = false): self
    {
        $this->data = sorted($this->data, $func, $flags, false, $key);

        return $this;
    }

    /**
     * Filter.
     *
     * @param  callable|null $func
     * @return self
     */
    public function filter(callable $func = null): self
    {
        $this->data = filter($this->data, $func, keep_keys: false);

        return $this;
    }

    /**
     * Map.
     *
     * @param  callable $func
     * @return self
     */
    public function map(callable $func): self
    {
        $this->data = map($this->data, $func);

        return $this;
    }

    /**
     * Reduce.
     *
     * @param  mixed    $carry
     * @param  callable $func
     * @param  bool     $right
     * @return mixed
     */
    public function reduce(mixed $carry, callable $func, bool $right = false): mixed
    {
        return reduce($this->data, $carry, $func, $right);
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
            yield $i => $this->data[$i];
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
     * @param  string|int|array<string> $data
     * @param  string|null              $encoding
     * @return static
     */
    public static function from(string|int|array $data, string|null $encoding = ''): static
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
        return new static(str_split(random_string($length, $puncted)), 'ascii');
    }

    /**
     * Create an instance from random bytes.
     *
     * @param  int $length
     * @return static
     */
    public static function fromRandomBytes(int $length): static
    {
        return new static(str_split(random_bytes($length)), 'ascii');
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
        return new static($chars, $encoding);
    }

    /**
     * Create an instance from given char codes.
     *
     * @param  int ...$codes
     * @return static
     */
    public static function fromCharCodes(int ...$codes): static
    {
        return new static(array_map(fn(int $code): ?string => Strings::chr($code), $codes));
    }
}
