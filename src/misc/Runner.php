<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

use froq\util\UtilException;

/**
 * Runner.
 *
 * Represents a profiler class entity that provides a profiling interface via `run()` method printing
 * speed & memory peeks.
 *
 * @package froq\util\misc
 * @object  froq\util\misc\Runner
 * @author  Kerem Güneş
 * @since   5.5
 */
final class Runner
{
    /** @var int, int */
    private int $limit, $runs = 0;

    /** @var bool */
    private bool $simple = false;

    /**
     * Constructor.
     *
     * @param  int  $limit
     * @param  bool $simple
     * @throws froq\util\UtilException
     */
    public function __construct(int $limit = 1000, bool $simple = false)
    {
        if ($limit < 1) {
            throw new UtilException('Min limit is 1, ' . $limit . ' given');
        }

        $this->limit  = $limit;
        $this->simple = $simple;
    }

    /**
     * Run given function by self limit.
     *
     * @param  callable $func
     * @param  bool     $profile
     * @return self
     */
    public function run(callable $func, bool $profile = true): self
    {
        // Increase run(time)s.
        $this->runs += 1;

        if ($profile) {
            $startMem = memory_get_usage();
            $start    = microtime(true);
        }

        $limit  = $this->limit;
        $simple = $this->simple;

        while ($limit--) {
            // Temp is for memory measurement only.
            $simple || $temp = $func();
        }

        if ($profile) {
            $endMem = memory_get_usage();
            $end    = microtime(true) - $start;
            $temp   = null;

            // Simple drops memory info.
            if ($simple) {
                printf("run(%d)#%d: %F\n",
                    $this->limit, $this->runs, $end,
                );
            } else {
                printf("run(%d)#%d: %F, mem: %d (%d-%d)\n",
                    $this->limit, $this->runs, $end,
                    $endMem - $startMem, $endMem, $startMem,
                );
            }
        }

        return $this;
    }

    /**
     * Create an instance with given limit.
     *
     * @param  int  $limit
     * @param  bool $simple
     * @return self
     */
    public static function limit(int $limit, bool $simple = false): self
    {
        return new self($limit, $simple);
    }

    /**
     * Create an instance with/without given limit.
     *
     * @param  int  $limit
     * @param  bool $simple
     * @return self
     */
    public static function init(int $limit = 1000, bool $simple = false): self
    {
        return new self($limit, $simple);
    }
}
