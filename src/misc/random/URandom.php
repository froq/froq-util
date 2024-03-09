<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\random;

use froq\util\System;

/**
 * A simple class that reads random bytes from `/dev/urandom` device, holds as its data and
 * provides some utility methods.
 * @see https://unix.stackexchange.com/questions/324209/when-to-use-dev-random-vs-dev-urandom
 *
 * @package froq\util\random
 * @class   froq\util\random\URandom
 * @author  Kerem Güneş
 * @since   7.15
 */
class URandom extends RandomString
{
    /**
     * @override
     */
    public function __construct(int $length)
    {
        parent::passData(self::readBytes($length));
    }

    /**
     * Read.
     *
     * @param  int  $length
     * @param  bool $hex
     * @return string|null
     * @causes Error
     */
    public static function read(int $length, bool $hex = false): string|null
    {
        $ret = System::urandom($length);

        if ($hex && $ret !== null) {
            $ret = bin2hex($ret);
        }

        return $ret;
    }

    /**
     * Read array.
     *
     * @param  int  $length
     * @param  bool $hex
     * @param  bool $ord
     * @return array|null
     * @causes Error
     */
    public static function readArray(int $length, bool $hex = false, bool $ord = false): array|null
    {
        $ret = self::read($length);

        if ($ret !== null) {
            $ret = str_split($ret, 1);
            if ($hex) {
                $ret = array_map('bin2hex', $ret);
            } elseif ($ord) {
                $ret = array_map('ord', $ret);
            }
        }

        return $ret;
    }

    /**
     * Read bytes (safe-return).
     *
     * @param  int  $length
     * @param  bool $hex
     * @return string|null
     * @causes Error
     */
    public static function readBytes(int $length, bool $hex = false): string
    {
        return (string) self::read($length, $hex);
    }

    /**
     * Read bytes array (safe-return).
     *
     * @param  int  $length
     * @param  bool $hex
     * @param  bool $ord
     * @return array
     * @causes Error
     */
    public static function readBytesArray(int $length, bool $hex = false, bool $ord = true): array
    {
        return (array) self::readArray($length, $hex, $ord);
    }
}
