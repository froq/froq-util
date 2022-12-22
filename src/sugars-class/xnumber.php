<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\common\interface\{Intable, Floatable, Numberable};
use froq\util\Numbers;

/**
 * A class for playing with numbers in OOP-way.
 *
 * @package global
 * @class   XNumber
 * @author  Kerem Güneş
 * @since   6.0
 */
class XNumber implements Intable, Floatable, Numberable, Stringable
{
    /** Constants. */
    public final const PRECISION = PRECISION,
                       EPSILON   = PHP_FLOAT_EPSILON,
                       MAX_INT   = PHP_INT_MAX,
                       MAX_FLOAT = PHP_FLOAT_MAX;

    /** Data. */
    protected int|float $data;

    /** Precision. */
    protected int|true $precision;

    /**
     * Constructor.
     *
     * @param int|float|string $data
     * @param int|true         $precision
     */
    public function __construct(int|float|string $data, int|true $precision = true)
    {
        if (is_string($data)) {
            $data = Numbers::convert($data, $precision);
        }
        $this->data      = $data;
        $this->precision = $precision;
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * Get data.
     *
     * @return int|float
     */
    public function data(): int|float
    {
        return $this->data;
    }

    /**
     * Get precision.
     *
     * @return int|float|null
     */
    public function precision(): int|float|null
    {
        return $this->precision;
    }

    /**
     * Format.
     *
     * @param  int|true    $decimals
     * @param  string|null $decimalSeparator
     * @param  string|null $thousandSeparator
     * @return string
     */
    public function format(int|true $decimals = null, string $decimalSeparator = null, string $thousandSeparator = null): string
    {
        return format_number($this->data, $decimals ?? $this->precision, $decimalSeparator, $thousandSeparator);
    }

    /**
     * Addition.
     *
     * @param  int|float|string|self $data
     * @return self
     */
    public function add(int|float|string|self $data): self
    {
        $this->data += $this->prepare($data);

        return $this;
    }

    /**
     * Subtraction.
     *
     * @param  int|float|string|self $data
     * @return self
     */
    public function sub(int|float|string|self $data): self
    {
        $this->data -= $this->prepare($data);

        return $this;
    }

    /**
     * Multiplication.
     *
     * @param  int|float|string|self $data
     * @return self
     */
    public function mul(int|float|string|self $data): self
    {
        $this->data *= $this->prepare($data);

        return $this;
    }

    /**
     * Division.
     *
     * @param  int|float|string|self $data
     * @return self
     */
    public function div(int|float|string|self $data): self
    {
        try {
            $this->data /= $this->prepare($data);
        } catch (DivisionByZeroError) {
            $this->data = $this->data ? INF : NAN;
        }

        return $this;
    }

    /**
     * Int division.
     *
     * @param  int|float|string|self $data
     * @return self
     */
    public function intDiv(int|float|string|self $data): self
    {
        $this->data = (int) $this->div($data)->data;

        return $this;
    }

    /**
     * Float division.
     *
     * @param  int|float|string|self $data
     * @return self
     */
    public function floatDiv(int|float|string|self $data): self
    {
        $this->data = fdiv($this->data, $this->prepare($data));

        return $this;
    }

    /**
     * Modulo.
     *
     * @param  int|float|string|self $data
     * @return self
     */
    public function mod(int|float|string|self $data): self
    {
        try {
            $this->data %= $this->prepare($data);
        } catch (DivisionByZeroError) {
            $this->data = NAN;
        }

        return $this;
    }


    /**
     * Float modulo.
     *
     * @param  int|float|string|self $data
     * @return self
     */
    public function floatMod(int|float|string|self $data): self
    {
        $this->data = fmod($this->data, $this->prepare($data));

        return $this;
    }

    /**
     * Calculates: What is the % B (share) of A (this.data)?
     *
     * @param  int|float $share
     * @param  int       $precision
     * @return self
     */
    public function percent(int|float $share, int $precision = PRECISION): self
    {
        // @tome: A'nın % B'si kaçtır? https://hesaptablosu.net/yuzde-hesaplama/
        $data = round($this->data / 100 * abs($share), $precision);

        // Normalize non-floats.
        if ($data === round($data)) {
            $data = (int) $data;
        }

        $this->data = $data;

        return $this;
    }

    /**
     * Calculates: What is the % of A (this.data) of B (share)?
     *
     * @param  int|float $share
     * @param  int       $precision
     * @return self
     */
    public function percentOf(int|float $share, int $precision = PRECISION): self
    {
        // @tome: A B'nin % kaçıdır? https://hesaptablosu.net/yuzde-hesaplama/
        $data = round($this->data * 100 / abs($share), $precision);

        // Normalize non-floats.
        if ($data === round($data)) {
            $data = (int) $data;
        }

        $this->data = $data;

        return $this;
    }

    /**
     * Calculates: What is the difference ratio (%) from A (this.data) to B (share)?
     *
     * @param  int|float $share
     * @param  int       $precision
     * @return self
     */
    public function percentRateOf(int|float $share, int $precision = PRECISION): self
    {
        // @tome: A'dan B'ye fark oranı (%) nedir? https://hesaptablosu.net/yuzde-hesaplama/
        $data = round((abs($share) - $this->data) * 100 / $this->data, $precision);

        // Normalize non-floats.
        if ($data === round($data)) {
            $data = (int) $data;
        }

        $this->data = $data;

        return $this;
    }

    /**
     * Sign data.
     *
     * @return self
     */
    public function sign(): self
    {
        $this->data = ($this->data > 0) ? -$this->data : $this->data;

        return $this;
    }

    /**
     * Unsign data.
     *
     * @return self
     */
    public function unsign(): self
    {
        $this->data = ($this->data > 0) ? $this->data : -$this->data;

        return $this;
    }

    /**
     * Absolute value.
     *
     * @return self
     */
    public function abs(): self
    {
        $this->data = abs($this->data);

        return $this;
    }

    /**
     * Exponent value.
     *
     * @return self
     */
    public function exp(): self
    {
        $this->data = exp($this->data);

        return $this;
    }

    /**
     * Power value.
     *
     * @param  int $exponent
     * @return self
     */
    public function pow(int $exponent): self
    {
        $this->data = pow($this->data, $exponent);

        return $this;
    }

    /**
     * Ceil.
     *
     * @return self
     */
    public function ceil(): self
    {
        $this->data = ceil($this->data);

        return $this;
    }

    /**
     * Floor.
     *
     * @return self
     */
    public function floor(): self
    {
        $this->data = floor($this->data);

        return $this;
    }

    /**
     * Round.
     *
     * @param  int $precision
     * @param  int $mode
     * @return self
     */
    public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP): self
    {
        $this->data = round($this->data, $precision, $mode);

        return $this;
    }

