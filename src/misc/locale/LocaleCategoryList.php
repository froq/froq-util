<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\locale;

/**
 * Locale category list class.
 *
 * @package froq\util\locale
 * @class   froq\util\locale\LocaleCategoryList
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleCategoryList extends \ItemList
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
