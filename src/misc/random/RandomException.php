<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

/**
 * @package froq\util\random
 * @class   froq\util\random\RandomException
 * @author  Kerem Güneş
 * @since   5.0
 */
class RandomException extends \froq\common\Exception
{
    public static function forInvalidBound(int $bound): static
    {
        return new static('Invalid bound %s [min=1]', $bound);
    }

    public static function forInvalidLength(int $length): static
    {
        return new static('Invalid length %s [min=1]', $length);
    }

    public static function forInvalidBase(int $base): static
    {
        return new static('Invalid base %s [min=2, max=62]', $base);
    }

    public static function forEmptyChars(): static
    {
        return new static('Empty chars given');
    }
}
