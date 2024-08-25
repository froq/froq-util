<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
use froq\util\{Util, System, Arrays, Objects};

// Load base stuff.
require __DIR__ . '/sugars-class.php';
require __DIR__ . '/sugars-constant.php';
require __DIR__ . '/sugars-function.php';

/**
 * Sugar loader.
 */
function sugar(string ...$names): void
{
    Util::loadSugar(...$names);
}

/**
 * Yes man..
 */
function equal($a, $b, ...$c): bool
{
    return ($a == $b) || ($c && in_array($a, $c));
}
function equals($a, $b, ...$c): bool
{
    return ($a === $b) || ($c && in_array($a, $c, true));
}

/**
 * Quick array/object (with "x:1, y:2" notation).
 */
function qa(...$args): array
{
    // When arguments are named.
    if (!is_list($args)) {
        return $args;
    }

    $ret = [];
    if ($argc = count($args)) {
        for ($i = 1; $i < $argc + 1; $i += 2) {
            $ret[$args[$i - 1]] = $args[$i];
        }
    }
    return $ret;
}
function qo(...$args): object
{
    return (object) qa(...$args);
}

/**
 * Quick array/object export (with "x:$x, y:$y" notation).
 */
function qx(array|object $iter, mixed &...$vars): int
{
    return array_export((array) $iter, ...$vars);
}

/**
 * Unref given var(s), return their value(s).
 *
 * Example:
 * ```
 * $user->data = unref($data)     // $data => null
 * [$id, $name] = unref(...$data) // $data => [null, null]
 * unref($someTempData)           // $someTempData => null (why?)
 * ```
 *
 * @param  mixed &...$vars
 * @return mixed
 */
function unref(mixed &...$vars): mixed
{
    if (count($vars) === 1) {
        $ret = $vars[0];
        $vars[0] = null;
    } else {
        $ret = [];
        foreach ($vars as $i => &$var) {
            $ret[$i] = $var;
            $var = null;
        }
    }

    return $ret;
}

/**
 * Each wrapper for scoped function calls on given array or just for syntactic sugar.
 *
 * @param  array       $array
 * @param  callable    $func
 * @param  mixed    ...$funcArgs
 * @return void
 * @since  5.0
 */
function each(array $array, callable $func, mixed ...$funcArgs): void
{
    Arrays::each($array, $func, ...$funcArgs);
}

/**
 * Filter, with some options.
 *
 * @param  array                      $array
 * @param  callable|string|array|null $func
 * @param  bool                       $recursive
 * @param  bool                       $use_keys
 * @param  bool                       $keep_keys
 * @param  bool|null                  $list Alias of $keep_keys.
 * @param  bool                       $key  Filter keys directive.
 * @return array
 * @since  3.0, 5.0
 */
function filter(array $array, callable|string|array $func = null, bool $recursive = false, bool $use_keys = false, bool $keep_keys = true, bool $list = null, bool $key = false): array
{
    return $key // Filter keys.
         ? Arrays::filterKeys($array, $func, $recursive)
         : Arrays::filter($array, $func, $recursive, $use_keys, ($list ? false : $keep_keys));
}

/**
 * Map, with some options.
 *
 * @param  array                 $array
 * @param  callable|string|array $func
 * @param  bool                  $recursive
 * @param  bool                  $use_keys
 * @param  bool                  $keep_keys
 * @param  bool|null             $list Alias of $keep_keys.
 * @param  bool                  $key  Map keys directive.
 * @return array
 * @since  3.0, 5.0
 */
function map(array $array, callable|string|array $func, bool $recursive = false, bool $use_keys = false, bool $keep_keys = true,
    bool $list = null, bool $key = false): array
{
    return $key // Map keys.
         ? Arrays::mapKeys($array, $func, $recursive)
         : Arrays::map($array, $func, $recursive, $use_keys, ($list ? false : $keep_keys));
}

/**
 * Reduce, with right option.
 *
 * @param  array         $array
 * @param  mixed         $carry
 * @param  callable|null $func
 * @param  bool          $right Reduce right directive.
 * @param  bool          $key   Reduce keys directive.
 * @return mixed
 * @since  4.0, 5.0
 */
function reduce(array $array, mixed $carry, callable $func = null, bool $right = false, bool $key = false): mixed
{
    return $key // Reduce keys.
        ? Arrays::reduceKeys($array, $carry, $func, $right)
        : Arrays::reduce($array, $carry, $func, $right);
}

/**
 * Aggregate an array.
 *
 * @param  array      $array
 * @param  callable   $func
 * @param  array|null $carry
 * @return array
 * @since  7.44
 */
function aggregate(array $array, callable $func, array $carry = null): array
{
    return Arrays::aggregate($array, $func, $carry);
}

/**
 * Sort an array values without modifying input array, or keys only.
 *
 * @param  array             $array
 * @param  callable|int|null $func  Valid ints: 1 or -1.
 * @param  int               $flags
 * @param  bool|null         $assoc Associative directive.
 * @param  bool              $key   Sort by key directive.
 * @return array
 * @since  5.41
 */
function sorted(array $array, callable|int $func = null, int $flags = 0, bool $assoc = null, bool $key = false): array
{
    return $key // Sort keys.
         ? Arrays::sortKey($array, $func, $flags)
         : Arrays::sort($array, $func, $flags, $assoc);
}

/**
 * Get size (length/count).
 *
 * @param  mixed<string|countable|object|null> $var
 * @return int
 * @since  3.0, 5.0
 */
function size(mixed $var): int
{
    return Util::size($var);
}

/**
 * Pad an array or string.
 *
 * @param  array|string $input
 * @param  int          $length
 * @param  mixed|null   $pad
 * @return array|string
 * @since  5.0
 */
function pad(array|string $input, int $length, mixed $pad = null): array|string
{
    return is_array($input) ? array_pad($input, $length, $pad)
         : str_pad($input, $length, strval($pad ?? ' '));
}

/**
 * Chunk an array or string.
 *
 * @param  array|string $input
 * @param  int          $length
 * @param  bool         $keep_keys
 * @return array
 * @since  5.0
 */
function chunk(array|string $input, int $length, bool $keep_keys = false): array
{
    return is_array($input) ? array_chunk($input, $length, $keep_keys)
         : str_chunk($input, $length, join: false);
}

/**
 * Concat an array or string.
 *
 * @param  array|string    $input
 * @param  mixed        ...$inputs
 * @return array|string
 * @since  4.0, 5.0
 */
function concat(array|string $input, mixed ...$inputs): array|string
{
    return is_array($input) ? array_concat($input, ...$inputs)
         : str_concat($input, ...$inputs);
}

/**
 * Reverse.
 *
 * @param  array|string     $input
 * @param  bool|string|null $keep_keys_or_encoding
 * @return array|string
 * @since  7.12
 */
function reverse(array|string $input, bool|string $keep_keys_or_encoding = null): array|string
{
    return is_array($input) ? array_reverse($input, (bool) $keep_keys_or_encoding)
         : (func_num_args() > 1 ? str_reverse($input, $keep_keys_or_encoding) : str_reverse($input));
}

