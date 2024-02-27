<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * A class for playing with stack traces in OOP-way.
 *
 * @package global
 * @class   TraceStack
 * @author  Kerem Güneş
 * @since   6.0
 */
class TraceStack implements Stringable, Countable, IteratorAggregate, ArrayAccess
{
    /** Stack data. */
    public readonly array $stack;

    /**
     * Constructor.
     *
     * @param array|null $stack
     * @param int        $options
     * @param int        $limit
     * @param int        $slice
     * @param bool       $reverse
     */
    public function __construct(array $stack = null, int $options = 0, int $limit = 0, int $slice = 1, bool $reverse = false)
    {
        $stack ??= get_trace($options, $limit, slice: $slice, reverse: $reverse);

        $this->stack = array_filter($stack);
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        $ret = []; $index = -1;

        foreach ($this as $index => $trace) {
            if (empty($trace->data)) {
                continue;
            }

            $index = $trace->index ?? $index;
            $ret[] = sprintf('#%d %s', $index, $trace->call());
        }

        // Append {main} to end as original.
        $ret[] = sprintf('#%d {main}', $index + 1);

        return join("\n", $ret);
    }

    /**
     * @magic
     */
    public function __debugInfo(): array
    {
        return $this->stack;
    }

    /**
     * Check a trace.
     *
     * @param  int $index
     * @return bool
     */
    public function has(int $index): bool
    {
        return isset($this->stack[$index]);
    }

    /**
     * Get a trace.
     *
     * @param  int $index
     * @return Trace|null
     */
    public function get(int $index): Trace|null
    {
        $trace = value($this->stack, $index);

        return $trace ? new Trace($trace) : null;
    }

    /**
     * Get first trace.
     *
     * @return Trace|null
     */
    public function getFirst(): Trace|null
    {
        $trace = first($this->stack);

        return $trace ? new Trace($trace) : null;
    }

    /**
     * Get last trace.
     *
     * @return Trace|null
     */
    public function getLast(): Trace|null
    {
        $trace = last($this->stack);

        return $trace ? new Trace($trace) : null;
    }

    /**
     * @alias getFirst()
     */
    public function first()
    {
        return $this->getFirst();
    }

    /**
     * @alias getLast()
     */
    public function last()
    {
        return $this->getLast();
    }

    /**
     * Find a trace that satisfies the provided callable.
     *
     * @param  callable $func
     * @return Trace|null
     */
    public function find(callable $func): Trace|null
    {
        foreach ($this as $trace) {
            if ($func($trace)) {
                return $trace;
            }
        }
        return null;
    }

    /**
     * @inheritDoc Countable
     */
    public function count(): int
    {
        return count($this->stack);
    }

    /**
     * @inheritDoc IteratorAggregate
     * @return     Generator<Trace>
     */
    public function getIterator(): Generator
    {
        foreach ($this->stack as $entry) {
            yield new Trace($entry);
        }
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $index): bool
    {
        return $this->has($index);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet(mixed $index): Trace|null
    {
        return $this->get($index);
    }

    /**
     * @inheritDoc ArrayAccess
     * @throws     UnimplementedError
     */
    public function offsetSet(mixed $index, mixed $_): never
    {
        throw new UnimplementedError();
    }

    /**
     * @inheritDoc ArrayAccess
     * @throws     UnimplementedError
     */
    public function offsetUnset(mixed $index): never
    {
        throw new UnimplementedError();
    }
}

/**
 * An internal class for stack frames.
 *
 * @package global
 * @class   Trace
 * @author  Kerem Güneş
 * @since   6.0
 * @internal
 */
class Trace implements ArrayAccess
{
    /** Trace index. */
    public readonly int|null $index;

    /** Trace data. */
    public readonly array $data;

    /**
     * Constructor.
     *
     * @param array    $data
     * @param int|null $index
     */
    public function __construct(array $data, int $index = null)
    {
        // For throwable traces.
        if ($data && !isset($data['method']) && isset($data['class'], $data['function'])) {
            $data['method']     = $data['function'];
            $data['methodType'] = ($data['type'] === '::') ? 'static' : 'non-static';
        }

        if ($data) {
            $this->index = $index ?? $data['#'] ?? null;
            $this->data  = ['#' => $this->index] + $data;
        } else {
            $this->index = null;
            $this->data  = [];
        }
    }

