<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

/**
 * A class that provides a profiling interface via `run()` method printing
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
    private bool $simple;

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
            throw new \ValueError('Min limit is 1, ' . $limit . ' given');
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
            $startMemo = memory_get_usage();
            $startTime = microtime(true);
        }

        $limit  = $this->limit;
        $simple = $this->simple;

        while ($limit--) {
            // Temp is for memory measurement only.
            $simple || $temp = $func();
        }

        if ($profile) {
            $endMemo = memory_get_usage();
            $endTime = microtime(true) - $startTime;

            // Free.
            unset($temp);

            $formatRun = fn($v) => number_format($v, 0, '', ',');
            $formatMemo = fn($v) => \froq\util\Util::formatBytes($v, 3);

            // Simple drops memory info.
            if ($simple) {
                printf("run(%s)#%s: %F\n",
                    $formatRun($this->limit), $this->runs, $endTime,
                );
            } else {
                printf("run(%s)#%s: %F, memo: %s (%s - %s)\n",
                    $formatRun($this->limit), $this->runs, $endTime,
                    $formatMemo($endMemo - $startMemo),
                    $formatMemo($endMemo), $formatMemo($startMemo),
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