/**
 * Slice an array or string.
 *
 * @param  array|string $input
 * @param  int          $start
 * @param  int|null     $end
 * @param  bool         $keep_keys
 * @return array|string
 * @since  3.0, 4.0, 5.0
 */
function slice(array|string $input, int $start, int $end = null, bool $keep_keys = false): array|string
{
    return is_array($input) ? array_slice($input, $start, $end, $keep_keys)
         : str_slice($input, [$start, $end]);
}

/**
 * Splice an array or string.
 *
 * @param  array|string      $input
 * @param  int               $start
 * @param  int|null          $end
 * @param  array|string|null $replace
 * @param  array|string|null &$replaced
 * @return array|string
 * @since  6.0
 */
function splice(array|string $input, int $start, int $end = null, array|string $replace = null, array|string &$replaced = null): array|string
{
    if (is_array($input)) {
        $replaced = (func_num_args() > 3)
            ? array_splice($input, $start, $end, is_array($replace) ? $replace : [$replace])
            : array_splice($input, $start, $end);

        return $input; // Return modified input, not extracted part.
    }

    return str_splice($input, $start, $end, $replace, $replaced);
}

/**
 * Split a string, with unicode style.
 *
 * @param  string           $separator
 * @param  string           $input
 * @param  int|null         $limit
 * @param  int|null         $flags
 * @param  RegExpError|null &$error
 * @return array
 * @since  5.0
 */
function split(string $separator, string $input, int $limit = null, int $flags = null, RegExpError &$error = null): array
{
    if ($separator === '') {
        // Safe for binary strings.
        $ret = strlen($input) === mb_strlen($input)
             ? str_split($input) : mb_str_split($input);

        // Mind limit option.
        if ($limit && $limit > 0) {
            $res = array_slice($ret, $limit - 1); // Rest.
            $ret = array_slice($ret, 0, $limit - 1);
            $res && $ret[] = join($res);
        }
    } else {
        // Escape special char typos / null bytes and delimiter.
        $separator = strlen($separator) === 1 ? preg_quote($separator, '~')
            : str_replace(["\0", '~'], ['\0', '\~'], $separator);

        $ret = preg_split(
            '~'. $separator .'~u',
            $input,
            limit: ($limit ?? -1),
            flags: ($flags |= PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)
        ) ?: [];
    }

    // Block "undefined index .." error.
    if ($limit && $limit > count($ret)) {
        $ret = array_pad($ret, $limit, null);
    }

    // Fill error if wanted.
    if (func_num_args() === 5) {
        $message = preg_error_message($code, 'preg_split');
        $message && $error = new RegExpError($message, code: $code);
    }

    return $ret;
}

/**
 * Unsplit, a fun function.
 *
 * @param  string $separator
 * @param  array  $input
 * @return string
 * @since  3.0, 5.0
 */
function unsplit(string $separator, array $input): string
{
    return join($separator, $input);
}

/**
 * Stripper for strings and arrays.
 *
 * @param  mixed  $input
 * @param  string $characters
 * @return string|array
 * @since  3.0, 5.0
 */
function strip(mixed $input, string $characters = ''): string|array
{
    if (is_array($input)) {
        return map($input, fn($in) => strip($in, $characters));
    }
    if (is_scalar($input)) {
        $input = format_scalar($input);
    }

    return ($characters === '') ? trim((string) $input) : trim((string) $input, $characters);
}

/**
 * Replacer for strings and arrays.
 *
 * @param  string|array        $input
 * @param  string|array|RegExp $search
 * @param  string|array        $replace
 * @param  bool                $icase
 * @return string|array
 * @since  3.0, 6.0
 */
function replace(string|array $input, string|array|RegExp $search, string|array $replace, bool $icase = false): string|array
{
    if ($search instanceof RegExp) {
        return preg_replace($search->pattern, $replace, $input);
    }
    return $icase ? str_ireplace($search, $replace, $input) : str_replace($search, $replace, $input);
}

/**
 * Grep, actually grabs something from given input.
 *
 * @param  string $pattern
 * @param  string $input
 * @param  bool   $named
 * @return string|array|null
 * @since  3.0, 5.0
 */
function grep(string $pattern, string $input, bool $named = false): string|array|null
{
    $res = @preg_match($pattern, $input, $match, PREG_UNMATCHED_AS_NULL);

    // Act as original.
    if ($res === false) {
        $message = preg_error_message(func: 'preg_match');
        trigger_error(sprintf('%s(): %s', __FUNCTION__, $message), E_USER_WARNING);

        return null;
    }

    if (count($match) > 1) {
        unset($match[0]);

        // For named capturing groups.
        if ($named) {
            $ret = array_filter($match, fn($k): bool => is_string($k), 2);
        } else {
            $ret = array_filter($match, fn($v): bool => $v !== null);
        }

        // Reset keys (to 0-N).
        $ret = array_slice($ret, 0);

        // Single return.
        if (!$named && count($ret) === 1) {
            $ret = current($ret);
        }

        return $ret;
    }

    return null;
}

/**
 * Grep all, actually grabs somethings from given input.
 *
 * @param  string $pattern
 * @param  string $input
 * @param  bool   $named
 * @param  bool   $uniform
 * @return array|null
 * @since  3.15, 5.0
 */
function grep_all(string $pattern, string $input, bool $named = false, bool $uniform = false): array|null
{
    $res = @preg_match_all($pattern, $input, $match, PREG_UNMATCHED_AS_NULL);

    // Act as original.
    if ($res === false) {
        $message = preg_error_message(func: 'preg_match');
        trigger_error(sprintf('%s(): %s', __FUNCTION__, $message), E_USER_WARNING);

        return null;
    }

    if (count($match) > 1) {
        unset($match[0]);

        // For named capturing groups.
        if ($named) {
            $ret = array_filter($match, fn($k): bool => is_string($k), 2);
        } else {
            $ret = array_filter($match, fn($v): bool => $v !== null);
        }

        // Reduce moving sub matches up.
        $ret = array_apply($ret, function ($re) {
            if (is_array($re) && count($re) === 1) {
                $re = current($re);
            }
            return $re;
        });

        // Useful for combining, for example:
        // [$link, $text] = grep_all('~href="(.+?)"|>([^>]+?)<~u', $link, uniform: true);
        if ($uniform) {
            $ret = array_apply($ret, function ($re) {
                if (is_array($re)) {
                    $re = array_filter($re, 'size');
                    if (count($re) === 1) {
                        $re = current($re);
                    }
                }
                return $re;
            });
        }

        // Reset keys (to 0-N).
        $ret = array_slice($ret, 0);

        // Single return.
        if (!$named && count($ret) === 1) {
            $ret = (array) current($ret);
        }

        return $ret;
    }

    return null;
}

/**
 * Convert base (original source: http://stackoverflow.com/a/4668620/362780).
 *
 * @param  int|string $input
 * @param  int|string $from  From base or characters.
 * @param  int|string $to    To base or characters.
 * @return string
 * @throws ArgumentError
 * @since  4.0, 4.25
 */
