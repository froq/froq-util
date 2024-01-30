<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\util\Numbers;

/**
 * A simple class, holds a random int as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomInt
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomInt implements \Stringable, \IteratorAggregate
{
    /** Data holder. */
    public readonly int $data;

    /**
     * Constructor.
     *
     * @param int|null $min
     * @param int|null $max
     */
    public function __construct(int $min = null, int $max = null)
    {
        $this->data = Numbers::randomInt($min, $max);
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return (string) $this->data;
    }

    /**
     * Get data length.
     *
     * @return int
     */
    public function length(): int
    {
        return strlen((string) $this);
    }

    /**
     * Get data as int.
     *
     * @return array
     */
    public function toInt(): int
    {
        return $this->data;
    }

    /**
     * Get data as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return str_split((string) $this);
    }

    /**
     * Get data as int array.
     *
     * @return array
     */
    public function toIntArray(): array
    {
        return array_map(fn($x) => intval($x), $this->toArray());
    }

    /**
     * Get data as float array.
     *
     * @return array
     */
    public function toFloatArray(): array
    {
        return array_map(fn($x) => floatval($x), $this->toArray());
    }

    /**
     * Get data as string.
     *
     * @param  int|null $base
     * @return string
     */
    public function toString(int $base = null): string
    {
        $ret = (string) $this;

        if ($base !== null) {
            $ret = convert_base($ret, 10, $base);
        }

        return $ret;
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
     * Format.
     *
     * @param  string|null $tsep
     * @return string
     */
    public function format(string $tsep = null): string
    {
        return format_number($this->data, thousand_separator: $tsep);
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
     * @param  int $data
     * @return static
     */
    public static function from(int $data): static
    {
        $that = (new \XReflectionClass(static::class))->init();
        $that->data = $data;

        return $that;
    }
}
