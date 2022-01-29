<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * RegExp.
 *
 * A class for playing with regular expression stuff OOP-way.
 *
 * @package froq\util
 * @object  RegExp
 * @author  Kerem Güneş
 * @since   6.0
 */
final class RegExp
{
    /** @const int */
    public const PATTERN_ORDER        = PREG_PATTERN_ORDER,
                 SET_ORDER            = PREG_SET_ORDER,
                 OFFSET_CAPTURE       = PREG_OFFSET_CAPTURE,
                 SPLIT_NO_EMPTY       = PREG_SPLIT_NO_EMPTY,
                 SPLIT_DELIM_CAPTURE  = PREG_SPLIT_DELIM_CAPTURE,
                 SPLIT_OFFSET_CAPTURE = PREG_SPLIT_OFFSET_CAPTURE,
                 UNMATCHED_AS_NULL    = PREG_UNMATCHED_AS_NULL;

    /** @const int */
    public const ERROR_NONE            = PREG_NO_ERROR,
                 ERROR_INTERNAL        = PREG_INTERNAL_ERROR,
                 ERROR_BACKTRACK_LIMIT = PREG_BACKTRACK_LIMIT_ERROR,
                 ERROR_RECURSION_LIMIT = PREG_RECURSION_LIMIT_ERROR,
                 ERROR_BAD_UTF8        = PREG_BAD_UTF8_ERROR,
                 ERROR_BAD_UTF8_OFFSET = PREG_BAD_UTF8_OFFSET_ERROR,
                 ERROR_JIT_STACKLIMIT  = PREG_JIT_STACKLIMIT_ERROR;

    /** @var string */
    public readonly string $source;

    /** @var string|null */
    public readonly string|null $modifiers;

    /** @var string */
    public readonly string $pattern;

    /** @var bool */
    private bool $throw = false;

    /** @var RegExpError|null */
    private RegExpError|null $error = null;

    /** @var int|null */
    private int|null $errorCode = null;

    /**
     * Constructor.
     *
     * @param  string      $source
     * @param  string|null $modifiers
     * @param  bool        $throw
     * @throws RegExpError
     */
    public function __construct(string $source, string $modifiers = null, bool $throw = false)
    {
        ($source === '') && throw new RegExpError('Empty source');

        $this->source    = $source;
        $this->modifiers = $modifiers;
        $this->pattern   = $this->preparePattern($source, $modifiers);
        $this->throw     = $throw; // False is silent mode.
    }

