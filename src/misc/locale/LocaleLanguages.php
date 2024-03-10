<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\locale;

/**
 * Locale language map class.
 *
 * @package froq\util\locale
 * @class   froq\util\locale\LocaleLanguages
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleLanguages extends \Map
{
    /**
     * Constructor.
     *
     * @param array       $data
     * @param string|null $prefix
     */
    public function __construct(array $data, string $prefix = null)
    {
        $map = [];

        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            if ($prefix && !str_starts_with($key, $prefix)) {
                continue;
            }

            $map[$key] = new LocaleLanguage(
                $value['area']      ?? '',
                $value['locale']    ?? '',
                $value['localeAlt'] ?? null,
            );
        }

        parent::__construct($map);
    }

    /**
     * @override
     */
    public function toArray(bool $deep = false): array
    {
        $items = parent::toArray();

        if ($deep) foreach ($items as &$item) {
            if ($item instanceof LocaleLanguage) {
                $item = $item->toArray();
            }
        }

        return $items;
    }
}
