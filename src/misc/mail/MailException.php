<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\mail;

/**
 * @package froq\util\mail
 * @object  froq\util\mail\MailException
 * @author  Kerem Güneş
 * @since   7.0
 */
class MailException extends \froq\common\Exception
{
    /**
     * Create for empty from.
     *
     * @return static
     */
    public static function forEmptyFrom(): static
    {
        return new static('Empty from, call setFrom()');
    }

    /**
     * Create for empty to.
     *
     * @return static
     */
    public static function forEmptyTo(): static
    {
        return new static('Empty to, call addTo()');
    }

    /**
     * Create for null subject.
     *
     * @return static
     */
    public static function forNullSubject(): static
    {
        return new static('Null subject, call setSubject()');
    }

    /**
     * Create for null body.
     *
     * @return static
     */
    public static function forNullBody(): static
    {
        return new static('Null body, call setBody()');
    }

    /**
     * Create for invalid address.
     *
     * @param  string $address
     * @return static
     */
    public static function forInvalidAddress(string $address): static
    {
        return new static('Invalid address: %q', $address);
    }

    /**
     * Create for error.
     *
     * @return static
     */
    public static function forError(): static
    {
        $message = error_message(extract: true) ??
            // It shows this error "sh: 1: /usr/sbin/sendmail: not found"
            // and ob_start() does not work to hide this output.
            'cannot found "sendmail", probably not installed or misconfigured';

        return new static('Mail error [%s]', $message);
    }
}
