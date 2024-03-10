<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\locale;

use froq\common\interface\Arrayable;

/**
 * Locale language class.
 *
 * @package froq\util\locale
 * @class   froq\util\locale\LocaleLanguage
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleLanguage implements Arrayable
{
    /** Locale area. */
    public readonly string $area;

    /** Locale name (abbreviation). */
    public readonly string $locale;

    /** Locale alternative (or dominant). */
    public readonly string|null $localeAlt;

    /** Locale tag (ISO format). */
    public readonly string $localeTag;

    /**
     * Constructor.
     *
     * @param string      $area
     * @param string      $locale
     * @param string|null $localeAlt
     */
    public function __construct(string $area, string $locale, string $localeAlt = null)
    {
        $localeTag = str_lower(str_replace('_', '-', $locale));

        $this->area      = $area;
        $this->locale    = $locale;
        $this->localeAlt = $localeAlt;
        $this->localeTag = $localeTag;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return [
            'area'      => $this->area,
            'locale'    => $this->locale,
            'localeAlt' => $this->localeAlt,
            'localeTag' => $this->localeTag
        ];
    }
}
