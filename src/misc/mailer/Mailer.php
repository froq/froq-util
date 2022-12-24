<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */
namespace froq\util\mailer;

/**
 * A simple mailer class.
 *
 * Note: This class utilizes `mail()` function. So, if any complex mailing works needed,
 * then an extended mailer tool should be used.
 *
 * @package froq\util\mailer
 * @class   froq\util\mailer\Mailer
 * @author  Kerem Güneş
 * @since   7.0
 */
class Mailer
{
    /** From address. */
    private string $from;

    /** To address. */
    private string $to;

    /** Reply-to address. */
    private string $replyTo;

    /** Subject. */
    private string $subject;

    /** Body. */
    private string $body;

    /** Map of headers. */
    private array $headers = [
        'Content-Type'              => 'text/plain; charset=utf-8',
        'Content-Transfer-Encoding' => 'quoted-printable',
        'Message-Id'                => null, // To be set.
        'MIME-Version'              => '1.0',
        'X-Mailer'                  => 'Froq! Mailer',
    ];

    /**
     * Constructor.
     *
     * @param string|null $from
     * @param string|null $to
     */
    public function __construct(string $from = null, string $to = null)
    {
        isset($from) && $this->setFrom($from);
        isset($to)   && $this->addTo($to);
    }

    /**
     * Set from address.
     *
     * @param  string $from
     * @return self
     * @causes froq\util\mailer\MailerException
     */
    public function setFrom(string $from): self
    {
        $this->checkAddress($from);

        $this->from = $from;

        return $this;
    }

    /**
     * Get from address.
     *
     * @return string|null
     */
    public function getFrom(): string|null
    {
        return $this->from ?? null;
    }

    /**
     * Add to address.
     *
     * @param  string $to
     * @return self
     * @causes froq\util\mailer\MailerException
     */
    public function addTo(string $to): self
    {
        $this->checkAddress($to);

        $this->to = $this->prepareTo($to);

        return $this;
    }

    /**
     * Get to address.
     *
     * @return string|null
     */
    public function getTo(): string|null
    {
        return $this->to ?? null;
    }

    /**
     * Add reply-to address.
     *
     * @param  string $replyTo
     * @return self
     * @causes froq\util\mailer\MailerException
     */
    public function addReplyTo(string $replyTo): self
    {
        $this->checkAddress($replyTo);

        $this->replyTo = $this->prepareTo($replyTo);

        return $this;
    }

    /**
     * Get reply-to address.
     *
     * @return string|null
     */
    public function getReplyTo(): string|null
    {
        return $this->replyTo ?? null;
    }

    /**
     * Set subject.
     *
     * @param  string $subject
     * @return self
     */
    public function setSubject(string $subject): self
    {
        $this->subject = trim($subject);

        return $this;
    }

    /**
     * Get subject.
     *
     * @return string|null
     */
    public function getSubject(): string|null
    {
        return $this->subject ?? null;
    }

    /**
     * Set body.
     *
     * @param  string $body
     * @return self
     */
    public function setBody(string $body): self
    {
        $this->body = trim($body);

        return $this;
    }

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody(): string|null
    {
        return $this->body ?? null;
    }

    /**
     * Set header.
     *
     * @param  string $name
     * @param  string $value
     * @return self
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Get header.
     *
     * @param  string $name
     * @return string|null
     */
    public function getHeader(string $name): string|null
    {
        return $this->headers[$name] ?? $this->headers[lower($name)] ?? null;
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Send.
     *
     * @return true
     * @throws froq\util\mailer\MailerException
     */
    public function send(): true
    {
        [$from, $to, $subject, $body] = [
            $this->getFrom()    ?: throw MailerException::forEmptyFrom(),
            $this->getTo()      ?: throw MailerException::forEmptyTo(),
            $this->getSubject() ?? throw MailerException::forNullSubject(),
            $this->getBody()    ?? throw MailerException::forNullBody(),
        ];

        $subject = '=?UTF-8?Q?' . quoted_printable_encode($subject) . '?=';

        if ($this->getHeader('Content-Transfer-Encoding') === 'quoted-printable') {
            $body = quoted_printable_encode($body);
        } elseif ($this->getHeader('Content-Transfer-Encoding') === 'base64') {
            $body = wordwrap($body, 70, "\r\n", true);
        } else {
            $body = wordwrap($body, 70, "\r\n");
        }

        if ($this->getHeader('Message-Id') === null) {
            $this->setHeader('Message-Id', vsprintf('<%s@%s>', $this->generateMessageId()));
        }

        $headers = $this->getHeaders();
        $headers['From'] = $from;
        if ($replyTo = $this->getReplyTo()) {
            $headers['Reply-To'] = $replyTo;
        }

        $headers = filter($headers);

        return @mail($to, $subject, $body, $headers) ?: throw MailerException::forError();
    }

    /**
     * Prepare given "to" address.
     */
    private function prepareTo(string $to): string
    {
        $tos = split(' *, *', (string) $this->getTo());

        if (!array_contains($tos, $to)) {
            $tos[] = $to;
        }

        return join(', ', $tos);
    }

    /**
     * Check an address.
     *
     * @throws froq\util\mailer\MailerException
     */
    private function checkAddress(string &$address): void
    {
        // Trim& reduce spaces.
        $address = preg_replace(
            ['~^\s+|\s+$~', '~\s+~'], ['', ' '],
            $address
        );

        if (str_contains($address, ',')) {
            $addresses = [];

            foreach (split(' *, *', $address) as $address) {
                $this->checkAddress($address);
                $addresses[] = $address;
            }

            $address = join(', ', $addresses);
        } else {
            // Eg: Jon Doo <jon@doo.com> or <jon@doo.com>
            if (!$email = grep('~(?:.*?<(.+)>|(.+))~', $address)) {
                throw MailerException::forInvalidAddress($address);
            }

            // Validate but skip localhost stuff.
            if (!str_ends_with($email, '@localhost')
                && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw MailerException::forInvalidAddress($address);
            }
        }
    }

    /**
     * Generate a message id with a unique id & host name.
     */
    private function generateMessageId(): array
    {
        return [
            gmdate('YmdHis') . '.' . suid(10, base: 10),
            $_SERVER['SERVER_NAME'] ?? 'localhost'
        ];
    }
}
