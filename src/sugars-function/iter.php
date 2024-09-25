<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * Convert an iterable to an array.
 *
 * @param  iterable      $iter
 * @param  int|null      $limit
 * @param  bool          $reverse
 * @param  callable|null $apply
 * @return array
 */
function iter_to_array(iterable $iter, int $limit = null, bool $reverse = false, callable $apply = null): array
{
    $items = [];

    $i = 0;
    foreach ($iter as $key => $item) {
        if ($limit && $i >= $limit) {
            break;
        }

        $items[$key] = $item;
        $i += 1;
    }

    $reverse && $items = array_reverse($items);
    $apply && $items = array_apply($items, $apply);

    return $items;
}

/**
 * Convert an iterable to an Iter.
 *
 * @param  iterable      $iter
 * @param  int|null      $limit
 * @param  bool          $reverse
 * @param  callable|null $apply
 * @return Iter
 */
function iter_to_iter(iterable $iter, int $limit = null, bool $reverse = false, callable $apply = null): Iter
{
    return new Iter(iter_to_array($iter, $limit, $reverse, $apply));
}

/**
 * Get first item of given iterable.
 *
 * @param  iterable      $iter
 * @param  callable|null $apply
 * @return mixed
 */
function iter_first(iterable $iter, callable $apply = null): mixed
{
    return first(iter_to_array($iter, limit: 1, apply: $apply));
}

/**
 * Get last item of given iterable.
 *
 * @param  iterable      $iter
 * @param  callable|null $apply
 * @return mixed
 */
function iter_last(iterable $iter, callable $apply = null): mixed
{
    return last(iter_to_array($iter, apply: $apply));
}