    /** @magic __toString() */
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
        return $this->error;
    }

    /**
     * Get error code.
     *
     * @return int|null
     */
    public function errorCode(): int|null
    {
        return $this->errorCode;
    }

    /**
     * Set/get throw option.
     *
     * @param  bool|null $throw
     * @return bool|self
     */
    public function throw(bool $throw = null): bool|self
    {
        if ($throw === null) {
            return $this->throw;
        }

        $this->throw = $throw;

        return $this;
    }

    /**
     * Perform a test.
     *
     * @param  string $input
     * @return bool
     */
    public function test(string $input): bool
    {
        $ret = preg_test($this->pattern, $input);

        if (!$ret) {
            $this->processError();
        }

        return $ret;
    }

    /**
     * Perform a match.
     *
     * @param  string $input
     * @param  int    $flags
     * @param  int    $offset
     * @return array|null
     */
    public function match(string $input, int $flags = 0, int $offset = 0): array|null
    {
        $ret = preg_match($this->pattern, $input, $match, $flags, $offset);

        if ($ret === false) {
            $this->processError();
            $match = null;
        }

        return $match;
    }

    /**
     * Perform a match-all.
     *
     * @param  string $input
     * @param  int    $flags
     * @param  int    $offset
     * @return array|null
     */
    public function matchAll(string $input, int $flags = 0, int $offset = 0): array|null
    {
        $ret = preg_match_all($this->pattern, $input, $match, $flags, $offset);

        if ($ret === false) {
            $this->processError();
            $match = null;
        }

        return $match;
    }

    /**
     * Perform a search & replace.
     *
     * @param  string|array  $input
     * @param  string|array  $replace
     * @param  int           $limit
     * @param  int|null     &$count
     * @return string|array|null
     */
    public function filter(string|array $input, string|array $replace, int $limit = -1, int &$count = null,
        ): string|array|null
    {
        $ret = preg_filter($this->pattern, $replace, $input, $limit, $count);

        if ($ret === null) {
            $this->processError();
        }

        return $ret;
    }

    /**
     * Perform a search & replace.
     *
     * @param  string|array           $input
     * @param  string|array|callable  $replace
     * @param  int                    $limit
     * @param  int|null              &$count
     * @param  int                    $flags
     * @return string|array|null
     */
    public function replace(string|array $input, string|array|callable $replace, int $limit = -1, int &$count = null,
        int $flags = 0): string|array|null
    {
        $ret = is_callable($replace)
             ? preg_replace_callback($this->pattern, $replace, $input, $limit, $count, $flags)
             : preg_replace($this->pattern, $replace, $input, $limit, $count);

        if ($ret === null) {
            $this->processError();
        }

        return $ret;
    }

    /**
     * Perform a split.
     *
     * @param  string $input
     * @param  int    $limit
     * @param  int    $flags
     * @return array|null
     */
    public function split(string $input, int $limit = -1, int $flags = 0): array|null
    {
        $ret = preg_split($this->pattern, $input, $limit, $flags);

        if ($ret === false) {
            $this->processError();
            $ret = null;
        }

        return $ret;
    }

    /**
     * Perform a split for given array-like class.
     *
     * @param  string $input
     * @param  string $class
     * @param  int    $limit
     * @param  int    $flags
     * @return object|null
     * @throws RegExpError
     */
    public function splitTo(string $input, string $class = 'Map', int $limit = -1, int $flags = 0): object|null
    {
        class_exists($class) || throw new RegExpError('Invalid class');

        $ret = $this->split($input, $limit, $flags);

        if (!$this->error) {
            return new $class($ret);
        }

        return null;
    }

    /**
     * Perform a search (like JavaScript search, but null on failure).
     *
     * @param  string    $input
     * @param  bool|null $unicode
     * @return int|null
     */
    public function search(string $input, bool $unicode = null): int|null
    {
        $unicode ??= $this->modifiers && str_contains($this->modifiers, 'u');
        $function  = $unicode ? mb_strpos(...) : strpos(...);

        if (($pos = $function($input, $this->source)) !== false) {
            return $pos;
        }
        return null;
    }

    /**
     * Find possible matches.
     *
     * @param  string $input
     * @param  bool   $named
     * @return string|array|null
     */
    public function grep(string $input, bool $named = false): string|array|null
    {
        $ret = grep($input, $this->pattern, $named);

        if ($ret === null) {
            $this->processError();
        }

        return $ret;
    }

    /**
     * Find all possible matches.
     *
     * @param  string $input
     * @param  bool   $named
     * @param  bool   $uniform
     * @return array|null
     */
    public function grepAll(string $input, bool $named = false, bool $uniform = false): array|null
    {
        $ret = grep_all($input, $this->pattern, $named, $uniform);

        if ($ret === null) {
            $this->processError();
        }

        return $ret;
    }

    /**
     * Escape special regular expression characters.
     *
     * @param  string      $input
     * @param  string|null $delimiter
     * @return string
     */
    public static function escape(string $input, string $delimiter = null): string
    {
        return preg_quote($input, $delimiter);
    }

    /**
     * Prepare pattern.
     */
    private function preparePattern(string $source, string|null $modifiers): string
    {
        return '~' . addcslashes($source, "~\r\n\t\v\0") . '~' . $modifiers;
    }

    /**
     * Process error if any occurs.
     */
    private function processError(string $func = ''): void
    {
        // Somehow this code disappears when error_get_last() called.
        $code = preg_last_error();

        if ($message = preg_error_message($func, clear: true)) {
            $this->error = new RegExpError($message, $code);
            $this->errorCode = $code;
            if ($this->throw) {
                throw $this->error;
            }
        }
    }
}
