<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\util\Numbers;

/**
 * A simple class, holds a random float as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomFloat
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomFloat implements \Stringable, \IteratorAggregate
{
    /** Data holder. */
    public readonly float $data;

    /**
     * Constructor.
     *
     * @param float|null $min
     * @param float|null $max
     * @param int|null   $precision
     */
    public function __construct(float $min = null, float $max = null, int $precision = null)
    {
        $this->data = Numbers::randomFloat($min, $max, $precision);
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return $this->format(tsep: '');
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
     * Get data as float.
     *
     * @return float
     */
    public function toFloat(): float
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
        return array_map(fn($x) => $x === '.' ? $x : intval($x), $this->toArray());
    }

    /**
     * Get data as float array.
     *
     * @return array
     */
    public function toFloatArray(): array
    {
        return array_map(fn($x) => $x === '.' ? $x : floatval($x), $this->toArray());
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
            $tmp = explode('.', $ret);
            $ret = implode('.', [
                convert_base($tmp[0], 10, $base),
                convert_base($tmp[1], 10, $base)
            ]);
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
     * @param  int|true    $decs
     * @param  string|null $dsep
     * @param  string|null $tsep
     * @return string
     */
    public function format(int|true $decs = true, string $dsep = null, string $tsep = null): string
    {
        return format_number($this->data, $decs, decimal_separator: $dsep, thousand_separator: $tsep);
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
     * @param  float $data
     * @return static
     */
    public static function from(float $data): static
    {
        $that = (new \XReflectionClass(static::class))->init();
        $that->data = $data;

        return $that;
    }
}
