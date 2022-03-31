<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * Trace.
 *
 * A class for playing with traces in OOP-way.
 *
 * @package froq\util
 * @object  Trace
 * @author  Kerem Güneş
 * @since   6.0
 */
final class Trace implements Stringable, Countable, IteratorAggregate, ArrayAccess
{
    /** @var array */
    public readonly array $stack;

    /**
     * Constructor.
     *
     * @param array|null $stack
     * @param int        $options
     * @param int        $limit
     * @param int        $slice
     */
    public function __construct(array $stack = null, int $options = 0, int $limit = 0, int $slice = 1)
    {
        $stack ??= get_trace($options, $limit, slice: $slice);

        $this->stack = $stack;
    }

    /** @magic */
    public function __toString(): string
    {
        $ret = []; $entry = null;
        foreach ($this->getIterator() as $entry) {
            $ret[] = format('#%s %s', $entry->index, $entry->call());
        }

        // Dunno what does this actually..
        $ret[] = format('#%s {main}', $entry?->index + 1);

        return join("\n", $ret);
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return $this->stack;
    }

    /**
     * Check an entry.
     *
     * @param  int  $index
     * @return bool
     */
    public function has(int $index): bool
    {
        return isset($this->stack[$index]);
    }

    /**
     * Get an entry.
     *
     * @param  int $index
     * @return TraceEntry|null
     */
    public function get(int $index): TraceEntry|null
    {
        $entry = value($this->stack, $index);

        return $entry ? new TraceEntry($entry) : null;
    }

    /**
     * Get first entry.
     *
     * @return TraceEntry|null
     */
    public function getFirst(): TraceEntry|null
    {
        $entry = first($this->stack) ?: null;

        return $entry ? new TraceEntry($entry) : null;
    }

    /**
     * Get last entry.
     *
     * @return TraceEntry|null
     */
    public function getLast(): TraceEntry|null
    {
        $entry = last($this->stack) ?: null;

        return $entry ? new TraceEntry($entry) : null;
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
     * Get a reversed trace.
     *
     * @return Trace
     */
    public function reverse(): Trace
    {
        return new Trace(array_reverse($this->stack));
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
     */ #[\ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        foreach ($this->stack as $entry) {
            yield new TraceEntry($entry);
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
    public function offsetGet(mixed $index): TraceEntry|null
    {
        return $this->get($index);
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetSet(mixed $index, mixed $_): never
    {
        throw new UnimplementedError();
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $index): never
    {
        throw new UnimplementedError();
    }
}

/**
 * Trace Entry.
 *
 * An internal class for Trace entries.
 *
 * @package froq\util
 * @object  TraceEntry
 * @author  Kerem Güneş
 * @since   6.0
 * @internal
 */
final class TraceEntry implements ArrayAccess
{
    /** @var array */
    public readonly array $data;

    /** @var int|null */
    public readonly int|null $index;

    /**
     * Constructor.
     *
     * @param array    $data
     * @param int|null $index
     */
    public function __construct(array $data)
    {
        $this->data  = $data;
        $this->index = $data['#'] ?? null;
    }

    /** @magic */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /** @magic */
    public function __set(string $key, mixed $_): never
    {
        throw new UnimplementedError();
    }

    /** @magic */
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
        // When ref'ed  "Cannot modify readonly property TraceEntry::$data" error..
        $data = $this->data;

        return array_select($data, $key, $default);
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
        // When ref'ed  "Cannot modify readonly property TraceEntry::$data" error..
        $data = $this->data;

        return array_select($data, $keys, $defaults);
    }

    /**
     * @alias getField()
     */
    public function field(...$args)
    {
        return $this->getField(...$args);
    }

    /**
     * @alias getFields()
     */
    public function fields(...$args)
    {
        return $this->getFields(...$args);
    }

    /**
     * Get file.
     *
     * @return string
     */
    public function file(): string
    {
        return $this->getField('file');
    }

    /**
     * Get line.
     *
     * @return int
     */
    public function line(): int
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
     * @return string
     */
    public function call(): string
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
     * @return string
     */
    public function callPath(bool $full = false): string
    {
        $ret = $this->getField('callPath');

        if ($full) {
            [$class, $function, $type] = $this->getFields(['class', 'function', 'type']);
            if ($class) {
                // Simple modification for Trace struct.
                if ($class == 'Trace' && $function == '__construct') {
                    $class = 'new Trace'; $function = $type = '';
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
     * @return string
     */
    public function function(): string
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
     * Extract.
     *
     * @param  array     $keys
     * @param  mixed &...$vars
     * @return int
     */
    public function extract(int|string|array $keys, mixed &...$vars): int
    {
        return array_extract($this->data, $keys, ...$vars);
    }

    /**
     * Export.
     *
     * @param  mixed &...$vars
     * @return int
     */
    public function export(mixed &...$vars): int
    {
        return array_export($this->data, ...$vars);
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
     */
    public function offsetSet(mixed $key, mixed $_): never
    {
        throw new UnimplementedError();
    }

    /**
     * @inheritDoc ArrayAccess
     */
    public function offsetUnset(mixed $key): never
    {
        throw new UnimplementedError();
    }
}
