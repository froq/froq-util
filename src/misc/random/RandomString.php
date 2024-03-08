<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\util\Strings;

/**
 * A simple class, holds a random string as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomString
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomString implements \Stringable, \IteratorAggregate
{
    /** Data holder. */
    public readonly string $data;

    /**
     * Constructor.
     *
     * @param int  $length
     * @param bool $puncted
     */
    public function __construct(int $length, bool $puncted = false)
    {
        $this->data = Strings::random($length, $puncted);
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
     * Get data as ord array.
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
            $data = $this->data;

            if (!$this instanceof RandomHash) {
                $data = bin2hex($data);
            }

            if ($base === 16) {
                return $data;
            }

            return convert_base($data, 16, $base);
        }

        return $this->data;
    }

    /**
     * Get data as hash string.
     *
     * @return string
     */
    public function toHashString(string $algo = 'md5'): string
    {
        if ($this instanceof RandomHash) {
            return $this->data;
        }

        return hash($algo, $this->data);
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
     * Static initializer.
     *
     * @param  string $data
     * @return static
     */
    public static function from(string $data): static
    {
        $that = (new \XReflectionClass(static::class))->init();
        $that->data = $data;

        return $that;
    }
}
