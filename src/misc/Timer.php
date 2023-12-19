<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

use froq\common\interface\Arrayable;

/**
 * A timer class, just like a stopwatch.
 *
 * @package froq\util
 * @class   froq\util\Timer
 * @author  Kerem Güneş
 * @since   6.0
 */
class Timer implements Arrayable
{
    /** Start time. */
    private ?float $start = null;

    /** Stop time. */
    private ?float $stop = null;

    /**
     * Constructor.
     *
     * @param bool $start
     */
    public function __construct(bool $start = true)
    {
        $start && $this->start();
    }

    /**
     * Start.
     *
     * @return self
     */
    public function start(): self
    {
        $this->start = microtime(true);

        return $this;
    }

    /**
     * Stop.
     *
     * @return self
     */
    public function stop(): self
    {
        $this->stop = microtime(true);

        return $this;
    }

    /**
     * Get start time or return null if not started yet.
     *
     * @return float|null
     */
    public function getStart(): float|null
    {
        return $this->start;
    }

    /**
     * Get stop time or return null if not stopped yet.
     *
     * @return float|null
     */
    public function getStop(): float|null
    {
        return $this->stop;
    }

    /**
     * Get (elapsed/duration) time or return null if not started yet.
     *
     * @param  int $precision
     * @return float|null
     */
    public function getTime(int $precision = 10): float|null
    {
        if ($this->start === null) {
            return null;
        }

        // If stop() not called yet.
        $stop = $this->stop ?? microtime(true);

        return round($stop - $this->start, $precision);
    }

    /**
     * Reset start/stop values.
     *
     * @return self
     */
    public function reset(): self
    {
        $this->start = null;
        $this->stop  = null;

        return $this;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return [
            'start' => $this->getStart(),
            'stop'  => $this->getStop(),
            'time'  => $this->getTime()
        ];
    }
}