function convert_base(int|string $input, int|string $from, int|string $to): string
{
    $input = strval($input);
    if ($input === '') {
        return $input;
    }
    if ($from === $to) {
        return $input;
    }

    // Try to use speed/power of GMP.
    if (is_int($from) && is_int($to) && extension_loaded('gmp')) {
        if ($from < 2 || $from > 62) {
            throw new ArgumentError('Invalid from base %s [min=2, max=62]', $from);
        }
        if ($to < 2 || $to > 62) {
            throw new ArgumentError('Invalid to base %s [min=2, max=62]', $to);
        }
        if (!ctype_alnum($input)) {
            throw new ArgumentError('Invalid input %s', $input);
        }

        return gmp_strval(gmp_init($input, $from), $to);
    }

    // Using base62 characters.
    $characters = BASE62_ALPHABET;

    if (is_int($from)) {
        if ($from < 2 || $from > 62) {
            throw new ArgumentError('Invalid from base %s [min=2, max=62]', $from);
        }

        $from = strcut($characters, $from);
    }
    if (is_int($to)) {
        if ($to < 2 || $to > 62) {
            throw new ArgumentError('Invalid to base %s [min=2, max=62]', $to);
        }

        $to = strcut($characters, $to);
    }

    [$input_length, $from_base_length, $to_base_length]
        = [strlen($input), strlen($from), strlen($to)];

    $numbers = [];
    for ($i = 0; $i < $input_length; $i++) {
        $numbers[$i] = strpos($from, $input[$i]);
    }

    $ret = '';
    $old_length = $input_length;

    do {
        $new_length = $div = 0;

        for ($i = 0; $i < $old_length; $i++) {
            $div = ($div * $from_base_length) + $numbers[$i];
            if ($div >= $to_base_length) {
                $numbers[$new_length++] = (int) ($div / $to_base_length);
                $div = $div % $to_base_length;
            } elseif ($new_length > 0) {
                $numbers[$new_length++] = 0;
            }
        }

        $old_length = $new_length;

        $ret = $to[$div] . $ret;
    } while ($new_length !== 0);

    return $ret;
}

/**
 * Convert case.
 *
 * @param  string      $input
 * @param  string|int  $case
 * @param  string|null $exploder
 * @param  string|null $imploder
 * @param  string|null $reduce
 * @return string
 * @throws ArgumentError
 * @since  4.26
 */
function convert_case(string $input, string|int $case, string $exploder = null, string $imploder = null, string $reduce = null): string
{
    if (is_string($case)) {
        $case = match (strtolower($case)) {
            'lower' => CASE_LOWER, 'upper' => CASE_UPPER,
            'title' => CASE_TITLE, 'dash'  => CASE_DASH,
            'snake' => CASE_SNAKE, 'camel' => CASE_CAMEL,
            default => throw new ArgumentError(
                'Invalid case %q [valids: lower,upper,dash,snake,title,camel]',
                $case
            )
        };
    }

    // Reduce given char(s) if repeating more then 2 times.
    $reduce && $input = preg_replace('~[' . preg_quote($reduce, '~') . ']{2,}~', $reduce, $input);

    // Shortcut for lower & upper cases.
    if ($case === CASE_LOWER) return mb_strtolower($input);
    if ($case === CASE_UPPER) return mb_strtoupper($input);

    // Set default split char.
    $exploder = ($exploder !== null && $exploder !== '') ? $exploder : ' ';

    return match ($case) {
        CASE_DASH  => implode('-', explode($exploder, mb_strtolower($input))),
        CASE_SNAKE => implode('_', explode($exploder, mb_strtolower($input))),
        CASE_TITLE => implode($imploder ?? $exploder, array_map(
            fn($s): string => mb_ucfirst(trim($s)),
            explode($exploder, mb_strtolower($input))
        )),
        CASE_CAMEL => mb_lcfirst(implode('', array_map(
            fn($s): string => mb_ucfirst(trim($s)),
            explode($exploder, mb_strtolower($input))
        ))),
        // Invalid case.
        default => throw new ArgumentError(
            'Invalid case %q, use a valid constant: %A', [$case, array_flip(array_filter(
                get_defined_constants(), fn($name) => str_starts_with($name, 'CASE_'), 2
            ))]
        )
    };
}

/**
 * Check whether class 1 extends class 2.
 *
 * @param  string $class1
 * @param  string $class2
 * @param  bool   $parent_only
 * @return bool
 */
function class_extends(string $class1, string $class2, bool $parent_only = false): bool
{
    return !$parent_only ? is_subclass_of($class1, $class2)
         : is_subclass_of($class1, $class2) && current(class_parents($class1)) === $class2;
}

/**
 * Check whether interface 1 extends interface 2.
 *
 * @param  string $interface1
 * @param  string $interface2
 * @param  bool   $parent_only
 * @return bool
 */
function interface_extends(string $interface1, string $interface2, bool $parent_only = false): bool
{
    return !$parent_only ? is_subclass_of($interface1, $interface2)
         : is_subclass_of($interface1, $interface2) && current(class_implements($interface1)) === $interface2;
}

/**
 * Get class name.
 *
 * @param  string|object $class
 * @param  bool          $short
 * @param  bool          $real
 * @param  bool          $escape
 * @return string
 * @since  5.0
 */
function get_class_name(string|object $class, bool $short = false, bool $real = false, bool $escape = false): string
{
    return match (true) {
        $short  => Objects::getShortName($class, $escape),
        $real   => Objects::getRealName($class),
        default => Objects::getName($class, $escape),
    };
}

/**
 * Get class namespace.
 *
 * @param  string|object $class
 * @param  bool          $base_only
 * @return string
 * @since  7.0
 */
function get_class_namespace(string|object $class, bool $base_only = false): string
{
    return Objects::getNamespace($class, $base_only);
}

/**
 * Get class file.
 *
 * @param  string|object $class
 * @return string|false|null
 * @since  7.0
 */
function get_class_file(string|object $class): string|false|null
{
    return Objects::getFile($class);
}

/**
 * Get constants of given class/object, or return null if no such class.
 *
 * @param  string|object $class
 * @param  bool          $scope
 * @param  bool          $names_only
 * @param  bool          $assoc
 * @return array|null
 * @since  4.0
 */
function get_class_constants(string|object $class, bool $scope = true, bool $names_only = false, bool $assoc = true): array|null
{
    if ($scope) {
        $all = ($caller_class = debug_backtrace(2, 2)[1]['class'] ?? null)
            && ($caller_class === Objects::getName($class));
    } else {
        $all = !$scope;
    }

    return $names_only ? Objects::getConstantNames($class, $all) : Objects::getConstantValues($class, $all, $assoc);
}

/**
 * Get properties of given class/object, or return null if no such class.
 *
 * @param  string|object $class
 * @param  bool          $scope
 * @param  bool          $names_only
 * @param  bool          $assoc
 * @return array|null
 * @since  4.0
 */
