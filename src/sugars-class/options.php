<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A class for options related works.
 *
 * @package global
 * @object  Options
 * @author  Kerem Güneş
 * @since   6.0
 */
class Options extends XArrayObject
{
    /** @var array */
    private array $defaults = [];

    /**
     * Constructor.
     *
     * @param  array|null $options
     * @param  array|null $defaults
     * @param  bool       $map
     */
    public function __construct(array $options = null, array $defaults = null, bool $map = true)
    {
        if ($options || $defaults) {
            $this->defaults = $defaults ?? [];
            parent::__construct(array_options($options, $defaults, map: $map));
        }
    }

    /**
     * Check an option.
     *
     * @param  string $key
     * @return bool
     */
    public function hasOption(string $key): bool
    {
        return $this->has($key);
    }

    /**
     * Set an option.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return self
     */
    public function setOption(string $key, mixed $value): self
    {
        return $this->set($key, $value);
    }

    /**
     * Get an option.
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed|null
     */
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $default);
    }

    /**
     * Remove an option.
     *
     * @param  string $key
     * @return self
     */
    public function removeOption(string $key): self
    {
        return $this->remove($key);
    }

    /**
     * Set defaults.
     *
     * @param  array $defaults
     * @return self
     */
    public function setDefaults(array $defaults): self
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * Get defaults.
     *
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Filter self data dropping unknown/undefined fields by given defaults or self defaults.
     *
     * @param  array|null $defaults
     * @param  bool       $recursive
     * @return self
     */
    public function filterDefaults(array $defaults = null, bool $recursive = false): self
    {
        $defaults ??= $this->defaults;

        return $this->filterKeys(fn($key) => array_key_exists($key, $defaults), $recursive);
    }

    /**
     * Resolve given options by self defaults.
     *
     * Note: With empty defaults, this method is useless.
     *
     * @param  array $options
     * @return array
     */
    public function resolve(array $options): array
    {
        $array = [];

        foreach (array_keys($this->defaults) as $key) {
            $array[$key] = array_key_exists($key, $options)
                ? $options[$key] : $this->defaults[$key];
        }

        return $array;
    }

    /**
     * Select an option item or many.
     *
     * @param  int|string|array $key
     * @param  mixed|null       $default
     * @param  bool             $drop
     * @param  bool             $combine
     * @return mixed
     */
    public function select(int|string|array $key, mixed $default = null, bool $drop = false, bool $combine = false): mixed
    {
        $array = $this->getData();
        $value = array_select($array, $key, $default, $drop, $combine);

        // Update modified data.
        $drop && $this->setData($array);

        return $value;
    }
}
