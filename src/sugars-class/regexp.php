<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * A class for playing with regular expression stuff in OOP-way.
 *
 * @package global
 * @class   RegExp
 * @author  Kerem Güneş
 * @since   6.0
 */
class RegExp implements Stringable
{
    /** Delimiter. */
    public final const DELIMITER = '~';

    /** Valid modifiers (@see http://php.net/manual/reference.pcre.pattern.modifiers.php). */
    public final const MODIFIERS = ['i', 'm', 's', 'u', 'x', 'n', 'A', 'D', 'S', 'U', 'J', 'X'];

    /** PREG constants. */
    public final const PATTERN_ORDER        = PREG_PATTERN_ORDER,
                       SET_ORDER            = PREG_SET_ORDER,
                       OFFSET_CAPTURE       = PREG_OFFSET_CAPTURE,
                       SPLIT_NO_EMPTY       = PREG_SPLIT_NO_EMPTY,
                       SPLIT_DELIM_CAPTURE  = PREG_SPLIT_DELIM_CAPTURE,
                       SPLIT_OFFSET_CAPTURE = PREG_SPLIT_OFFSET_CAPTURE,
                       UNMATCHED_AS_NULL    = PREG_UNMATCHED_AS_NULL;

    /** PREG error constants. */
    public final const ERROR_NONE            = PREG_NO_ERROR,
                       ERROR_INTERNAL        = PREG_INTERNAL_ERROR,
                       ERROR_BACKTRACK_LIMIT = PREG_BACKTRACK_LIMIT_ERROR,
                       ERROR_RECURSION_LIMIT = PREG_RECURSION_LIMIT_ERROR,
                       ERROR_BAD_UTF8        = PREG_BAD_UTF8_ERROR,
                       ERROR_BAD_UTF8_OFFSET = PREG_BAD_UTF8_OFFSET_ERROR,
                       ERROR_JIT_STACKLIMIT  = PREG_JIT_STACKLIMIT_ERROR;

    /** Raw source. */
    public readonly string $source;

    /** Modifiers. */
    public readonly string $modifiers;

    /** Prepared pattern. */
    public readonly string $pattern;

    /** Throw option. */
    public bool $throw = false;

    /** Error instance. */
    private RegExpError $error;

    /** Error code. */
    private int $errorCode;

    /**
     * Constructor.
     *
     * @param  string $source    Plain RegExp source (without delimiters).
     * @param  string $modifiers Valids: imsuxnADSUJX.
     * @param  bool   $throw     False is silent mode.
     * @throws RegExpError
     */
    public function __construct(string $source, string $modifiers = '', bool $throw = false)
    {
        if ($modifiers !== '') {
            $modifiers = self::prepareModifiers($modifiers, $invalids);
            if (!$modifiers) {
                throw new RegExpError('Invalid modifiers: %q', $invalids);
            }
        }

        $this->source    = $source;
        $this->modifiers = $modifiers;
        $this->pattern   = $this->preparePattern($source, $modifiers);
        $this->throw     = $throw;
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return $this->pattern;
    }

    /**
     * Get error.
     *
     * @return RegExpError|null
     */
    public function error(): RegExpError|null
    {
        return $this->error ?? null;
    }

    /**
     * Get error code.
     *
     * @return int|null
     */
    public function errorCode(): int|null
    {
        return $this->errorCode ?? null;
    }

    /**
     * Perform a search & replace.
     *
     * @param  string   $input
     * @param  string   $replace
     * @param  int      $limit
     * @param  int|null &$count
     * @return string|null
     */
    public function filter(string $input, string $replace, int $limit = -1, int &$count = null): string|null
    {
        $ret = @preg_filter($this->pattern, $replace, $input, $limit, $count);

        if ($ret === null) {
            $this->processError('preg_filter');
        }

        return $ret;
    }

    /**
     * Perform a search & replace.
     *
     * @param  string          $input
     * @param  string|callable $replace
     * @param  int             $limit
     * @param  int|null        &$count
     * @param  int|array       $flags
     * @param  string|null     $class
     * @return string|null
     */
    public function replace(string $input, string|callable $replace, int $limit = -1, int &$count = null,
        int|array $flags = 0, string $class = null): string|null
    {
        $this->classCheck($class);
        $this->flagsCheck($flags);

        $callback = is_callable($replace);
        $function = $callback ? 'preg_replace_callback' : 'preg_replace';

        // Send class instance as match argument when a class given.
        if ($callback && $class) {
            $replace = fn($match) => $replace(new $class($match));
        }

        $ret = @$callback ? $function($this->pattern, $replace, $input, $limit, $count, $flags)
                          : $function($this->pattern, $replace, $input, $limit, $count);

        if ($ret === null) {
            $this->processError($function);
        }

        return $ret;
    }