function get_class_properties(string|object $class, bool $scope = true, bool $names_only = false, bool $assoc = true): array|null
{
    if ($scope) {
        $all = ($caller_class = debug_backtrace(2, 2)[1]['class'] ?? null)
            && ($caller_class === Objects::getName($class));
    } else {
        $all = !$scope;
    }

    return $names_only ? Objects::getPropertyNames($class, $all) : Objects::getPropertyValues($class, $all, $assoc);
}

/**
 * Get a constant name.
 *
 * @param  mixed        $value
 * @param  string|array $name_prefix
 * @return string|null
 * @throws ArgumentError
 * @since  5.26
 */
function get_constant_name(mixed $value, string|array $name_prefix): string|null
{
    $name_prefix || throw new ArgumentError('Empty name prefix given');

    // Regular constants.
    if (is_string($name_prefix)) {
        return array_first(array_filter(array_keys(get_defined_constants(), $value, true),
            fn($name): bool => str_starts_with($name, $name_prefix)));
    }

    // Class constants.
    return array_first(array_filter(array_keys(get_class_constants($name_prefix[0], false), $value, true),
        fn($name): bool => str_starts_with($name, $name_prefix[1])));
}

/**
 * Get a constant value.
 *
 * @param  string     $name
 * @param  mixed|null $default
 * @return mixed|null
 * @throws ArgumentError
 * @since  5.26
 */
function get_constant_value(string $name, mixed $default = null): mixed
{
    $name || throw new ArgumentError('Empty name given');

    return defined($name) ? constant($name) : $default;
}

/**
 * Check if a class constant exists.
 *
 * @param  string|object $class
 * @param  string        $name
 * @param  bool          $scope
 * @param  bool          $upper
 * @return bool
 * @since  4.0
 */
function constant_exists(string|object $class, string $name, bool $scope = false, bool $upper = false): bool
{
    $class = Objects::getName($class);
    $upper && $name = strtoupper($name);

    if ($scope) {
        $caller_class = debug_backtrace(2, 2)[1]['class'] ?? null;
        if ($caller_class) {
            return ($caller_class === $class || is_class_of($class, $caller_class))
                && Objects::hasConstant($class, $name);
        }
        return defined($class .'::'. $name); // Direct check.
    }

    return defined($class .'::'. $name) || Objects::hasConstant($class, $name);
}

/**
 * Check if a class property initialized.
 *
 * @param  string|object $class
 * @param  string        $name
 * @return bool
 * @causes ReflectionException Property $class::$name does not exist.
 * @causes TypeError           Argument $object must be provided for instance properties.
 * @since  7.8
 */
function property_initialized(string|object $class, string $name): bool
{
    $ref = new ReflectionProperty($class, $name);

    return is_string($class) ? $ref->isInitialized() : $ref->isInitialized($class);
}

/**
 * Get type with/without scalars option.
 *
 * @param  mixed $var
 * @param  bool  $scalars
 * @return string
 * @since  4.0
 */
function get_type(mixed $var, bool $scalars = false): string
{
    if ($scalars && is_scalar($var)) {
        return 'scalar';
    }
    return get_debug_type($var);
}

/**
 * Get last error if exists, by field when given.
 *
 * @param  string|null $field
 * @return mixed|null
 * @since  4.17
 */
function get_error(string $field = null): mixed
{
    $error = error_get_last();
    if (!$error) {
        return null;
    }

    // Put in a new order.
    $error = array_select($error, ['type', 'file', 'line', 'message', 'function'], combine: true);

    // Separate message & function if available.
    if ($pos = strpos($error['message'], '):')) {
        $mes = $error['message'];

        $error['message']  = ucfirst(substr($mes, $pos + 3));
        $error['function'] = substr($mes, 0, $pos + 1);
    }

    return $field ? $error[$field] ?? null : $error;
}

/**
 * Get a unique id with/without length & base options.
 *
 * @param  int  $length
 * @param  int  $base
 * @param  bool $upper
 * @param  bool $hrtime
 * @return string
 * @throws ArgumentError
 * @since  4.0
 */
function get_unique_id(int $length = 16, int $base = 16, bool $upper = false, bool $hrtime = false): string
{
    if ($length < 16 && $base < 17) {
        throw new ArgumentError('Invalid length %s [min=16]', $length);
    } elseif ($base < 10 || $base > 62) {
        throw new ArgumentError('Invalid base %s [min=10, max=62]', $base);
    }

    // Grab 16-length hex from uniqid() or map hrtime() as hex.
    if (!$hrtime) {
        $id = explode('.', uniqid('', true))[0];
    } else {
        $id = implode('', map(hrtime(), 'dechex'));
    }

    $ret = $id;

    // Convert non-hex.
    if ($base !== 16) {
        $ret = '';

        foreach (str_split($id, 8) as $i) {
            $ret .= convert_base($i, 16, $base);
        }
    }

    // Pad if needed.
    if ($length > ($ret_length = strlen($ret))) {
        $ret .= suid($length - $ret_length, $base);
    }

    if ($upper && $base > 10) {
        $ret = strtoupper($ret);
    }

    return strcut($ret, $length);
}

/**
 * Get a random id with/without length & base options..
 *
 * @param  int  $length
 * @param  int  $base
 * @param  bool $upper
 * @return string
 * @throws ArgumentError
 * @since  4.0
 */
function get_random_id(int $length = 16, int $base = 16, bool $upper = false): string
{
    if ($length < 16 && $base < 17) {
        throw new ArgumentError('Invalid length %s [min=16]', $length);
    } elseif ($base < 10 || $base > 62) {
        throw new ArgumentError('Invalid base %s [min=10, max=62]', $base);
    }

    $ret = '';

    while ($length > strlen($ret)) {
        $id = random_xbytes($length);

        // Convert non-hex.
        if ($base !== 16) {
            $id = convert_base($id, 16, $base);
        }

        $ret .= $id;
    }

    if ($upper && $base > 10) {
        $ret = strtoupper($ret);
    }

    return strcut($ret, $length);
}

/**
 * Get request id.
 *
 * Format: seconds - microseconds, server ip - server port - remote port.
 *
 * @return string
 * @since  4.0
 */
function get_request_id(): string
{
    $parts = explode('.', utime(true) .'.'. ip2long(
        $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname())
    ));

    $parts[] = $_SERVER['SERVER_PORT'] ?? 0;
    $parts[] = $_SERVER['REMOTE_PORT'] ?? 0;

    return vsprintf('%x-%x-%x-%x-%x', $parts);
}

/**
 * Get real path of given path.
 *
 * @param  string           $path
 * @param  string|true|null $check Valids: true, "dir", "file".
 * @param  bool             $real @internal
 * @return string|false|null
 * @throws ArgumentError
 * @since  4.0
 */
