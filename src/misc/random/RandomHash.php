<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

/**
 * A simple class, holds a hashed random bytes as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomHash
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomHash extends RandomString
{
    /**
     * @override
     */
    public readonly string $data;

    /**
     * @override
     */
    public function __construct(int $length = 20, string $algo = 'md5', bool $upper = false)
    {
        $data = (new Random)->nextBytes($length, algo: $algo);

        if ($upper) {
            $data = strtoupper($data);
        }

        $this->data = $data;
    }
}
