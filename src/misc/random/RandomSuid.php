<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

/**
 * A simple class, holds a random bytes as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomSuid
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomSuid extends RandomString
{
    /**
     * @override
     */
    public readonly string $data;

    /**
     * @override
     */
    public function __construct(int $length, int $base = 62)
    {
        $data = (new Random)->nextChars($length, $base);

        $this->data = $data;
    }
}
