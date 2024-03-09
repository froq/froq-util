<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\common\interface\Stringable;
use froq\util\Strings;

/**
 * A simple class, holds a random string as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomString
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomString extends AbstractRandomString implements Stringable
{
    /**
     * @override
     */
    public function __construct(int $length, bool $puncted = false)
    {
        $data = Strings::random($length, $puncted);

        parent::__construct($data);
    }

    /**
     * @override
     */
    public function toString(int $base = null): string
    {
        if ($base !== null) {
            $data = $this->data;

            $data = bin2hex($data);

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
     * Get data as hash string.
     *
     * @param  string $algo
     * @return string
     */
    public function toHashString(string $algo = 'md5'): string
    {
        return hash($algo, $this->data);
    }

    /**
     * @override
     */
    protected function passData(string $data): void
    {
        if ($data === '') {
            throw new \ArgumentError('Empty data given');
        }

        parent::__construct($data);
    }
}
