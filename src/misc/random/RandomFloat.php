<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\common\interface\Stringable;
use froq\util\Numbers;

/**
 * A simple class, holds a random float as its data & provides some utility methods.
 *
 * @package froq\util\random
 * @class   froq\util\random\RandomFloat
 * @author  Kerem Güneş
 * @since   7.15
 */
class RandomFloat extends RandomNumber implements Stringable
{
    /**
     * @override
     */
    public function __construct(float $min = null, float $max = null, int $precision = null)
    {
        $data = Numbers::randomFloat($min, $max, $precision);

        parent::passData($data);
    }
}
