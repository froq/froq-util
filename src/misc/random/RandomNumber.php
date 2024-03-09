<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\common\interface\Stringable;
use froq\util\Numbers;

/**
 * A simple class, holds a random number as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomNumber
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomNumber extends AbstractRandomNumber implements Stringable
{
    /**
     * @override
     */
    public function __construct(int|float $min = null, int|float $max = null, int $precision = null)
    {
        $data = Numbers::random($min, $max, $precision);

        parent::__construct($data);
    }

    /**
     * @override
     */
    public function toString(int $base = null): string
    {
        $ret = (string) $this;

        if ($base !== null) {
            if (is_int($this->data)) {
                $ret = convert_base($ret, 10, $base);
            } else {
                $tmp = explode('.', $ret);
                $ret = implode('.', [
                    convert_base($tmp[0], 10, $base),
                    convert_base($tmp[1], 10, $base)
                ]);
            }
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
     * @override
     */
    public function format(mixed ...$args): string
    {
        if (is_int($this->data)) {
            $args['tsep'] ??= '';
        } else {
            $args['decs'] ??= true;
            $args['dsep'] ??= '.';
            $args['tsep'] ??= '';
        }

        return format_number($this->data, ...$args);
    }

    /**
     * @override
     */
    protected function passData(int|float $data): void
    {
        if ($this instanceof RandomInt) {
            is_int($data) || throw new \ArgumentError(
                'Invalid data type float for %s', $this::class
            );
        } elseif ($this instanceof RandomFloat) {
            is_float($data) || throw new \ArgumentError(
                'Invalid data type int for %s', $this::class
            );
        }

        parent::__construct($data);
    }
}
