<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

namespace froq\util\misc;

use froq\common\interface\Arrayable;

/**
 * A timer class just like a stopwatch.
 *
 * @package froq\util\misc
 * @object  froq\util\misc\Timer
 * @author  Kerem Güneş
 * @since   6.0
 */
class Timer implements Arrayable
{
    /** @var ?float */
    private ?float $start = null;

    /** @var ?float */
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
     * @return ?float
     */
    public function getStart(): ?float
    {
        return $this->start;
    }

    /**
     * Get stop time or return null if not stopped yet.
     *
     * @return ?float
     */
    public function getStop(): ?float
    {
        return $this->stop;
    }

    /**
     * Get (elapsed) time or return null if not started yet.
     *
     * @return ?float
     */
    public function getTime(int $precision = 10): ?float
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
        $this->start = $this->stop = null;

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
