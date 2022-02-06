<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * RegExp.
 *
 * A class for playing with regular expression stuff in OOP-way.
 *
 * @package froq\util
 * @object  RegExp
 * @author  Kerem Güneş
 * @since   6.0
 */
final class RegExp
{
    /** @const string */
    public const DELIMITER = '~';

    /** @const array<string> */
    public const MODIFIERS = ['i', 'm', 's', 'u', 'x', 'A', 'D', 'S', 'U', 'J', 'X'];

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
        if ($throw && strval($modifiers) !== '') {
            $modifiers = self::prepareModifiers($modifiers, $invalids);
            if (!$modifiers) {
                throw new RegExpError('Invalid modifiers `' . $invalids . '`');
            }
        }

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
        $ret =@ preg_filter($this->pattern, $replace, $input, $limit, $count);

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
     * @param  int|array              $flags
     * @param  string|null            $class
     * @return string|array|null
     */
    public function replace(string|array $input, string|array|callable $replace, int $limit = -1, int &$count = null,
        int|array $flags = 0, string $class = null): string|array|null
    {
        $this->classCheck($class);
        $this->flagsCheck($flags);

        $callback = is_callable($replace);

        // @cancel: Param $class added.
        // Append Map instance as second argument to given callback. @todo: Use XArray().
        // $callback && $replace = fn($match) => $replace($match, new $class($match));

        // Send class instance as match argument when a class given.
        if ($callback && $class) {
            $replace = fn($match) => $replace(new $class($match));
        }

        $ret =@ $callback ? preg_replace_callback($this->pattern, $replace, $input, $limit, $count, $flags)
            : preg_replace($this->pattern, $replace, $input, $limit, $count);

        if ($ret === null) {
            $this->processError();
        }

        return $ret;
    }

    /**
     * Preform a remove.
     *
     * @param  string|array  $input
     * @param  int           $limit
     * @param  int|null     &$count
     * @return string|array|null
     */
    public function remove(string|array $input, int $limit = -1, int &$count = null): string|array|null
    {
        if (is_string($input)) {
            $ret =@ preg_remove($this->pattern, $input, $limit, $count);

            if ($ret === null) {
                $this->processError();
            }

            return $ret;
        }

        $rets = null;

        foreach ($input as $input) {
            $ret =@ preg_remove($this->pattern, $input, $limit, $count);

            if ($ret === null) {
                $this->processError();
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

        $ret =@ preg_split($this->pattern, $input, $limit, flags: (
            $flags |= PREG_SPLIT_NO_EMPTY // Always..
        ));

        if ($ret === false) {
            $this->processError();
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
     * @return iterable|null
     */
    public function splitTo(string $input, string $class, int $limit = -1, int|array $flags = 0): iterable|null
    {
        return $this->split($input, $limit, $flags, $class);
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

        $res =@ preg_match($this->pattern, $input, $ret, $flags, $offset);

        if ($res === false) {
            $this->processError();
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

        $res =@ preg_match_all($this->pattern, $input, $ret, $flags, $offset);

        if ($res === false) {
            $this->processError();
            $ret = null;
        }

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Find possible matches (@see sugars.grep()).
     *
     * @param  string      $input
     * @param  bool        $named
     * @param  string|null $class
     * @return string|iterable|null
     */
    public function grep(string $input, bool $named = false, string $class = null): string|iterable|null
    {
        $this->classCheck($class);

        $ret = grep($input, $this->pattern, $named);

        if ($ret === null) {
            $this->processError();
        }

        return $class ? new $class((array) $ret) : $ret;
    }

    /**
     * Find all possible matches (@see sugars.grep_all()).
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

        $ret = grep_all($input, $this->pattern, $named, $uniform);

        if ($ret === null) {
            $this->processError();
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
        $ret =@ preg_match($this->pattern, $input);

        if ($ret === false) {
            $this->processError();
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
        $ret =@ preg_match($this->pattern, $input, $match, PREG_OFFSET_CAPTURE);

        if ($ret === false) {
            $this->processError();
        }

        if ($ret && isset($match[0][1])) {
            $offset = $match[0][1];

            /** @thanks http://php.net/preg_match#106804 */
            $unicode && $offset = strlen(utf8_decode(substr($input, 0, $offset)));

            return $offset;
        }

        return -1;
    }

    /**
     * Escape pattern with/without modifiers.
     * Note: Sould not be used with prepare().
     *
     * @param  string      $input
     * @param  string|null $delimiter
     * @return string
     */
    public static function escape(string $input, string $delimiter = null): string
    {
        $input = preg_quote($input, $delimiter);

        $chars = "\r\n\t\v\f\0";
        $delim = self::DELIMITER;

        if (!$delimiter || !str_contains($delimiter, $delim)) {
            $chars .= $delim;
        }

        // Prevent double escape.
        if (!str_contains($input, '\\' . $delim)) {
            $chars .= $delim;
        }

        if (strpbrk($input, $chars) !== false) {
            $input = addcslashes($input, $chars);
        }

        return $input;
    }

    /**
     * Prepare source as pattern with/without modifiers.
     * Note: Sould not be used with escape().
     *
     * @param  string      $source
     * @param  string|null $modifiers
     * @return string
     */
    public static function prepare(string $source, string $modifiers = null): string
    {
        $chars = "\r\n\t\v\f\0";
        $delim = self::DELIMITER;

        // Prevent double escape.
        if (!str_contains($source, '\\' . $delim)) {
            $chars .= $delim;
        }

        if (strpbrk($source, $chars) !== false) {
            $source = addcslashes($source, $chars);
        }

        return $delim . $source . $delim . $modifiers;
    }

    /**
     * Prepare modifiers.
     * Valids: imsuxADSUJX (@see http://php.net/manual/en/reference.pcre.pattern.modifiers.php).
     *
     * @param  string       $modifiers
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
     * @param  string      $source
     * @param  string|null $modifiers
     * @param  bool        $throw
     * @return static
     */
    public static function from(string $source, string $modifiers = null, bool $throw = false): static
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
            throw new RegExpError('No end delimiter ' . $delimiter);
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
    private function preparePattern(string $source, string|null $modifiers): string
    {
        return self::prepare($source, $modifiers);
    }

    /**
     * Process error if any occurs.
     */
    private function processError(string $func = ''): void
    {
        if ($message = preg_error_message($code, $func, true)) {
            $this->error = new RegExpError($message, $code);
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
                throw new RegExpError('No class exists such `' . $class . '`');
            }
            if (!class_extends($class, 'Traversable')) {
                throw new RegExpError('Class `' . $class . '` must be an iterable');
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
                    defined($constant) || throw new RegExpError(sprintf(
                        'No constant exists such `%s`',
                        $constant
                    ));

                    $flagsSum |= constant($constant);
                } elseif (is_int($flag)) {
                    $flagsSum |= $flag;
                } else {
                    throw new RegExpError(sprintf(
                        'Invalid flag type `%s` [valids: string, int]',
                        type($flag)
                    ));
                }
            }

            // Update.
            $flags = $flagsSum;
        }
    }
}

/**
 * RegExp Match.
 *
 * A class for match stuff of RegExp class.
 * @todo: Use XArray().
 *
 * @package froq\util
 * @object  RegExpMatch
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
     * Clean filtering "", null fields.
     *
     * @return self
     */
    public function clean(): self
    {
        return $this->filter(fn($v) => !equals($v, "", null));
    }

    /**
     * Clear filtering given search.
     *
     * @param  mixed ...$search
     * @return self
     */
    public function clear(mixed ...$search): self
    {
        return $this->filter(fn($v) => !equals($v, ...$search));
    }
}
