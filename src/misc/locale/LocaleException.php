<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\locale;

/**
 * @package froq\util\locale
 * @class   froq\util\locale\LocaleException
 * @author  Kerem Güneş
 * @since   6.0
 */
class LocaleException extends \froq\common\Exception
{
    public static function forInvalidLanguage(string $language): static
    {
        return new static('Invalid language: %q', $language);
    }

    public static function forInvalidCategory(int|string $category): static
    {
        return new static('Invalid category: %q', $category);
    }
}
