<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\mailer;

/**
 * @package froq\util\mailer
 * @class   froq\util\mailer\MailerException
 * @author  Kerem Güneş
 * @since   7.0
 */
class MailerException extends \froq\common\Exception
{
    public static function forEmptyFrom(): static
    {
        return new static('Empty from, call setFrom()');
    }

    public static function forEmptyTo(): static
    {
        return new static('Empty to, call addTo()');
    }

    public static function forNullSubject(): static
    {
        return new static('Null subject, call setSubject()');
    }

    public static function forNullBody(): static
    {
        return new static('Null body, call setBody()');
    }

    public static function forInvalidAddress(string $address): static
    {
        return new static('Invalid address %q', $address);
    }

    public static function forError(): static
    {
        $message = error_message(extract: true) ??
            // It shows this error "sh: 1: /usr/sbin/sendmail: not found"
            // and ob_start() does not work to hide this output.
            'cannot found "sendmail", probably not installed or misconfigured';

        return new static('Error [%s]', $message);
    }
}