function get_real_path(string $path, string|true $check = null, bool $real = true): string|false|null
{
    if (str_empty($path)) {
        return false;
    }

    if ($check && !in_array($check, [true, 'dir', 'file'], true)) {
        throw new ArgumentError(
            'Invalid check directive %q [valids: true, "dir", "file"]',
            $check
        );
    }

    // NULL-bytes issue.
    if (str_contains($path, "\0")) {
        $path = str_replace("\0", "\\0", $path);
    }

    // Validate existence of directory / file or file only.
    static $check_path; $check_path ??= fn($c, $p): bool => (
        $c === true ? file_exists($p) : ($c === 'dir' ? is_dir($p) : is_file($p))
    );

    // Use realpath(), but return result if no check.
    if ($real && ($ret = realpath($path))) {
        if ($check && !$check_path($check, $ret)) {
            return null;
        }
        return $ret;
    }

    $ret = '';
    $sep = DIRECTORY_SEPARATOR;
    $win = DIRECTORY_SEPARATOR === '\\';

    // Alter path "foo" => "./foo" to prevent invalid returns.
    if (!str_contains($path, $sep) || ($win && substr($path, 1, 2) !== ':\\')) {
        $path = '.' . $sep . $path;
    }

    // Fix "/." => "/" + cwd() returns.
    if ($path[0] === $sep) {
        $ret .= $sep;
    }

    foreach (split($sep, $path) as $i => $cur) {
        if ($i === 0) {
            // Home path (eg: ~/Desktop => /home/kerem/Desktop).
            if ($cur === '~') {
                $ret = getenv('HOME') ?: '';
                continue;
            }
            // Current / parent path.
            elseif ($cur === '.' || $cur === '..') {
                // Set once.
                if ($ret === '') {
                    // @cancel
                    // $file = getcwd(); // Fallback.
                    // foreach (debug_backtrace(0) as $trace) {
                    //     // Search until finding the right path argument (sadly seems no way else
                    //     // for that when call stack is chaining from a function to another function).
                    //     if (empty($trace['args'][0]) || $trace['args'][0] !== $path) {
                    //         break;
                    //     }
                    //     $file = $trace['file'];
                    // }

                    // Eg: "/var/www" + "/" + "a.php"
                    $tmp = getcwd() . $sep . basename($path);
                    $ret = dirname($tmp, levels: strlen($cur));
                    unset($tmp);
                }
                continue;
            }
        }

        if ($cur === '.') {
            continue; // Current.
        } elseif ($cur === '..') {
            $ret = dirname($ret); // Parent.
            continue;
        }

        // Prepend separator.
        $ret .= $sep . $cur;
    }

    // For root stuff (empty split).
    if ($ret === '' && str_contains($path, $sep)) {
        $ret = $sep;
    }

    if ($check && !$check_path($check, $ret)) {
        return null;
    }

    // Normalize.
    if ($ret !== '') {
        // Drop repeating separators.
        $ret = preg_replace('~(['. preg_quote(PATH_SEPARATOR . DIRECTORY_SEPARATOR) .'])\1+~', '\1', $ret);

        // Drop ending separators.
        if ($ret !== PATH_SEPARATOR && $ret !== DIRECTORY_SEPARATOR) {
            $ret = chop($ret, PATH_SEPARATOR . DIRECTORY_SEPARATOR);
        }

        // Fix leading slash for win.
        if ($win && $ret[0] === $sep) {
            $ret = substr($ret, 1);
        }
    }

    return $ret;
}

/**
 * Get path info of given path.
 *
 * @param  string          $path
 * @param  string|int|null $component
 * @return string|array|false|null
 * @since  5.0
 */
function get_path_info(string $path, string|int $component = null): string|array|false|null
{
    if (str_empty($path)) {
        return false;
    }

    $opath = $path;

    if (!$path = get_real_path($path, real: false)) {
        return null;
    }
    if (!$info = pathinfo($path)) {
        return null;
    }

    // Really really, real path.
    if ($realpath = realpath($path)) {
        @ $filetype = filetype($opath) ?: filetype($path);
    } else {
        $realpath = $filetype = null;
    }

    // Drop "" fields, put in order.
    $info = array_filter($info, 'strlen');
    $info = array_select($info, ['dirname', 'basename', 'filename', 'extension'], combine: true);

    // Null if not real parent.
    if ($info['dirname'] === $path && $filetype === 'dir') {
        $info['dirname'] = null;
    }

    $ret = ['path' => $path, 'realpath' => $realpath, 'type' => $filetype] + $info;

    // No filename & extension for dirs.
    if ($filetype === 'dir' || strsfx($opath, DIRECTORY_SEPARATOR) || strsfx($path, DIRECTORY_SEPARATOR)) {
        $ret['filename'] = $ret['extension'] = null;
    } elseif ($filetype === 'link' && is_dir($path)) {
        $ret['filename'] = $ret['extension'] = null;
    } else {
        [$ret['filename'], $ret['extension']] = [file_name($path), file_extension($path)];
    }

    if ($component !== null) {
        if (is_string($component)) {
            $ret = $ret[$component] ?? null;
        } else {
            $ret = match ($component) {
                PATHINFO_DIRNAME  => $ret['dirname'],  PATHINFO_BASENAME  => $ret['basename'],
                PATHINFO_FILENAME => $ret['filename'], PATHINFO_EXTENSION => $ret['extension'],
                PATHINFO_TYPE     => $ret['type'],     default            => $ret, // All.
            };
        }
    }

    return $ret;
}

/**
 * Get a bit detailed trace with default options, limit, index and field options.
 *
 * @param  int         $options
 * @param  int         $limit
 * @param  int|null    $index
 * @param  string|null $field
 * @param  int|null    $slice
 * @param  bool        $reverse
 * @return mixed|null
 * @since  4.0
 */
function get_trace(int $options = 0, int $limit = 0, int $index = null, string $field = null, int $slice = null, bool $reverse = false): mixed
{
    $stack = debug_backtrace($options, $limit ? $limit + 1 : 0);

    // Drop self.
    array_shift($stack);

    // When slice wanted (@internal).
    $slice   && $stack = array_slice($stack, $slice);
    $reverse && $stack = array_reverse($stack);

    foreach ($stack as $i => $trace) {
        $trace = [
            // Index.
            '#' => $i,
            // For "[internal function]", "{closure}" stuff.
            'file' => $trace['file'] ?? null,
            'line' => $trace['line'] ?? null,
        ] + $trace + [
            // Additions.
            'callee' => $trace['function'] ?? null,
            'caller' => null, 'callerClass' => null, 'callerMethod' => null,
        ];

        if (isset($trace['file'], $trace['line'])) {
            $trace['callPath'] = $trace['file'] . ':' . $trace['line'];
        } else {
            $trace['callPath'] = '[internal function]:';
        }

        if (isset($trace['class'])) {
            $trace['method']     = $trace['function'];
            $trace['methodType'] = ($trace['type'] === '::') ? 'static' : 'non-static';
        }
        if (isset($stack[$i + 1]['function'])) {
            $trace['caller'] = $stack[$i + 1]['function'];
        }
        if (isset($stack[$i + 1]['class'])) {
            $trace['callerClass']  = $stack[$i + 1]['class'];
            $trace['callerMethod'] = sprintf('%s::%s', $stack[$i + 1]['class'], $stack[$i + 1]['function']);
        }

        $stack[$i] = $trace;
    }

    return is_null($index) ? $stack : ($stack[$index][$field] ?? $stack[$index] ?? null);
}

