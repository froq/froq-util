<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

use froq\util\UtilException;
use froq\encrypting\Generator;

// Check dependencies.
if (!class_exists(Generator::class, true)) {
    throw new UtilException("Generate sugars dependent to 'froq\encrypting' module but not found");
}

/**
 * Generate salt.
 * @param  int $length
 * @param  int $bits_per_char
 * @return string
 * @since  4.25
 */
function generate_salt(int $length = 40, int $bits_per_char = 6): string
{
    return Generator::generateSalt($length, $bits_per_char);
}

/**
 * Generate nonce.
 * @param  int $length
 * @param  int $bits_per_char
 * @return string
 * @since  4.0, 4.1 Changed from rand_string(),rand_nonce().
 */
function generate_nonce(int $length = 16, int $bits_per_char = 4): string
{
    return Generator::generateNonce($length, $bits_per_char);
}

/**
 * Generate token.
 * @param  int $hash_length
 * @return string
 * @since  4.6
 */
function generate_token(int $hash_length = 32): string
{
    return Generator::generateToken($hash_length);
}

/**
 * Generate id.
 * @param  int  $length
 * @param  int  $base
 * @param  bool $dated
 * @return string
 * @since  4.0, 4.1 Changed from rand_id().
 */
function generate_id(int $length, int $base = 10, bool $dated = false): string
{
    return Generator::generateId($length, $base, $dated);
}

/**
 * Generate serial id.
 * @param  bool $dated
 * @return string
 * @since  4.1
 */
function generate_serial_id(bool $dated = false): string
{
    return Generator::generateSerialId($dated);
}

/**
 * Generate random id.
 * @param  int $length
 * @param  int $base
 * @return string
 * @since  4.4
 */
function generate_random_id(int $length, int $base = 16): string
{
    return Generator::generateRandomId($length, $base);
}

/**
 * Generate session id.
 * @param  array|null $options
 * @return string
 * @since  4.7
 */
function generate_session_id(array $options = null): string
{
    return Generator::generateSessionId($options);
}

/**
 * Generate oid.
 * @param  bool $counted
 * @return string
 * @since  4.0, 4.1 Changed from rand_oid().
 */
function generate_oid(bool $counted = true): string
{
    return Generator::generateObjectId($counted);
}

/**
 * Generate uuid.
 * @param  bool $dashed
 * @return string
 * @since  4.0, 4.1 Changed from rand_uuid().
 */
function generate_uuid(bool $dashed = true): string
{
    return Generator::generateUuid($dashed);
}

/**
 * Generate guid.
 * @param  bool $dashed
 * @return string
 * @since  4.0, 4.1 Changed from rand_guid().
 */
function generate_guid(bool $dashed = true): string
{
    return Generator::generateGuid($dashed);
}

/**
 * Generate password.
 * @param  int  $length
 * @param  bool $puncted
 * @return string
 * @since  4.25
 */
function generate_password(int $length, bool $puncted = false): string
{
    return Generator::generatePassword($length, $puncted);
}
