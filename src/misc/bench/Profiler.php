<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\bench;

use froq\util\Util;

/**
 * A class, profiles callables / creates profile markers.
 *
 * @package froq\util\bench
 * @class   froq\util\bench\Profiler
 * @author  Kerem Güneş
 * @since   7.9
 */
class Profiler
{
    /** Marks map. */
    private static array $marks = [];

    /**
     * Profile a callable, return profile result.
     *
     * @param  callable $func
     * @param  array    $funcArgs
     * @return froq\util\bench\ProfileResult
     */
    public static function profile(callable $func, array $funcArgs = []): ProfileResult
    {
        $id = self::mark();

        // Temp is for memory measurement.
        $temp = $func(...$funcArgs);

        // Drop exponent (of end-start).
        usleep(100);

        $mark = self::unmark($id);

        return $mark->result;
    }

    /**
     * Create a profile mark with given or generated id, and store it.
     *
     * Note: This method generates a UUID no id given on call-time, so
     * returned id can be kept and used to fetch a marker.
     *
     * @param  string|null $id
     * @return string
     */
    public static function mark(string $id = null): string
    {
        $mark = new ProfileMark($id ??= uuid());
        $mark->start();

        // Store.
        self::$marks[$id] = $mark;

        return $id;
    }

    /**
     * Fetch a profile mark with given id if exists, and unstore it.
     *
     * @param  string $id
     * @return froq\util\bench\ProfileMark|null
     */
    public static function unmark(string $id): ProfileMark|null
    {
        if (isset(self::$marks[$id])) {
            $mark = self::$marks[$id];
            $mark->end();

            // Clear mark.
            unset(self::$marks[$id]);

            return $mark;
        }

        return null;
    }

    /**
     * Clear marks.
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$marks = [];
    }
}

/**
 * Profile result class.
 *
 * @internal
 */
readonly class ProfileResult
{
    public function __construct(
        public TimeProfile $time,
        public MemoryProfile $memory
    )
    {}

    /**
     * Print profile result.
     */
    public function print(): void
    {
        [$time, $memory, $precision]
            = [$this->time->total, (int) $this->memory->total, 3];

        printf(
            "Time: %f, Memory: %s (%s - %s)\n",
            $time, Util::formatBytes($memory, $precision),
            Util::formatBytes($this->memory->end, $precision),
            Util::formatBytes($this->memory->start, $precision)
        );
    }
}

/**
 * Profile mark class.
 *
 * @internal
 */
readonly class ProfileMark
{
    public function __construct(
        public string $id,
        public ProfileResult $result = new ProfileResult(
            new TimeProfile, new MemoryProfile,
        )
    )
    {}

    /**
     * Start profile.
     */
    public function start(): void
    {
        $this->result->time->start();
        $this->result->memory->start();
    }

    /**
     * End profile.
     */
    public function end(): void
    {
        $this->result->time->end();
        $this->result->memory->end();
    }
}

/**
 * Base profile class of `TimeProfile`, `MemoryProfile` classes.
 *
 * @internal
 */
readonly class Profile
{
    public int|float $start;
    public int|float $end;
    public int|float $total;

    // Because "Cannot initialize readonly property .. from scope ..".
    protected function _start(int|float $start): void
    {
        $this->start = $start;
    }

    // Because "Cannot initialize readonly property .. from scope ..".
    protected function _end(int|float $end): void
    {
        $this->end   = $end;
        $this->total = round($end - $this->start, 10);
    }
}

/**
 * Time profile class.
 *
 * @internal
 */
readonly class TimeProfile extends Profile
{
    /**
     * Start profile.
     */
    public function start(): void
    {
        $this->_start(microtime(true));
    }

    /**
     * End profile.
     */
    public function end(): void
    {
        $this->_end(microtime(true));
    }
}

/**
 * Memory profile class.
 *
 * @internal
 */
readonly class MemoryProfile extends Profile
{
    /**
     * Start profile.
     */
    public function start(): void
    {
        $this->_start(memory_get_usage());
    }

    /**
     * End profile.
     */
    public function end(): void
    {
        $this->_end(memory_get_usage());
    }
}
