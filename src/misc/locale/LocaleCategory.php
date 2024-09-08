<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\locale;

use froq\common\interface\Arrayable;

/**
 * Locale category class.
 *
 * @package froq\util\locale
 * @class   froq\util\locale\LocaleCategory
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleCategory implements Arrayable, \Stringable
{
    /** Type/value constants. */
    public final const ALL      = LC_ALL,
                       CTYPE    = LC_CTYPE,
                       TIME     = LC_TIME,
                       COLLATE  = LC_COLLATE,
                       NUMERIC  = LC_NUMERIC,
                       MONETARY = LC_MONETARY,
                       MESSAGES = LC_MESSAGES;

    /** Name, set by given value. */
    public readonly string $name;

    /** Value of this category. */
    public readonly int $value;

    /**
     * Constructor.
     *
     * @param  int|string $category
     * @throws froq\util\locale\LocaleException
     */
    public function __construct(int|string $category)
    {
        if (is_int($category)) {
            $name  = self::map(true)[$category] ?? null;
            $value = $category;
        } else {
            $name  = strtoupper($category);
            $value = self::map()[$name] ?? null;
        }

        if (!isset($name, $value)) {
            throw LocaleException::forInvalidCategory($category);
        }

        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return 'LC_' . $this->name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get value.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value
        ];
    }

    /**
     * Create from given locale category.
     *
     * @param  int|string $category
     * @return LocaleCategory
     */
    public static function from(int|string $category): LocaleCategory
    {
        // When LC_* constant given.
        if (is_string($category) && strpfx($category, 'LC_', true)) {
            $category = strcut($category, 3);
        }

        return new LocaleCategory($category);
    }

    /**
     * List all locale categories.
     *
     * @return LocaleCategoryList<LocaleCategory>
     */
    public static function list(): LocaleCategoryList
    {
        $items = [];
        $class = new \XClass(self::class);

        foreach (self::map() as $name => $value) {
            $items[] = $class->sample(name: $name, value: $value);
        }

        return new LocaleCategoryList($items);
    }

    /**
     * Get constants map, creating for once.
     *
     * @param  bool $flip
     * @return array
     */
    public static function map(bool $flip = false): array
    {
        static $map;
        $map ??= get_class_constants(self::class, false);

        return $flip ? array_flip($map) : $map;
    }
}

