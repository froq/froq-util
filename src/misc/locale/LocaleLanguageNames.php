<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\locale;

/**
 * Locale language name set class.
 *
 * @package froq\util\locale
 * @class   froq\util\locale\LocaleLanguageNames
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleLanguageNames extends \Set
{
    /**
     * Constructor.
     *
     * @param array       $data
     * @param string|null $prefix
     */
    public function __construct(array $data, string $prefix = null)
    {
        $set = [];

        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            if ($prefix && !str_starts_with($key, $prefix)) {
                continue;
            }

            $set[] = $key;
        }

        parent::__construct($set);
    }
}