/**
 * Get current locale info.
 *
 * @param  int               $category
 * @param  string|array|null $default
 * @param  bool              $array
 * @return string|array|null
 * @since  5.26
 */
function getlocale(int $category = LC_ALL, string|array $default = null, bool $array = false): string|array|null
{
    $ret = $tmp = setlocale($category, 0);
    if ($ret === false) {
        $ret = $default;
    }

    if ($tmp !== false && $array) {
        $tmp = [];
        // Multi, eg: LC_ALL.
        if (str_contains($ret, ';')) {
            foreach (split(';', $ret) as $re) {
                [$name, $value] = split('=', $re, 2);
                $tmp[] = ['name' => $name, 'value' => $value,
                          'category' => get_constant_value($name)];
            }
        } else {
            // Single, eg: LC_TIME.
            $tmp = ['name' => ($name = get_constant_name($category, 'LC_')),
                    'value' => $ret, 'category' => $name ? $category : null];
        }
        $ret = $tmp;
    }

    return $ret;
}

/**
 * Get current value of given array (for the sake of key()) or given key's value if exists.
 *
 * @param  array           $array
 * @param  int|string|null $key
 * @param  mixed|null      $default
 * @return mixed
 * @since  5.35
 */
function value(array $array, int|string $key = null, mixed $default = null): mixed
{
    // When a key wanted.
    if (func_num_args() > 1) {
        return $array[$key] ?? $default;
    }

    return $array ? current($array) : null; // No falses.
}

/**
 * Really got sick of "pass by reference" error.
 *
 * @param  array $array
 * @return mixed
 * @since  5.29
 */
function first(array $array): mixed
{
    return $array ? reset($array) : null; // No falses.
}

/**
 * Really got sick of "pass by reference" error.
 *
 * @param  array $array
 * @return mixed
 * @since  5.29
 */
function last(array $array): mixed
{
    return $array ? end($array) : null; // No falses.
}

/**
 * Remove last error message with/without code.
 *
 * @param  int|null $code
 * @return void
 * @since  5.0
 */
function error_clear(int $code = null): void
{
    $error = error_get_last();
    if (!$error) {
        return;
    }

    if ($code && $code !== $error['type']) {
        return;
    }

    error_clear_last();
}

/**
 * Get last error message with code, optionally formatted.
 *
 * @param  int|null &$code
 * @param  bool     $format
 * @param  bool     $extract
 * @param  bool     $clear
 * @return string|null
 * @since  4.17
 */
function error_message(int &$code = null, bool $format = false, bool $extract = false, bool $clear = false): string|null
{
    $error = error_get_last();
    if (!$error) {
        return null;
    }

    $code = $error['type'];
    $clear && error_clear($code);

    // Format with name.
    if ($format) {
        $error['name'] = match ($error['type']) {
            E_NOTICE,     E_USER_NOTICE     => 'NOTICE',
            E_WARNING,    E_USER_WARNING    => 'WARNING',
            E_DEPRECATED, E_USER_DEPRECATED => 'DEPRECATED',
            default                         => 'ERROR'
        };

        return vsprintf('%s(%d): %s @%s:%d', array_select(
            $error, ['name', 'type', 'message', 'file', 'line']
        ));
    }
    // Extract message only dropping caused function.
    elseif ($extract && ($pos = strpos($error['message'], '):'))) {
        return ucfirst(substr($error['message'], $pos + 3));
    }

    return $error['message'];
}

/**
 * Get JSON last error message with code if any, instead "No error".
 *
 * @param  int|null &$code
 * @param  bool     $clear
 * @return string|null
 * @since  4.17
 */
function json_error_message(int &$code = null, bool $clear = false): string|null
{
    $message = ($code = json_last_error()) ? json_last_error_msg() : null;

    // Clear last error.
    if ($clear && $message) {
        json_decode('""');
    }

    return $message;
}

/**
 * Get PECL last error message with code if any, instead "No error".
 *
 * @param  int|null    &$code
 * @param  string|null $func
 * @param  bool        $clear
 * @return string|null
 * @since  4.17
 */
function preg_error_message(int &$code = null, string $func = null, bool $clear = false): string|null
{
    // No specific functions.
    if ($func === null) {
        $message = ($code = preg_last_error()) ? preg_last_error_msg() : null;

        // Clear last error.
        if ($clear && $message) {
            preg_match('~~', '');
        }

        return $message;
    }

    $error = error_get_last();

    if (isset($error['type'], $error['message'])) {
        ['type' => $code, 'message' => $message] = $error;

        if (strpfx($message, $func ?: 'preg_')) {
            // Clear last error.
            $clear && error_clear_last();

            return strsub($message, strpos($message, '):') + 3);
        }
    }

    return null;
}

/**
 * Format like `sprintf()` but with additional specifiers.
 *
 * Specifiers: %q single-quotes, %Q double-quotes, %i integer, %n number, %t type,
 * %k backticks, %b bool, %a join(','), %A join(', '), %U upper %L lower,
 * %S escape NULL-bytes, join arrays (','), fix floats (N.0).
 *
 * Example: format('a: %S | b: %S | c: %S, d: %k', "x\0y\0z", [1,2,3,4.0], 1.0, 'foo')
 *   => 'a: x\0y\0z | b: 1,2,3,4.0 | c: 1.0, d: `foo`'
 *
 * Note: Multi-escaped formats are problematic (eg: 'a: %%S | b: %%%S').
 *
 * @param  string   $format
 * @param  mixed ...$arguments
 * @return string
 * @throws ArgumentError
 */
