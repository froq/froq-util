<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

/**
 * Base ramdom number class.
 *
 * @package froq\util\random
 * @class   froq\util\random\AbstractRandomNumber
 * @author  Kerem Güneş
 * @since   7.15
 */
abstract class AbstractRandomNumber extends AbstractRandom implements \Stringable
{
    /**
     * @override
     */
    public function __construct(int|float $data)
    {
        parent::__construct($data);
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return $this->format();
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
     * Static initializer.
     *
     * @param  int|float $data
     * @return static
     */
    public static function from(int|float $data): static
    {
        $that = reflect(static::class)->init();
        $that->passData($data);

        return $that;
    }

    /**
     * Template method for format.
     *
     * @return string
     */
    abstract public function format(mixed ...$args): string;

    /**
     * Internal data setter for subclasses.
     *
     * @param  int|float $data
     * @return void
     * @throws ArgumentError
     */
    abstract protected function passData(int|float $data): void;
}
