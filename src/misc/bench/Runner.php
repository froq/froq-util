<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\bench;

/**
 * A class, runs given callables, optionally prints time & memory peeks.
 *
 * @package froq\util\bench
 * @class   froq\util\bench\Runner
 * @author  Kerem Güneş
 * @since   5.5
 */
class Runner
{
    /** Run limit. */
    private int $limit = 0;

    /** Total runs. */
    private int $runs = 0;

    /**
     * Constructor.
     *
     * @param  int $limit
     * @causes ArgumentError
     */
    public function __construct(int $limit = 1000)
    {
        $this->limit($limit);
    }

    /**
     * Set/update limit.
     *
     * @param  int $limit
     * @return self
     * @throws ArgumentError
     */
    public function limit(int $limit): self
    {
        if ($limit < 1) {
            throw new \ArgumentError('Min limit is 1, %d given', $limit);
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * Run given function.
     *
     * @param  callable $func
     * @param  bool     $profile
     * @param  bool     $print
     * @param  bool     $simple
     * @return self
     */
    public function run(callable $func, bool $profile = true, bool $print = true, bool $simple = false): self
    {
        // Increase runs.
        $this->runs += 1;

        if ($profile) {
            $startMemo = memory_get_usage();
            $startTime = microtime(true);
        }

        $limit = $this->limit;
        $count = 1;

        while ($limit--) {
            // Temp is for memory measurement.
            $simple || $temp = $func($count++);
        }

        if ($profile) {
            $endMemo = memory_get_usage();
            $endTime = microtime(true) - $startTime;

            // Free.
            unset($temp);

            if ($print) {
                $formatLimit = fn(int $v): string => number_format($v, 0, '', ',');
                $formatBytes = fn(int $v): string => \froq\util\Util::formatBytes($v, 3);

                // Drop memory info.
                if ($simple) {
                    printf("run(%s)#%s: %F\n",
                        $formatLimit($this->limit), $this->runs, $endTime
                    );
                } else {
                    printf("run(%s)#%s: %F, memo: %s (%s - %s)\n",
                        $formatLimit($this->limit), $this->runs, $endTime,
                        $formatBytes($endMemo - $startMemo),
                        $formatBytes($endMemo), $formatBytes($startMemo)
                    );
                }
            }
        }

        return $this;
    }
}
