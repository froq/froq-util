<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\common\interface\Lengthable;

/**
 * Base ramdom class.
 *
 * @package froq\util\random
 * @class   froq\util\random\AbstractRandom
 * @author  Kerem Güneş
 * @since   7.15
 */
abstract class AbstractRandom implements Lengthable
{
    /**
     * Constructor.
     *
     * @param mixed $data
     */
    public function __construct(
        public readonly mixed $data
    )
    {}
}
