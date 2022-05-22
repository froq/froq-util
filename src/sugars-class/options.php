<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

/**
 * A class for options-related works.
 *
 * @package froq\util
 * @object  Options
 * @author  Kerem Güneş
 * @since   6.0
 */
class Options extends XArrayObject
{
    /**
     * Constructor.
     *
     * @param  array|self|null $options
     * @param  array|self|null $optionsDefault
     * @param  bool            $map
     */
    public function __construct(array|self|null $options, array|self|null $optionsDefault = null, bool $map = true)
    {
        parent::__construct(array_options((array) $options, (array) $optionsDefault, map: $map));
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
     * @return mixed
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

    /**
     * Filter default keys dropping unknown/undefined fields.
     *
     * @param  array $optionsDefault
     * @return self
     */
    public function filterDefaultKeys(array $optionsDefault): self
    {
        return $this->filterKeys(fn($key) => array_key_exists($key, $optionsDefault));
    }
}