function format(string $format, mixed ...$arguments): string
{
    // @cancel: Argument indexes don't match with specifiers indexes.
    // if (preg_match_all('~(?<!%)%[qQintkbaAULS]~', $format, $match)) {
    if (preg_match_all('~(?<!%)%[a-zA-Z]~', $format, $match)) {
        $specifiers = $match[0];

        if (count($specifiers) > count($arguments)) {
            throw new ArgumentError(
                'Arguments must contain %d items, %d given',
                [count($specifiers), count($arguments)]
            );
        }

        foreach ($specifiers as $i => $specifier) {
            $offset = strpos($format, $specifier);

            switch ($specifier) {
                // Quoted strings.
                case '%q': case '%Q':
                    $repl = ($specifier === '%q') ? "'%s'" : '"%s"';
                    $format = substr_replace($format, $repl, $offset, 2);
                    break;

                // Integers (digits).
                case '%i':
                    $format = substr_replace($format, '%d', $offset, 2);
                    break;

                // Numbers.
                case '%n':
                    $format = substr_replace($format, '%s', $offset, 2);
                    $arguments[$i] = format_number((float) $arguments[$i]);
                    break;

                // Types.
                case '%t':
                    $format = substr_replace($format, '%s', $offset, 2);
                    $arguments[$i] = get_type($arguments[$i]);
                    break;

                // Backticks.
                case '%k':
                    $format = substr_replace($format, '`%s`', $offset, 2);
                    break;

                // Bools (as stringified bools, not 0/1).
                case '%b':
                    $format = substr_replace($format, '%s', $offset, 2);
                    $arguments[$i] = format_bool($arguments[$i]);
                    break;

                // Arrays (as joinified items).
                case '%a': case '%A':
                    $format = substr_replace($format, '%s', $offset, 2);
                    $separator = ($specifier === '%a') ? ',' : ', ';
                    // Handle each value mapping all.
                    $arguments[$i] = join($separator, map((array) $arguments[$i], fn($v) => (
                        is_type_of($v, 'string|float') ? format('%S', $v) : $v
                    )));
                    break;

                // Upper/Lower case.
                case '%U': case '%L':
                    $format = substr_replace($format, '%s', $offset, 2);
                    $function = ($specifier === '%U') ? 'upper' : 'lower';
                    $arguments[$i] = $function((string) $arguments[$i]);
                    break;

                // Escape NULL-bytes, join arrays, fix floats (N.0).
                case '%S':
                    $format = substr_replace($format, '%s', $offset, 2);
                    if (is_string($arguments[$i])) {
                        $arguments[$i] = str_replace("\0", "\\0", $arguments[$i]);
                    } elseif (is_array($arguments[$i])) {
                        // Handle each value mapping all.
                        $arguments[$i] = join(',', map($arguments[$i], fn($v) => (
                            is_type_of($v, 'string|float') ? format('%S', $v) : $v
                        )));
                    } elseif (is_float($arguments[$i])) {
                        $arguments[$i] = format_number($arguments[$i]);
                    }
                    break;
            }
        }
    }

    return vsprintf($format, $arguments);
}

/**
 * Format an input as bool (yes).
 *
 * @param  mixed $input
 * @param  bool  $numeric
 * @return string
 */
function format_bool(mixed $input, bool $numeric = false): string
{
    if ($numeric) {
        return $input ? '1' : '0';
    }
    return $input ? 'true' : 'false';
}

/**
 * Format an input as number.
 *
 * @param  int|float|string $input
 * @param  int|true         $decs
 * @param  string|null      $dsep
 * @param  string|null      $tsep
 * @return string|null
 */
function format_number(int|float|string $input, int|true $decs = true, string $dsep = null, string $tsep = null): string|null
{
    if (is_string($input)) {
        if (!is_numeric($input)) {
            trigger_error(sprintf('%s(): Invalid non-numeric input', __FUNCTION__));
            return null;
        }

        $input += 0;
    }

    // Some speed..
    if (is_int($input) && !$tsep) {
        return (string) $input;
    }

    $export = var_export($input, true);

    // Auto-detect decimals.
    if ($decs === true) {
        $decs = strlen(stracut($export, '.'));
    }

    // Prevent data corruptions.
    if ($decs > PRECISION) {
        $decs = PRECISION;
    }

    $ret = number_format($input, $decs, $dsep, $tsep);

    // Append ".0" for eg: 1.0 & upper NAN/INF.
    if (!$decs && !is_int($input) && strlen($export) === 1) {
        $ret .= '.0';
    } elseif ($ret === 'inf' || $ret === 'nan') {
        $ret = strtoupper($ret);
    }

    return $ret;
}

/**
 * Format an input if scalar, else return null.
 *
 * @param  mixed    $input
 * @param  mixed ...$options
 * @return string|null
 */
function format_scalar(mixed $input, mixed ...$options): string|null
{
    switch (true) {
        case is_string($input):
            return $input;
        case is_number($input):
            $defaults = is_list($options) ? [true] : ['decs' => true];
            $arguments = array_options($options, $defaults, map: false);
            return format_number($input, ...$arguments);
        case is_bool($input):
            $defaults = is_list($options) ? [false] : ['numeric' => false];
            $arguments = array_options($options, $defaults, map: false);
            return format_bool($input, ...$arguments);
        default:
            return null;
    }
}

/**
 * Translate given input to slugified output.
 *
 * @param  string $input
 * @param  string $preserve
 * @param  string $replace
 * @param  bool   $trim
 * @param  bool   $lower
 * @return string
 * @since  5.0
 */
function slug(string $input, string $preserve = '', string $replace = '-', bool $trim = true, bool $lower = true): string
{
    static $map; $map ??= require __DIR__ . '/etc/slug-map.php';

    if ($preserve !== '') $preserve = preg_quote($preserve, '~');

    $ret = preg_replace('~[^a-z0-9'. $preserve .']+~i', $replace, strtr($input, $map));

    $trim  && $ret = trim($ret, $preserve . $replace);
    $lower && $ret = lower($ret);

    return $ret;
}

/**
 * Generate a universally UID.
 *
 * @param  bool $time For Unix time prefix.
 * @param  bool $guid
 * @param  bool $hash
 * @param  bool $upper
 * @return string
 */
function uuid(bool $time = false, bool $guid = false, bool $hash = false, bool $upper = false): string
{
    return Uuid::generate($time, $guid, $hash, $upper);
}

/**
 * Generate a simple UID.
 *
 * @param  int $length Random characters length.
 * @param  int $base
 * @return string
 */
function suid(int $length = 6, int $base = 62): string
{
    return Uuid::generateSuid($length, $base);
}

/**
 * Generate a hashed UID.
 *
 * @param  int        $length Random bytes length.
 * @param  string|int $algo   Algo or base.
 * @return string
 */
function huid(int $length = 20, string|int $algo = 'md5'): string
{
    return Uuid::generateHash($length, $algo);
}

/**
 * Get an object id.
 *
 * @param  object $object
 * @param  bool   $with_name
 * @return int|string
 * @since  5.25
 */
function get_object_id(object $object, bool $with_name = true): int|string
{
    return Objects::getId($object, $with_name);
}

/**
 * Get an object hash.
 *
 * @param  object $object
 * @param  bool   $with_name
 * @param  bool   $with_rehash
 * @param  bool   $serialized
 * @return string
 * @since  5.25
 */
function get_object_hash(object $object, bool $with_name = true, bool $with_rehash = false, bool $serialized = false): string
{
    return !$serialized ? Objects::getHash($object, $with_name, $with_rehash) : Objects::getSerializedHash($object, $with_name);
}

/**
 * Set object vars.
 *
 * @param  object          $object
 * @param  object|iterable $vars
 * @return object
 * @since  6.0
 */
function set_object_vars(object $object, object|iterable $vars): object
{
    foreach ($vars as $name => $value) {
        if (property_exists($object, (string) $name)) {
            $ref = new ReflectionProperty($object, $name);
            $ref->setValue($object, $value);
        } elseif ($object instanceof stdClass) {
            $object->$name = $value;
        } elseif ($object instanceof ArrayAccess) {
            // Both ArrayAccess & ArrayObject.
            $object[$name] = $value;
        }
    }

    return $object;
}

