<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

/**
 * A class for factoring class instances dynamically.
 *
 * @package global
 * @class   Factory
 * @author  Kerem Güneş
 * @since   6.0
 */
class Factory
{
    /** Class name. */
    private string $class = '';

    /** Class constructor arguments. */
    private array $classArgs = [];

    /** Class variables. */
    private array $classVars = [];

    /** Class instances. */
    private static array $instances = [];

    /**
     * Constructor.
     *
     * @param string $class     The target class (fully qualified class name).
     * @param array  $classArgs The target class arguments (constructor parameters).
     */
    public function __construct(string $class = '', array $classArgs = [])
    {
        $this->class = $class;
        $this->classArgs = $classArgs;
    }

    /**
     * When a modifier method is not defined in subclass (eg: `withId()`) this method
     * will handle such these methods dynamically using `with()` method and returning
     * self instance for chaining functionality.
     *
     * Note: All indicated properties must be defined in target class and all absent
     * methods must be prefixed as "with". So, calling for example `withId(123)` for
     * `User::class` that not defines `$id` property, will cause `Error`.
     *
     * To restrict factory process with strict types and/or bypass this magic method,
     * this class can be extended and all "with" methods can be defined in strict mode.
     * For example, say we're going to build user objects via a custom factory class,
     * this factory class can be declared & used like:
     *
     * ```
     * class UserFactory extends Factory {
     *   function __construct() {
     *     parent::__construct(User::class);
     *   }
     *
     *   function withId(int $id): self {
     *     return $this->with('id', $id);
     *   }
     * }
     *
     * $user = UserFactory::new()
     *   ->withId(1)
     *   ->init();
     * ```
     *
     * @param  string $name
     * @param  array  $arguments
     * @return self
     * @throws Error
     * @magic
     */
    public function __call(string $name, array $arguments = []): self
    {
        if (!str_starts_with($name, 'with')) {
            throw new Error(sprintf(
                'Invalid call as %s::%s()', static::class, $name
            ));
        }

        // Eg: withId(1) => id=1.
        $name = lcfirst(substr($name, 4));

        return $this->with($name, $arguments);
    }

    /**
     * Get class.
     *
     * @return string
     */
    public function class(): string
    {
        return $this->class;
    }

    /**
     * Get class args.
     *
     * @return array
     */
    public function classArgs(): array
    {
        return $this->classArgs;
    }

    /**
     * Get class vars.
     *
     * @return array
     */
    public function classVars(): array
    {
        return $this->classVars;
    }

    /**
     * Add given property into class property map to use in `init()` or `initOnce()`
     * method later.
     *
     * Note: To handle multi-calls (eg: `withOption(..)->withOption(..)`), if a property
     * was set before, new arguments will be merged with existing one/ones.
     *
     * @param  string $name
     * @param  mixed  $arguments
     * @return self
     */
    public function with(string $name, mixed $arguments): self
    {
        // Normalize as array:
        // 1) Type "mixed" lets subclasses call this method in custom "with*" methods using
        //    any type of data as "arguments").
        // 2) Variables stored as array to handle setter method calls (if defined in subclasses),
        //    see setObjectVar().
        $arguments = is_array($arguments) ? $arguments : [$arguments];

        // For multi-calls (eg: withOption(..)->withOption(..)).
        if (isset($this->classVars[$name])) {
            $arguments = [...$this->classVars[$name], ...$arguments];
        }

        $this->classVars[$name] = $arguments;

        return $this;
    }

    /**
     * Create an instance from given class or self `$class` with/without its arguments.
     *
     * @param  string $class
     * @param  array  $classArgs
     * @return object
     * @throws Error
     */
    public function init(string $class = '', array $classArgs = []): object
    {
        $class = $class ?: $this->class ?: throw new Error('No class given');
        $classArgs = $classArgs ?: $this->classArgs;

        $object = new $class(...$classArgs);

        foreach ($this->classVars as $name => $arguments) {
            $this->setObjectVar($object, $name, $arguments);
        }

        return $object;
    }

    /**
     * Create an instance from given class or self `$class` as singleton with/without
     * its arguments and cache it or return cached one that was previously created.
     *
     * @param  string $class
     * @param  array  $classArgs
     * @return object
     * @throws Error
     */
    public function initOnce(string $class = '', array $classArgs = []): object
    {
        $class = $class ?: $this->class ?: throw new Error('No class given');

        return self::$instances[$class] ??= $this->init($class, $classArgs);
    }

    /**
     * Static initializer.
     *
     * @param  mixed ...$args
     * @return static
     */
    public static function new(mixed ...$args): static
    {
        return new static(...$args);
    }

    /**
     * Set initiated object variables (properties).
     *
     * @throws Error
     */
    private function setObjectVar(object $object, string $name, array $arguments): void
    {
        // Check for setter method.
        if (method_exists($object, $method = ('set' . $name))) {
            $ref = new ReflectionMethod($object, $method);

            // Call setter method chunking by argument count.
            $argsList = chunk($arguments, $ref->getNumberOfParameters());
            each($argsList, fn($args) => $ref->invokeArgs($object, $args));
            return;
        }
        // Check for existence.
        elseif (property_exists($object, $name)) {
            $ref = new ReflectionProperty($object, $name);

            // Set property using first item
            $ref->setValue($object, first($arguments));
            return;
        }

        throw new Error(sprintf(
            'No method set%s() or property $%s is defined in class %s',
            $name, $name, $object::class
        ));
    }
}
