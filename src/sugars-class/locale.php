<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
declare(strict_types=1);

use froq\common\interface\Arrayable;

/**
 * Locale class.
 *
 * @package global
 * @object  Locale
 * @author  Kerem Güneş
 * @since   6.0
 */
class Locale implements Arrayable, Stringable
{
    /** To use for parse/validate. */
    public const PATTERN = '~^
        (?<language>[a-zA-Z]{1,3})
        (?:_(?<country>[a-zA-Z]{2}))?
        (?:.(?<encoding>[a-zA-Z\d\-]+))?
        (?:@(?<currency>[a-zA-Z]+))?
    $~x';

    /** 1 or 2-3-length language code. */
    public readonly string $language;

    /** 2-length country code. */
    public readonly string|null $country;

    /** Encoding info. */
    public readonly string|null $encoding;

    /** Currency info. */
    public readonly string|null $currency;

    /** Category info. */
    public LocaleCategory|null $category = null;

    /**
     * Constructor.
     *
     * @param  string                         $language
     * @param  string|null                    $country
     * @param  string|null                    $encoding
     * @param  string|null                    $currency
     * @param  string|int|LocaleCategory|null $category  @internal
     * @param  bool                           $normalize @internal
     * @throws LocaleError
     */
    public function __construct(
        string $language, string $country = null,
        string $encoding = null, string $currency = null,
        string|int|LocaleCategory $category = null, bool $normalize = true,
    )
    {
        // Eg: C or en.
        if (!preg_test('~^[a-zA-Z]{1,3}$~', $language)) {
            throw new LocaleError('Invalid language: %q', $language);
        }

        if ($category !== null) {
            $this->setCategory($category);
        }

        // Normalization for lower/upper-case etc.
        if ($normalize) {
            ['language' => $language, 'country'  => $country,
             'encoding' => $encoding, 'currency' => $currency] = (
                self::normalize([
                    'language' => $language, 'country'  => $country,
                    'encoding' => $encoding, 'currency' => $currency
                ])
            );
        }

        $this->language = $language;
        $this->country  = $country;
        $this->encoding = $encoding;
        $this->currency = $currency;
    }

