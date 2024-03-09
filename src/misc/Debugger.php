<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util;

use Throwable, TraceStack;

/**
 * Debugger class for throwables.
 *
 * @package froq\util
 * @class   froq\util\Debugger
 * @author  Kerem Güneş
 * @since   7.0
 * @static
 */
class Debugger
{
    /**
     * Make a debug array for given throwable.
     *
     * @param  Throwable $e
     * @param  bool      $withTrace
     * @param  bool      $withTracePath
     * @param  bool      $withTraceString
     * @param  bool      $dots
     * @return array
     */
    public static function debug(Throwable $e, bool $withTrace = false, bool $withTracePath = true,
        bool $withTraceString = false, bool $dots = false): array
    {
        /** @var Throwable|null */
        $cause = $e->cause ?? null;

        if ($cause instanceof Throwable) {
            $cause = self::debug($cause, $withTrace, $withTracePath, $withTraceString, $dots);
        }
        if ($previous = $e->getPrevious()) {
            $previous = self::debug($previous, $withTrace, $withTracePath, $withTraceString, $dots);
        }

        $class = get_class_name($e, escape: true);
        $path  = $e->getFile() .':'. $e->getLine();

        $dots && $class = str_replace('\\', '.', $class);

        $ret = [
            'string' => sprintf('%s(%s): %s @%s', $class, $e->getCode(), $e->getMessage(), $path),
            'class'  => $class, 'message'  => $e->getMessage(), 'code' => $e->getCode(),
            'path'   => $path,  'file'     => $e->getFile(),    'line' => $e->getLine(),
            'cause'  => $cause, 'previous' => $previous,
        ];

        if ($withTrace) {
            $ret += ['trace' => $e->getTrace()];
        }
        if ($withTracePath) {
            $ret += ['tracePath' => self::debugTracePath($e, $dots)];
        }
        if ($withTraceString) {
            $ret += ['traceString' => self::debugTraceString($e, $dots)];
        }

        return $ret;
    }

    /**
     * Make a debug string for given throwable.
     *
     * @param  Throwable $e
     * @param  bool      $dots
     * @return string
     */
    public static function debugString(Throwable $e, bool $dots = false): string
    {
        /** @var Throwable|null */
        $cause = $e->cause ?? null;

        $class = get_class_name($e, escape: true);

        $ret = sprintf(
            "%s(%s): %s @%s:%d\nTrace:\n%s",
            $class, $e->getCode(), $e->getMessage(),
            $e->getFile(), $e->getLine(), self::debugTraceString($e, $dots)
        );

        if ($cause instanceof Throwable) {
            $ret .= "\n\nCause:\n" . self::debugString($cause, $dots);
        }
        if ($previous = $e->getPrevious()) {
            $ret .= "\n\nPrevious:\n" . self::debugString($previous, $dots);
        }

        return $ret;
    }

    /**
     * Make a trace paths for given throwable.
     *
     * @param  Throwable $e
     * @param  bool      $dots
     * @return array
     */
    public static function debugTracePath(Throwable $e, bool $dots = false): array
    {
        $traces = new TraceStack($e->getTrace());

        $ret = [];

        foreach ($traces as $trace) {
            $path = $trace->callPathFull();

            if ($dots) {
                $path = str_replace(['\\', '::', '->'], '.', $path);
            }

            $ret[] = $path;
        }

        return $ret;
    }

    /**
     * Make a trace string for given throwable.
     *
     * @param  Throwable $e
     * @param  bool      $dots
     * @return string
     */
    public static function debugTraceString(Throwable $e, bool $dots = false): string
    {
        $traces = new TraceStack($e->getTrace());

        $ret = (string) $traces;

        if ($dots) {
            $ret = str_replace(['\\', '::', '->'], '.', $ret);
        }

        return $ret;
    }

    /**
     * Make trace.
     *
     * @param  array|TraceStack|null $trace
     * @param  int                   $slice
     * @return TraceStack
     */
    public static function makeTrace(array|TraceStack $trace = null, int $slice = 1): TraceStack
    {
        if (is_array($trace)) {
            $ret = new TraceStack($trace, slice: $slice);
        } else {
            $ret ??= new TraceStack(null, slice: $slice);
        }

        return $ret;
    }

    /**
     * Print trace.
     *
     * @param  array|TraceStack|null $trace
     * @param  int                   $slice
     * @param  bool                  $return
     * @return string|null
     */
    public static function printTrace(array|TraceStack $trace = null, int $slice = 3, bool $return = !false): string|null
    {
        $ret = (string) self::makeTrace($trace, slice: $slice);

        if ($return) {
            return $ret;
        }

        print $ret;
        return null;
    }
}
