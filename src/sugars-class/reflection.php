<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A reflection class that combines ReflectionMethod & ReflectionFunction as one class
 * and adds some other utility methods.
 *
 * @package froq\util
 * @object  ReflectionCallable
 * @author  Kerem Güneş
 * @since   5.27
 */
class ReflectionCallable implements Reflector
{
    use ReflectionCallableTrait;

    /** Reflection object reference. */
    private ReflectionMethod|ReflectionFunction $reflection;

    /**
     * Constructor.
     *
     * @param  string|array $callable
     * @causes ReflectionException
     */
    public function __construct(string|array $callable)
    {
        // When "Foo.bar" or "Foo::bar" given.
        if (is_string($callable) && strpbrk($callable, '.:')) {
            $callable = preg_split(
                '~(.+?)(?:[.:]+)(\w+)$~', $callable,
                limit: 2, flags: PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
            );
        }

        $this->reflection = is_array($callable)
            ? new ReflectionMethod(...$callable)
            : new ReflectionFunction($callable);
    }

    /**
     * Proxy for reflection object properties.
     *
     * @param  string $property
     * @return string
     * @throws Error
     */
    public function __get(string $property): string
    {
        // For name, class actually.
        if (property_exists($this->reflection, $property)) {
            return $this->reflection->$property;
        }

        throw new Error(sprintf(
            'Undefined property %s::$%s / %s::$%s',
            $this::class, $property, $this->reflection::class, $property
        ));
    }

    /**
     * Proxy for reflection object methods.
     *
     * @param  string $method
     * @param  array  $methodArgs
     * @return mixed
     * @throws Error
     */
    public function __call(string $method, array $methodArgs): mixed
    {
        // For all parent methods actually.
        if (method_exists($this->reflection, $method)) {
            return $this->reflection->$method(...$methodArgs);
        }

        throw new Error(sprintf(
            'Undefined method %s::$%s / %s::$%s',
            $this::class, $method, $this->reflection::class, $method
        ));
    }

    /**
     * Proxy for reflection object __toString().
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->reflection->__toString();
    }

    /**
     * Check whether reflection object is a method.
     *
     * @return bool
     */
    public function isMethod(): bool
    {
        return ($this->reflection instanceof ReflectionMethod);
    }

    /**
     * Check whether reflection object is a function.
     *
     * @return bool
     */
    public function isFunction(): bool
    {
        return ($this->reflection instanceof ReflectionFunction);
    }
}

/**
 * An extended ReflectionMethod class.
 *
 * @package froq\util
 * @object  ReflectionMethodExtended
 * @author  Kerem Güneş
 * @since   5.27
 */
class ReflectionMethodExtended extends ReflectionMethod
{
    use ReflectionCallableTrait;
}

/**
 * An extended ReflectionFunction class.
 *
 * @package froq\util
 * @object  ReflectionFunctionExtended
 * @author  Kerem Güneş
 * @since   5.27
 */
class ReflectionFunctionExtended extends ReflectionMethod
{
    use ReflectionCallableTrait;
}

/**
 * A trait that used by Reflection* classes.
 *
 * @package froq\util
 * @object  ReflectionCallableTrait
 * @author  Kerem Güneş
 * @since   5.27
 */
trait ReflectionCallableTrait
{
    /**
     * Getter method for reflection object.
     *
     * @return ReflectionMethod|ReflectionFunction
     */
    public function reflection(): ReflectionMethod|ReflectionFunction
    {
        return ($this instanceof ReflectionCallable) ? $this->reflection
            : $this; // For *Extended classes.
    }

    /**
     * Check whether reflection object has a parameter.
     *
     * @param  string $name
     * @return bool
     */
    public function hasParameter(string $name): bool
    {
        return $this->getParameter($name) != null;
    }

    /**
     * Get a reflection object parameter.
     *
     * @param  string $name
     * @return ReflectionParameter|null
     */
    public function getParameter(string $name): ReflectionParameter|null
    {
        foreach ($this->reflection()->getParameters() as $parameter) {
            if ($parameter->name == $name) {
                return $parameter;
            }
        }

        return null;
    }

    /**
     * Get a reflection object parameter names.
     *
     * @return array
     */
    public function getParameterNames(): array
    {
        return array_map(fn($p) => $p->name, $this->reflection()->getParameters());
    }

    /**
     * Get a reflection object parameter (default) values.
     *
     * @param  bool $assoc
     * @return array
     */
    public function getParameterValues(bool $assoc = false): array
    {
        $ret = [];
        foreach ($this->reflection()->getParameters() as $p) {
            try {
                if ($p->isDefaultValueAvailable()) {
                    $ret[$p->name] = $p->getDefaultValue();
                } elseif ($p->isDefaultValueConstant()) {
                    $ret[$p->name] = $p->getDefaultValueConstantName();
                }
            } catch (ReflectionException) {
                $ret[$p->name] = null(); // Fill with null object.
            }
        }

        return ($assoc ? $ret : array_values($ret));
    }
}