/**
 * Check whether an argument was given in call silently (so func_get_arg() causes errors).
 *
 * @param  int|string $arg
 * @return bool
 * @since  5.28
 */
function func_has_arg(int|string $arg): bool
{
    $trace = debug_backtrace(0)[1];

    // Name check.
    if (is_string($arg)) {
        if (!empty($trace['args'])) {
            $ref = !empty($trace['class'])
                 ? new ReflectionCallable([$trace['class'], $trace['function']])
                 : new ReflectionCallable($trace['function']);

            return array_key_exists($ref->getParameter($arg)?->getPosition(), $trace['args']);
        }

        return false;
    }

    // Count & position check.
    return !empty($trace['args']) && array_key_exists($arg, $trace['args']);
}

/**
 * Check whether any arguments was given in call.
 *
 * @param  int|string ...$args
 * @return bool
 * @since  5.28
 */
function func_has_args(int|string ...$args): bool
{
    if ($args) {
        foreach ($args as $arg) {
            if (!func_has_arg($arg)) {
                return false;
            }
        }
        return true;
    }

    $trace = debug_backtrace(0)[1];

    // Count check.
    return !empty($trace['args']);
}

/**
 * Get an ini directive with bool option.
 *
 * @param  string     $name
 * @param  mixed|null $default
 * @param  bool       $bool
 * @return mixed|null
 * @since  4.0, 6.0
 */
function ini(string $name, mixed $default = null, bool $bool = false): mixed
{
    return System::iniGet($name, $default, $bool);
}

/**
 * Get an env/server variable.
 *
 * @param  string     $name
 * @param  mixed|null $default
 * @param  bool       $server
 * @return mixed|null
 * @since  4.0, 6.0
 */
function env(string $name, mixed $default = null, bool $server = true): mixed
{
    return System::envGet($name, $default, $server);
}

/**
 * Check if given input is a list.
 *
 * @param  mixed $var
 * @return bool
 * @since  5.0
 */
function is_list(mixed $var): bool
{
    return is_array($var) && array_is_list($var);
}

/**
 * Check if given input is a number (int|float).
 *
 * @param  mixed $var
 * @return bool
 * @since  5.0
 */
function is_number(mixed $var): bool
{
    return is_int($var) || is_float($var);
}

/**
 * Check if given input is a stream.
 *
 * @param  mixed $var
 * @return bool
 * @since  5.0
 */
function is_stream(mixed $var): bool
{
    return is_resource($var) && get_resource_type($var) === 'stream';
}

/**
 * Check if given input is a closure.
 *
 * @param  mixed $var
 * @return bool
 */
function is_closure(mixed $var): bool
{
    return is_object($var) && ($var instanceof Closure);
}

/**
 * Check if given input is a primitive (int|float|string|bool|true|false & array|null).
 *
 * Note: true|false is scalar (no mentions yet on http://php.net/is_scalar).
 *
 * @param  mixed $var
 * @return bool
 * @since  5.0
 */
function is_primitive(mixed $var): bool
{
    return is_scalar($var) || is_array($var) || is_null($var);
}

/**
 * Check if given input is arrayable.
 *
 * @param  mixed     $var
 * @param  bool|null &$native
 * @return bool
 * @since  7.0
 */
function is_arrayable(mixed $var, bool &$native = null): bool
{
    return ($native = is_iterable($var))
        || ($var instanceof froq\common\interface\Arrayable);
}

/**
 * Check if given input is stringable.
 *
 * @param  mixed     $var
 * @param  bool|null &$native
 * @return bool
 * @since  7.0
 */
function is_stringable(mixed $var, bool &$native = null): bool
{
    return ($native = is_string($var) || $var instanceof Stringable)
        || ($var instanceof froq\common\interface\Stringable);
}

/**
 * Check if given input is any type of given types.
 *
 * @param  mixed     $var
 * @param  string ...$types
 * @return bool
 * @since  5.0
 * @throws ArgumentError
 */
function is_type_of(mixed $var, string ...$types): bool
{
    $types = array_filter($types)
        ?: throw new ArgumentError('No type(s) given to check');

    $var_type = get_debug_type($var);

    // Multiple at once.
    if ($types && str_contains($types[0], '|')) {
        $types = explode('|', $types[0]);

        // A little bit faster than foreach().
        if (in_array($var_type, $types, true)) {
            return true;
        }
    }

    foreach ($types as $type) {
        if (match ($type) {
            // Any/mixed.
            'any', 'mixed' => true,

            // Sugar checkers.
            'list', 'number', 'stream', 'closure', 'primitive', 'true', 'false',
                => ('is_' . $type)($var),

            // Primitive & internal checkers.
            'int', 'float', 'string', 'bool', 'array', 'object', 'null',
            'numeric', 'scalar', 'resource', 'iterable', 'callable', 'countable',
                => ('is_' . $type)($var),

            // All others.
            default => ($var_type === $type) || ($var instanceof $type)
        }) {
            return true;
        }
    }

    return false;
}

/**
 * Check if given class is any type of given class(es).
 *
 * @param  string|object    $class
 * @param  string|object ...$classes
 * @return bool
 * @since  5.0
 * @throws ArgumentError
 */
function is_class_of(string|object $class, string|object ...$classes): bool
{
    $classes = array_filter($classes)
        ?: throw new ArgumentError('No class(es) given to check');

    $class1 = get_class_name($class);

    foreach ($classes as $class2) {
        $class2 = get_class_name($class2);

        if (is_a($class1, $class2, true)) {
            return true;
        }
    }

    return false;
}

/**
 * Check if given var(s) is empty.
 *
 * @param  mixed    $var
 * @param  mixed ...$vars
 * @return bool
 * @since  7.0
 */
function is_blank(mixed $var, mixed ...$vars): bool
{
    foreach ([$var, ...$vars] as $var) {
        if ($var === null || $var === '' || $var === []) {
            return true;
        }

        if (is_string($var) && trim($var, ' ') === '') {
            return true;
        }

        if (is_object($var) && !size($var)) {
            return true;
        }
    }

    return false;
}

/**
 * Check if given var(s) is empty.
 *
 * @param  mixed    $var
 * @param  mixed ...$vars
 * @return bool
 * @since  4.0, 5.0
 */
function is_empty(mixed $var, mixed ...$vars): bool
{
    foreach ([$var, ...$vars] as $var) {
        if (empty($var)) {
            return true;
        }

        if (is_object($var) && !size($var)) {
            return true;
        }
    }

    return false;
}

/**
 * Check if given input is true.
 *
 * @param  mixed $var
 * @return bool
 * @since  3.5, 5.6
 */
function is_true(mixed $var): bool
{
    return ($var === true);
}

/**
 * Check if given input is false.
 *
 * @param  mixed $var
 * @return bool
 * @since  3.5, 5.6
 */
function is_false(mixed $var): bool
{
    return ($var === false);
}