    /**
     * Int checker.
     *
     * @return bool
     */
    public function isInt(): bool
    {
        return is_int($this->data);
    }

    /**
     * Float checker.
     *
     * @return bool
     */
    public function isFloat(): bool
    {
        return is_float($this->data);
    }

    /**
     * signed checker.
     *
     * @return bool
     */
    public function isSigned(): bool
    {
        return $this->data < 0;
    }

    /**
     * Unsigned checker.
     *
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->data >= 0;
    }

    /**
     * NAN checker.
     *
     * @return bool
     */
    public function isNan(): bool
    {
        return is_nan($this->data);
    }

    /**
     * Finite checker.
     *
     * @return bool
     */
    public function isFinite(): bool
    {
        return is_finite($this->data);
    }

    /**
     * Infinite checker.
     *
     * @return bool
     */
    public function isInfinite(): bool
    {
        return is_infinite($this->data);
    }

    /**
     * Validity checker.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !$this->isNan() && !$this->isInfinite();
    }

    /**
     * Equal checker.
     *
     * @param  int|float|string|self $data
     * @param  int|null              $precision
     * @return bool
     * @since  6.6
     */
    public function isEqual(int|float|string|self $data, int $precision = null): bool
    {
        return Numbers::equals($this->data, $this->prepare($data, $precision), $precision);
    }

