<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\util\System;

/**
 * A simple class that reads random bytes from `/dev/urandom` device, holds as its data and
 * provides some utility methods.
 * @see https://unix.stackexchange.com/questions/324209/when-to-use-dev-random-vs-dev-urandom
 *
 * @package froq\util\random
 * @class   froq\util\random\URandom
 * @author  Kerem Güneş
 * @since   7.15
 */
class URandom implements \Stringable, \IteratorAggregate
{
    /** Data holder (raw binary). */
    public readonly string $data;

    /**
     * Constructor.
     *
     * @param  int $length
     * @causes Error
     */
    public function __construct(int $length)
    {
        $this->data = self::readBytes($length);
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * Get data length.
     *
     * @return int
     */
    public function length(): int
    {
        return strlen($this->data);
    }

    /**
     * Get data as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return str_split($this->data);
    }

    /**
     * Get data as byte array.
     *
     * @return array
     */
    public function toOrdArray(): array
    {
        return array_map('ord', $this->toArray());
    }

    /**
     * Get data as string.
     *
     * @param  int|null $base
     * @return string
     */
    public function toString(int $base = null): string
    {
        if ($base !== null) {
            $data = bin2hex($this->data);

            if ($base === 16) {
                return $data;
            }

            return convert_base($data, 16, $base);
        }

        return $this->data;
    }

    /**
     * Get data as hex string.
     *
     * @return string
     */
    public function toHexString(): string
    {
        return $this->toString(16);
    }

    /**
     * Get data as URL (base62) string.
     *
     * @return string
     */
    public function toUrlString(): string
    {
        return $this->toString(62);
    }

    /**
     * Get data as digit string.
     *
     * @return string
     */
    public function toDigitString(): string
    {
        return $this->toString(10);
    }

    /**
     * @inheritDoc IteratorAggregate
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->toArray() as $i => $item) {
            yield $i => $item;
        }
    }

    /**
     * Read.
     *
     * @param  int $length
     * @return string|null
     * @causes Error
     */
    public static function read(int $length): string|null
    {
        return System::urandom($length);
    }

    /**
     * Read array.
     *
     * @param  int  $length
     * @param  bool $ord
     * @return array|null
     * @causes Error
     */
    public static function readArray(int $length, bool $ord = false): array|null
    {
        $ret = self::read($length);

        if ($ret !== null) {
            $ret = str_split($ret, 1);
            $ord && $ret = array_map('ord', $ret);
        }

        return $ret;
    }

    /**
     * Read bytes (safe-return).
     *
     * @param  int $length
     * @return string|null
     * @causes Error
     */
    public static function readBytes(int $length): string
    {
        return (string) self::read($length);
    }

    /**
     * Read bytes array (safe-return).
     *
     * @param  int  $length
     * @param  bool $ord
     * @return array|null
     * @causes Error
     */
    public static function readBytesArray(int $length, bool $ord = true): array
    {
        return (array) self::readArray($length, $ord);
    }
}
