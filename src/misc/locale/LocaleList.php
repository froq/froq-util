<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\locale;

/**
 * Locale list class.
 *
 * @package froq\util\locale
 * @class   froq\util\locale\LocaleList
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleList extends \ItemList
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
