<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

/**
 * Base ramdom string class.
 *
 * @package froq\util\random
 * @class   froq\util\random\AbstractRandomString
 * @author  Kerem Güneş
 * @since   7.15
 */
abstract class AbstractRandomString extends AbstractRandom implements \Stringable
{
    /**
     * @override
     */
    public function __construct(string $data)
    {
        parent::__construct($data);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function length(): int
    {
        return strlen($this->data);
    }

    /**
     * Static initializer.
     *
     * @param  string $data
     * @return static
     */
    public static function from(string $data): static
    {
        $that = reflect(static::class)->init();
        $that->passData($data);

        return $that;
    }

    /**
     * Internal data setter for subclasses.
     *
     * @param  string $data
     * @return void
     * @throws froq\util\random\RandomException
     */
    abstract protected function passData(string $data): void;
}