    /**
     * @magic
     */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * @throws UnimplementedError
     * @magic
     */
    public function __set(string $key, mixed $_): never
    {
        throw new UnimplementedError();
    }

    /**
     * @magic
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    /**
     * Check a field.
     *
     * @param  string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Get a field.
     *
     * @param  string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return value($this->data, $key);
    }

    /**
     * Get field.
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function getField(string $key, mixed $default = null): mixed
    {
        return array_select($this->data, $key, $default);
    }

    /**
     * Get fields.
     *
     * @param  array      $keys
     * @param  array|null $defaults
     * @return array
     */
    public function getFields(array $keys, array $defaults = null): array
    {
        return array_select($this->data, $keys, $defaults);
    }

    /**
     * Get file.
     *
     * @return string|null
     */
    public function file(): string|null
    {
        return $this->getField('file');
    }

    /**
     * Get line.
     *
     * @return int|null
     */
    public function line(): int|null
    {
        return $this->getField('line');
    }

    /**
     * Get method.
     *
     * @return string|null
     */
    public function class(): string|null
    {
        return $this->getField('class');
    }

    /**
     * Get method.
     *
     * @return string|null
     */
    public function method(): string|null
    {
        return $this->getField('method');
    }

    /**
     * Get method type.
     *
     * @return string|null
     */
    public function methodType(): string|null
    {
        return $this->getField('methodType');
    }

    /**
     * Get call.
     *
     * @return string|null
     */
    public function call(): string|null
    {
        return $this->callPath(true);
    }

    /**
     * Get callee.
     *
     * @return string|null
     */
    public function callee(): string|null
    {
        return $this->getField('callee');
    }

    /**
     * Get caller.
     *
     * @return string|null
     */
    public function caller(): string|null
    {
        return $this->getField('caller');
    }

    /**
     * Get caller class.
     *
     * @return string|null
     */
    public function callerClass(): string|null
    {
        return $this->getField('callerClass');
    }

    /**
     * Get caller method.
     *
     * @return string|null
     */
    public function callerMethod(): string|null
    {
        return $this->getField('callerMethod');
    }

    /**
     * Get call path.
     *
     * @param  bool $full
     * @return string|null
     */
    public function callPath(bool $full = false): string|null
    {
        if (empty($this->data)) {
            return null;
        }

        $ret = $this->getField('callPath');

        if (!$ret) {
            $fields = $this->getFields(['file', 'line']);
            if (isset($fields[0], $fields[1])) {
                $ret = join(':', $fields);
            } else {
                $ret = join(':', ['[internal function]', '']);
            }
        }

        if ($full) {
            [$class, $function, $type] = $this->getFields(['class', 'function', 'type']);
            if ($class) {
                // Simple modification for TraceStack struct.
                if ($class === 'TraceStack' && $function === '__construct') {
                    [$class, $function, $type] = ['new TraceStack', '', ''];
                }

                $ret .= ' => '. $class . $type . $function .'()';
            } else {
                $ret .= ' => '. $function .'()';
            }
        }

        return $ret;
    }

    /**
     * Get object.
     *
     * @return object|null
     */
    public function object(): object|null
    {
        return $this->getField('object');
    }

    /**
     * Get function.
     *
     * @return string|null
     */
    public function function(): string|null
    {
        return $this->getField('function');
    }

    /**
     * Get function type.
     *
     * @return string|null
     */
    public function functionType(): string|null
    {
        return $this->getField('type');
    }

    /**
     * Get function arguments.
     *
     * @return array|null
     */
    public function functionArguments(): array|null
    {
        return $this->getField('args');
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetExists(mixed $key): bool
    {
        return $this->has($key);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetGet(mixed $key): mixed
    {
        return $this->get($key);
    }

    /**
     * @inheritDoc ArrayAccess
     * @throws     UnimplementedError
     */
    public function offsetSet(mixed $key, mixed $_): never
    {
        throw new UnimplementedError();
    }

    /**
     * @inheritDoc ArrayAccess
     * @throws     UnimplementedError
     */
    public function offsetUnset(mixed $key): never
    {
        throw new UnimplementedError();
    }
}