    /**
     * @magic
     */
    public function __toString()
    {
        $ret = $this->language;

        if ($this->country) {
            $ret .= '_' . $this->country;
        }
        if ($this->encoding) {
            $ret .= '.' . $this->encoding;
        }
        if ($this->currency) {
            $ret .= '@' . $this->currency;
        }

        return $ret;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Get country.
     *
     * @return string|null
     */
    public function getCountry(): string|null
    {
        return $this->country;
    }

    /**
     * Get encoding.
     *
     * @return string|null
     */
    public function getEncoding(): string|null
    {
        return $this->encoding;
    }

    /**
     * Get currency.
     *
     * @return string|null
     */
    public function getCurrency(): string|null
    {
        return $this->currency;
    }

    /**
     * Set category.
     *
     * Note: While other properties can be created by parse/from methods, category
     * property cannot. So this setter method becomes required and can be used for
     * that purpose.
     *
     * @param  int|string|LocaleCategory $category
     * @return void
     */
    public function setCategory(int|string|LocaleCategory $category): void
    {
        $this->category = is_scalar($category) ? new LocaleCategory($category) : $category;
    }

    /**
     * Get category.
     *
     * @return LocaleCategory|null
     */
    public function getCategory(): LocaleCategory|null
    {
        return $this->category;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return ['language' => $this->language, 'country'  => $this->country,
                'encoding' => $this->encoding, 'currency' => $this->currency,
                'category' => $this->category?->toArray()];
    }

    /**
     * Create from given locale parsing it.
     *
     * @param  string $locale
     * @return static
     */
    public static function from(string $locale): static
    {
        $info = self::parse($locale);

        return new static(...$info);
    }

    /**
     * Create from given (language) tag parsing it.
     *
     * @param  string $tag
     * @return static
     */
    public static function fromTag(string $tag): static
    {
        $info = self::parseTag($tag);

        return new static(...$info);
    }

    /**
     * Parse given locale to language, country, encoding and currency parts.
     *
     * Expected format: language[_COUNTRY[.encoding[@currency]]] (tr_TR.UTF-8).
     *
     * @param  string $locale
     * @return array
     */
    public static function parse(string $locale): array
    {
        $info = ['language' => '',   'country'  => null,
                 'encoding' => null, 'currency' => null];

        if (preg_match_names(static::PATTERN, $locale, $match)) {
            $info = [...$info, ...$match];
        }

        // Nullify possible empty fields.
        foreach (['country', 'encoding', 'currency'] as $field) {
            $info[$field] = $info[$field] ?: null;
        }

        $info = self::normalize($info);

        return $info;
    }

    /**
     * Parse given (language) tag to language & country parts.
     *
     * Expected format: language-COUNTRY (eg: tr-TR).
     *
     * @param  string $tag
     * @return array
     */
    public static function parseTag(string $tag): array
    {
        $locale = str_replace('-', '_', $tag);

        return self::parse($locale);
    }

    /**
     * Validate given locale.
     *
     * @param  string $locale
     * @return bool
     */
    public static function validate(string $locale): bool
    {
        preg_match(static::PATTERN, $locale, $match);

        return !empty($match['language']);
    }

    /**
     * Normalize given locale info.
     *
     * @param  array $info
     * @return array
     */
    public static function normalize(array $info): array
    {
        if (isset($info['language'])) {
            $info['language'] = (
                strlen($info['language']) == 1 // Eg: C.
                    ? strtoupper($info['language'])
                    : strtolower($info['language'])
            );
        }

        if (isset($info['country'])) {
            $info['country']  = strtoupper($info['country']);
        }
        if (isset($info['encoding'])) {
            $info['encoding'] = strtoupper($info['encoding']);
        }
        if (isset($info['currency'])) {
            $info['currency'] = strtolower($info['currency']);
        }

        return $info;
    }

    /**
     * List all locales.
     *
     * @return LocaleList
     */
    public static function list(): LocaleList
    {
        $items = [];

        foreach (LocaleCategory::list() as $category) {
            try {
                $items[] = new Locale(...[
                    ...self::parse(getlocale($category->value)),
                    'category'  => $category,
                    'normalize' => false
                ]);
            } catch (LocaleError) {}
        }

        return new LocaleList($items);
    }

    /**
     * Set a locale category value.
     *
     * @param  int|string|LocaleCategory $category
     * @param  string|Locale             $locale
     * @param  string|Locale          ...$locales
     * @return string|false
     */
    public static function set(int|string|LocaleCategory $category, string|Locale $locale, string|Locale ...$locales): string|false
    {
        if (is_string($category)) {
            $category = new LocaleCategory($category);
        }
        if ($category instanceof LocaleCategory) {
            $category = $category->value;
        }

        $locale = (string) $locale;
        foreach ($locales as $i => $_) {
            $locales[$i] = (string) $locales[$i];
        }

        return setlocale($category, $locale, ...$locales);
    }

    /**
     * Get a locale category value or all.
     *
     * @param  int|string|LocaleCategory $category
     * @param  bool                      $init
     * @return string|array|Locale|LocaleList|null
     */
    public static function get(int|string|LocaleCategory $category, bool $init = false): string|array|Locale|LocaleList|null
    {
        if (is_string($category)) {
            $category = new LocaleCategory($category);
        }
        if ($category instanceof LocaleCategory) {
            $category = $category->value;
        }

        $locale = getlocale($category, null, array: $category == LC_ALL);

        // No locale or init.
        if (!$locale || !$init) {
            return $locale;
        }

        // No LC_ALL given.
        if (!is_array($locale)) {
            $locale = Locale::from($locale);
            $locale->setCategory($category);
            return $locale;
        }

        $items = [];
        foreach ($locale as $item) {
            if (defined($item['name'])) {
                $locale = Locale::from($item['value']);
                $locale->setCategory($item['category']);
                $items[] = $locale;
            }
        }
        return new LocaleList($items);
    }
}

/**
 * Locale category class.
 *
 * @package global
 * @object  LocaleCategory
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleCategory implements Arrayable, Stringable
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
     * @throws LocaleError
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
            throw new LocaleError('Invalid category: %q', $category);
        }

        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * @magic
     */
    public function __toString()
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
        return ['name' => $this->name, 'value' => $this->value];
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
            $category = substr($category, 3);
        }

        return new LocaleCategory($category);
    }

    /**
     * List all locale categories.
     *
     * @return LocaleCategoryList
     */
    public static function list(): LocaleCategoryList
    {
        $items = [];

        foreach (self::map(true) as $name) {
            $items[] = new self($name);
        }

        return new LocaleCategoryList($items);
    }

    /**
     * Get constants map, creating for once.
     *
     * @param  bool $flip
     * @return array<string, int>
     */
    public static function map(bool $flip = false): array
    {
        static $map;
        $map ??= get_class_constants(self::class, false);

        return $flip ? array_flip($map) : $map;
    }
}

/**
 * Locale list class.
 *
 * @package global
 * @object  LocaleList
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleList extends ItemList
{
    /**
     * @override
     */
    public function toArray(bool $deep = false): array
    {
        $items = parent::toArray();

        if ($deep) foreach ($items as &$item) {
            if ($item instanceof Locale) {
                $item = $item->toArray();
            }
        }

        return $items;
    }
}

/**
 * Locale category list class.
 *
 * @package global
 * @object  LocaleCategoryList
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleCategoryList extends ItemList
{
    /**
     * @override
     */
    public function toArray(bool $deep = false): array
    {
        $items = parent::toArray();

        if ($deep) foreach ($items as &$item) {
            if ($item instanceof LocaleCategory) {
                $item = $item->toArray();
            }
        }

        return $items;
    }
}
