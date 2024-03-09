<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\common\interface\Stringable;
use froq\util\Numbers;

/**
 * A simple class, holds a random int as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomInt
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomInt extends RandomNumber implements Stringable
{
    /**
     * @override
     */
    public function __construct(int $min = null, int $max = null)
    {
        $data = Numbers::randomInt($min, $max);

        parent::passData($data);
    }
}
