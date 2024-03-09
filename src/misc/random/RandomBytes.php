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
 * @class   froq\util\random\RandomBytes
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomBytes extends RandomString
{
    /**
     * @override
     */
    public function __construct(int $length)
    {
        parent::passData(random_bytes($length));
    }
}