    /**
     * Perform a search & callback replace.
     *
     * @param  string      $input
     * @param  callable    $callback
     * @param  int         $limit
     * @param  int|null    &$count
     * @param  array|int   $flags
     * @param  string|null $class
     * @return string|null
     */
    public function replaceCallback(string $input, callable $callback, int $limit = -1, int &$count = null,
        int|array $flags = 0, string $class = null): string|null
    {
        return $this->replace($input, $callback, $limit, $count, $flags, $class);
    }

    /**
     * Preform a remove.
     *
     * @param  string   $input
     * @param  int      $limit
     * @param  int|null &$count
     * @return string|null
     */
    public function remove(string $input, int $limit = -1, int &$count = null): string|null
    {
        if (is_string($input)) {
            $ret = @preg_remove($this->pattern, $input, $limit, $count);

            if ($ret === null) {
                $this->processError('preg_remove');
            }

            return $ret;
        }

        $rets = null;

        foreach ($input as $input) {
            $ret = @preg_remove($this->pattern, $input, $limit, $count);

            if ($ret === null) {
                $this->processError('preg_remove');
            } else {
                $rets[] = $ret;
            }
        }

        return $rets;
    }

    /**
     * Perform a split.
     *
     * @param  string      $input
     * @param  int         $limit
     * @param  int|array   $flags
     * @param  string|null $class
     * @return iterable|null
     */
    public function split(string $input, int $limit = -1, int|array $flags = 0, string $class = null): iterable|null
    {
        $this->classCheck($class);
        $this->flagsCheck($flags);

        $ret = @preg_split($this->pattern, $input, $limit, flags: (
            $flags |= PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE // Always..
        ));

        if ($ret === false) {
            $this->processError('preg_split');
            $ret = null;
        }

        // Plus: prevent "undefined index .." error.
        if ($limit > 0 && $limit > count((array) $ret)) {
            $ret = array_pad((array) $ret, $limit, null);
        }

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Perform a split for given iterable class.
     *
     * @param  string    $input
     * @param  string    $class
     * @param  int       $limit
     * @param  int|array $flags
     * @return iterable
     */
    public function splitTo(string $input, string $class, int $limit = -1, int|array $flags = 0): iterable
    {
        return $this->split($input, $limit, $flags, $class);
    }

    /**
     * Perform a split for XArray class.
     *
     * @param  string    $input
     * @param  int       $limit
     * @param  int|array $flags
     * @return XArray
     */
    public function xsplit(string $input, int $limit = -1, int|array $flags = 0): XArray
    {
        return $this->split($input, $limit, $flags, XArray::class);
    }

    /**
     * Perform a match.
     *
     * @param  string      $input
     * @param  int|array   $flags
     * @param  int         $offset
     * @param  string|null $class
     * @return iterable|null
     */
    public function match(string $input, int|array $flags = 0, int $offset = 0, string $class = null): iterable|null
    {
        $this->classCheck($class);
        $this->flagsCheck($flags);

        $res = @preg_match($this->pattern, $input, $ret, $flags, $offset);

        if ($res === false) {
            $this->processError('preg_match');
            $ret = null;
        }

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Perform a match-all.
     *
     * @param  string      $input
     * @param  int|array   $flags
     * @param  int         $offset
     * @param  string|null $class
     * @return iterable|null
     */
    public function matchAll(string $input, int|array $flags = 0, int $offset = 0, string $class = null): iterable|null
    {
        $this->classCheck($class);
        $this->flagsCheck($flags);

        $res = @preg_match_all($this->pattern, $input, $ret, $flags, $offset);

        if ($res === false) {
            $this->processError('preg_match_all');
            $ret = null;
        }

        // Drop empty stuff.
        $ret && $ret = array_filter($ret, 'count');

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Perform a match names.
     *
     * @param  string      $input
     * @param  int|array   $flags
     * @param  int         $offset
     * @param  string|null $class
     * @return iterable|null
     */
    public function matchNames(string $input, int|array $flags = 0, int $offset = 0, string $class = null): iterable|null
    {
        $this->classCheck($class);
        $this->flagsCheck($flags);

        $res = @preg_match_names($this->pattern, $input, $ret, $flags, $offset);

        if ($res === false) {
            $this->processError('preg_match_names');
            $ret = null;
        }

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Perform a match-all names.
     *
     * @param  string      $input
     * @param  int|array   $flags
     * @param  int         $offset
     * @param  string|null $class
     * @return iterable|null
     */
    public function matchAllNames(string $input, int|array $flags = 0, int $offset = 0, string $class = null): iterable|null
    {
        $this->classCheck($class);
        $this->flagsCheck($flags);

        $res = @preg_match_all_names($this->pattern, $input, $ret, $flags, $offset);

        if ($res === false) {
            $this->processError('preg_match_all_names');
            $ret = null;
        }

        // Drop empty stuff.
        $ret && $ret = array_filter($ret, 'count');

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Perform an exec.
     *
     * @param  string      $input
     * @param  int|array   $flags
     * @param  int         $offset
     * @param  string|null $class
     * @return iterable|null
     */
    public function exec(string $input, int|array $flags = 0, int $offset = 0, string $class = null): iterable|null
    {
        $this->classCheck($class);
        $this->flagsCheck($flags);

        $res = @preg_match($this->pattern, $input, $ret, $flags, $offset);

        if ($res === false) {
            $this->processError('preg_match');
            $ret = null;
        } elseif ($ret) {
            $ret = array_filter_keys($ret, 'is_int')
                 + ['groups' => array_filter_keys($ret, 'is_string')];
        }

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Find a possible match.
     *
     * @param  string      $input
     * @param  bool        $named
     * @param  string|null $class
     * @return string|iterable|null
     */
    public function grep(string $input, bool $named = false, string $class = null): string|iterable|null
    {
        $this->classCheck($class);

        $ret = grep($this->pattern, $input, $named);

        if ($ret === null) {
            $this->processError('grep');
        }

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Find all possible matches.
     *
     * @param  string      $input
     * @param  bool        $named
     * @param  bool        $uniform
     * @param  string|null $class
     * @return iterable|null
     */
    public function grepAll(string $input, bool $named = false, bool $uniform = false, string $class = null): iterable|null
    {
        $this->classCheck($class);

        $ret = grep_all($this->pattern, $input, $named, $uniform);

        if ($ret === null) {
            $this->processError('grep_all');
        }

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Perform a test.
     *
     * @param  string $input
     * @return bool
     */
    public function test(string $input): bool
    {
        $ret = @preg_match($this->pattern, $input);

        if ($ret === false) {
            $this->processError('preg_match');
        }

        return (bool) $ret;
    }

    /**
     * Perform a search (like JavaScript search).
     *
     * @param  string $input
     * @param  bool   $unicode
     * @return int
     */
    public function search(string $input, bool $unicode = true): int
    {
        $ret = @preg_match($this->pattern, $input, $match, PREG_OFFSET_CAPTURE);

        if ($ret === false) {
            $this->processError('preg_match');
        }

        if (isset($match[0][0], $match[0][1])) {
            [$found, $offset] = $match[0];

            // For proper unicode check, "ui" modifiers must be used.
            if ($unicode) {
                $offset = str_contains($this->modifiers, 'i')
                    ? mb_stripos($input, $found) : mb_strpos($input, $found);
            }

            return $offset;
        }

        return -1;
    }

    /**
     * Quote given input.
     *
     * @param  string                    $input
     * @param  string|array<string>|null $delimiter
     * @return string
     */
    public static function quote(string $input, string|array $delimiter = null): string
    {
        if (is_array($delimiter)) {
            $input = preg_quote($input); // Regular chars.
            $input = addcslashes($input, join($delimiter));
        } else {
            $input = preg_quote($input, $delimiter);
        }

        return $input;
    }

    /**
     * Escape given input.
     *
     * Note: Sould not be used with `prepare()` method.
     *
     * @param  string                    $input
     * @param  string|array<string>|null $delimiter
     * @return string
     */
    public static function escape(string $input, string|array $delimiter = null): string
    {
        $chars = "\r\n\t\v\f\0";
        $delim = self::DELIMITER;

        $input = self::quote($input, $delimiter);

        // Prevent double escape of delim.
        if (!str_contains($input, '\\' . $delim)) {
            $chars .= $delim;
        }

        if (strpbrk($input, $chars) !== false) {
            $input = addcslashes($input, $chars);
        }

        return $input;
    }

    /**
     * Prepare given source as pattern.
     *
     * Note: Sould not be used with `escape()` method.
     *
     * @param  string $source
     * @param  string $modifiers
     * @param  bool   $quote
     * @return string
     */
    public static function prepare(string $source, string $modifiers = '', bool $quote = false): string
    {
        $chars = "\r\n\t\v\f\0";
        $delim = self::DELIMITER;

        $quote && $source = self::quote($source);

        // Prevent double escape of delim.
        if (!str_contains($source, '\\' . $delim)) {
            $chars .= $delim;
        }

        if (strpbrk($source, $chars) !== false && !str_contains($modifiers, 'x')) {
            $source = addcslashes($source, $chars);
        }

        return $delim . $source . $delim . $modifiers;
    }

    /**
     * Prepare modifiers, extract if any invalid.
     *
     * @param  string      $modifiers
     * @param  string|null &$invalids
     * @return string|null
     */
    public static function prepareModifiers(string $modifiers, string &$invalids = null): string|null
    {
        $mods = array_unique(split('', $modifiers));
        $diff = array_diff($mods, self::MODIFIERS);

        if ($diff) {
            $invalids = join($diff);
            return null;
        }

        return join($mods);
    }

    /**
     * Create an instance using given source/modifiers.
     *
     * @param  string $source
     * @param  string $modifiers
     * @param  bool   $throw
     * @return static
     */
    public static function from(string $source, string $modifiers = '', bool $throw = false): static
    {
        return new static($source, $modifiers, $throw);
    }

    /**
     * Create an instance using given pattern.
     *
     * @param  string $pattern
     * @param  bool   $throw
     * @return static
     * @throws RegExpError
     */
    public static function fromPattern(string $pattern, bool $throw = false): static
    {
        $delimiter = $pattern[0] ?? '';
        if (!$delimiter) {
            throw new RegExpError('No begin delimiter');
        }

        $pos = strrpos($pattern, $delimiter, 1);
        if (!$pos) {
            throw new RegExpError('No end delimiter %q', $delimiter);
        }

        return new static(
            source: substr($pattern, 1, $pos - 1),
            modifiers: substr($pattern, $pos + 1),
            throw: $throw
        );
    }

    /**
     * Prepare pattern.
     */
    private function preparePattern(string $source, string $modifiers): string
    {
        return self::prepare($source, $modifiers);
    }

    /**
     * Process error if any occurs.
     */
    private function processError(string $func = ''): void
    {
        if ($message = preg_error_message($code, $func, true)) {
            $this->error = new RegExpError($message, code: $code);
            $this->errorCode = $code;
            if ($this->throw) {
                throw $this->error;
            }
        }
    }

    /**
     * Class check for valid/exists/iterable classes.
     */
    private function classCheck(string|null $class): void
    {
        if ($class !== null) {
            if ($class === '') {
                throw new RegExpError('Empty class given');
            }
            if (!class_exists($class)) {
                throw new RegExpError('No class exists such %q', $class);
            }
            if (!class_extends($class, 'Traversable')) {
                throw new RegExpError('Class %q must be an iterable', $class);
            }
        }
    }

    /**
     * Flags check for array flags, to calculate when string[] given.
     */
    private function flagsCheck(int|array|null &$flags): void
    {
        if ($flags && is_array($flags)) {
            $flagsSum = 0;

            foreach ($flags as $flag) {
                if (is_string($flag)) {
                    $constant = 'RegExp::' . strtoupper($flag);
                    defined($constant) || throw new RegExpError(
                        'No constant exists such %s',
                        $constant
                    );

                    $flagsSum |= constant($constant);
                } elseif (is_int($flag)) {
                    $flagsSum |= $flag;
                } else {
                    throw new RegExpError(
                        'Invalid flag type %t [valids: string,int]',
                        $flag
                    );
                }
            }

            // Update.
            $flags = $flagsSum;
        }
    }
}

/**
 * A class for match stuff of RegExp class.
 *
 * @package global
 * @class   RegExpMatch
 * @author  Kerem Güneş
 * @since   6.0
 */
class RegExpMatch extends Map
{
    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Clean filtering "" and null values.
     *
     * @return self
     */
    public function clean(): self
    {
        return $this->filter(fn($v): bool => !equals($v, "", null));
    }

    /**
     * Clear filtering given search values.
     *
     * @param  mixed ...$search
     * @return self
     */
    public function clear(mixed ...$search): self
    {
        return $this->filter(fn($v): bool => !equals($v, ...$search));
    }
}