    /**
     * Between checker.
     *
     * @param  int|float|string|self $min
     * @param  int|float|string|self $max
     * @return bool
     * @since  6.6
     */
    public function isBetween(int|float|string|self $min, int|float|string|self $max): bool
    {
        return $this->data >= $this->prepare($min) && $this->data <= $this->prepare($max);
    }

    /**
     * Less-than checker.
     *
     * @param  int|float|string|self $data
     * @return bool
     * @since  6.6
     */
    public function isLessThan(int|float|string|self $data): bool
    {
        return $this->data < $this->prepare($data);
    }

    /**
     * Less-than-equal checker.
     *
     * @param  int|float|string|self $data
     * @return bool
     * @since  6.6
     */
    public function isLessThanEqual(int|float|string|self $data): bool
    {
        return $this->data <= $this->prepare($data);
    }

    /**
     * Greater-than checker.
     *
     * @param  int|float|string|self $data
     * @return bool
     * @since  6.6
     */
    public function isGreaterThan(int|float|string|self $data): bool
    {
        return $this->data > $this->prepare($data);
    }

    /**
     * Greater-than-equal checker.
     *
     * @param  int|float|string|self $data
     * @return bool
     * @since  6.6
     */
    public function isGreaterThanEqual(int|float|string|self $data): bool
    {
        return $this->data >= $this->prepare($data);
    }

    /**
     * @inheritDoc froq\common\interface\Intable
     */
    public function toInt(): int
    {
        return $this->isValid() ? (int) $this->data : 0;
    }

    /**
     * @inheritDoc froq\common\interface\Floatable
     */
    public function toFloat(): float
    {
        return $this->isValid() ? (float) $this->data : 0.0;
    }

    /**
     * @inheritDoc froq\common\interface\Numberable
     */
    public function toNumber(): int|float
    {
        return $this->data;
    }

    /**
     * Bin converter.
     *
     * @return string|null
     */
    public function toBin(): string|null
    {
        return $this->isInt() ? decbin($this->data) : null;
    }

    /**
     * Hex converter.
     *
     * @return string|null
     */
    public function toHex(): string|null
    {
        return $this->isInt() ? dechex($this->data) : null;
    }

    /**
     * Oct converter.
     *
     * @return string|null
     */
    public function toOct(): string|null
    {
        return $this->isInt() ? decoct($this->data) : null;
    }

    /**
     * Base converter.
     *
     * @param  int $base
     * @return string|null
     */
    public function toBase(int $base): string|null
    {
        return $this->isInt() ? convert_base($this->data, 10, $base) : null;
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
     * Static constructor.
     *
     * @param  int|float|string $data
     * @param  bool|null        $precision
     * @return static
     */
    public static function from(int|float|string $data, int|bool $precision = null): static
    {
        return new static($data, $precision);
    }

    /**
     * Static constructor from a random number.
     *
     * @param  int|float|null $min
     * @param  int|float|null $max
     * @param  int|null       $precision
     * @return static
     */
    public static function fromRandom(int|float $min = null, int|float $max = null, int $precision = null): static
    {
        return new static(Numbers::random($min, $max, $precision), $precision);
    }

    /**
     * Prepare given data for some methods.
     */
    private function prepare(int|float|string|self $data, int $precision = null): int|float
    {
        if (is_string($data)) {
            return Numbers::convert($data, $precision ?? $this->precision);
        }
        if ($data instanceof self) {
            return $data->data;
        }
        return $data;
    }
}

/**
 * XNumber initializer.
 *
 * @param  int|float|string $data
 * @param  int|true         $precision
 * @return XNumber
 */
function xnumber(int|float|string $data = 0, int|true $precision = true): XNumber
{
    return new XNumber($data, $precision);
}
