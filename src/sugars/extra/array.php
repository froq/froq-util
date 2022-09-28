<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Zip given arrays.
 *
 * @param  array ...$arrays
 * @return array
 * @since  6.0
 */
function array_zip(array ...$arrays): array
{
    return array_map(null, ...$arrays);
}

/**
 * Count given value repeats with strict mode as default.
 *
 * @param  array $array
 * @param  mixed $value
 * @param  bool  $strict
 * @return int
 * @since  6.0
 */
function array_value_count(array $array, mixed $value, bool $strict = true): int
{
    return array_values_count_all($array, $strict, false, [$value])[0]['count'];
}

/**
 * Count given values repeats with strict mode as default.
 *
 * @param  array $array
 * @param  array $values
 * @param  bool  $strict
 * @param  bool  $add_keys
 * @return array
 * @since  6.0
 */
function array_values_count(array $array, array $values, bool $strict = true, bool $add_keys = false): array
{
    return array_values_count_all($array, $strict, $add_keys, $values);
}

/**
 * Count each value repeats with strict mode as default.
 *
 * @param  array      $array
 * @param  bool       $strict
 * @param  bool       $add_keys
 * @param  array|null $_values @internal
 * @return array
 * @since  6.0
 */
function array_values_count_all(array $array, bool $strict = true, bool $add_keys = false, array $_values = null): array
{
    $ret = [];

    // Reduce O(n) stuff below.
    $values = $_values ?? array_dedupe($array, $strict);

    foreach ($values as $value) {
        $keys = array_keys($array, $value, $strict);

        $ret[] = $add_keys
            ? ['count' => count($keys), 'value' => $value, 'keys' => $keys]
            : ['count' => count($keys), 'value' => $value];
    }

    return $ret;
}
