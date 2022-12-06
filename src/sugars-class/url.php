<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-util
 */

use froq\common\interface\{Arrayable, Stringable};
use froq\common\trait\{DataAccessTrait, DataAccessMagicTrait};
use froq\collection\trait\{CountTrait, GetTrait, FilterTrait, MapTrait};

/**
 * A URL class for working customized URLs.
 *
 * @package global
 * @class   Url
 * @author  Kerem Güneş
 * @since   6.5
 */
class Url implements Arrayable, Stringable, \Stringable
{
    /** Components. */
    private array $data = [
        'origin', 'authority', 'scheme', 'host', 'port',
        'user', 'pass', 'path', 'query', 'fragment',
    ];

    /**
     * Constructor.
     *
     * @param  string|array|null $source
     * @param  array             $check
     * @throws UrlError
     */
    public function __construct(string|array $source = null, array $check = [])
    {
        // Fill data with null fields.
        $this->data = array_fill_keys($this->data, null);

        if ($source !== null) {
            if (is_string($source)) {
                $data = http_parse_url($source);
            } else {
                // Stringify query.
                if (is_array($source['query'] ?? '')) {
                    $source['query'] = http_build_query_string($source['query']);
                }

                // Filter valid keys & reformat.
                $data = array_filter_keys($source, fn(int|string $key): bool => (
                    array_key_exists($key, $this->data)
                ));

                $data && $data = http_parse_url(http_build_url($data));
            }

            // Drop nulls from data.
            $data = array_refine($data, [null]);

            if (!$data) {
                throw new UrlError('Invalid source: %q', (
                    is_string($source) ? $source : '(Array)'
                ));
            }

            // Check given fields.
            foreach ($check as $key) {
                isset($data[$key]) || throw new UrlError(
                    'Invalid source: missing key %q', $key
                );
            }

            // Set data fields via setters.
            foreach ($this->data as $key => $_) {
                isset($data[$key])
                && ($method = 'set' . $key)
                && $this->$method($data[$key]);
            }
        }
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @magic
     */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * @throws UrlError
     * @magic
     */
    public function __set(string $name, mixed $value): void
    {
        if (array_key_exists($name, $this->data)) {
            $setter = 'set' . $name;
            $this->$setter($value);
            return;
        }

        throw new UrlError('Invalid component: %q', $name);
    }

    /**
     * @throws UrlError
     * @magic
     */
    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->data)) {
            $getter = 'get' . $name;
            return $this->$getter();
        }

        throw new UrlError('Invalid component: %q', $name);
    }

    /**
     * Set origin.
     *
     * @param  string $origin
     * @return self
     * @throws UrlError
     */
    public function setOrigin(string $origin): self
    {
        if (!preg_match_names(
            '~^(?<scheme>\w+)://(?<host>[\w\-\.]+)(?::(?<port>\d+))?$~',
            $origin, $match
        )) {
            throw new UrlError('Invalid origin: %q', $origin);
        }

        $this->data['origin'] = $origin;

        $this->setScheme($match['scheme'])
             ->setHost($match['host']);

        if (isset($match['port'])) {
            $this->setPort((int) $match['port']);
        }

        return $this;
    }

    /**
     * Get origin.
     *
     * @return string|null
     */
    public function getOrigin(): string|null
    {
        return $this->data['origin'] ?? null;
    }

    /**
     * Set authority.
     *
     * @param  string $authority
     * @return self
     * @throws UrlError
     */
    public function setAuthority(string $authority): self
    {
        if (!preg_match_names(
            '~^(?:(?<userpass>[^@]+)@)?(?<host>[\w\-\.]+)(?::(?<port>\d+))?$~',
            $authority, $match
        )) {
            throw new UrlError('Invalid authority: %q', $authority);
        }

        $this->data['authority'] = $authority;

        $this->setHost($match['host']);

        if (isset($match['userpass'])) {
            [$user, $pass] = split(':', $match['userpass'], 2);
            isset($user) && $this->setUser($user);
            isset($pass) && $this->setPass($pass);
        }

        if (isset($match['port'])) {
            $this->setPort((int) $match['port']);
        }

        return $this;
    }

    /**
     * Get authority.
     *
     * @return string|null
     */
    public function getAuthority(): string|null
    {
        return $this->data['authority'] ?? null;
    }

    /**
     * Set scheme.
     *
     * @param  string $scheme
     * @return self
     * @throws UrlError
     */
    public function setScheme(string $scheme): self
    {
        if (!preg_test('~^([\w\-]+)$~', $scheme)) {
            throw new UrlError('Invalid scheme: %q', $scheme);
        }

        $this->data['scheme'] = $scheme;

        return $this;
    }

    /**
     * Get scheme.
     *
     * @return string|null
     */
    public function getScheme(): string|null
    {
        return $this->data['scheme'] ?? null;
    }

    /**
     * Set host.
     *
     * @param  string $host
     * @param  bool   $ip   For IP check.
     * @return self
     * @throws UrlError
     */
    public function setHost(string $host, bool $ip = false): self
    {
        if (!preg_test('~^(?:([\w\-\.]+))?([\w\-]+)\.([\w]+)$~', $host)) {
            throw new UrlError('Invalid host: %q', $host);
        } elseif ($ip && ip2long($host) === false) {
            throw new UrlError('Invalid host IP: %q', $host);
        }

        $this->data['host'] = $host;

        return $this;
    }

    /**
     * Get host.
     *
     * @return string|null
     */
    public function getHost(): string|null
    {
        return $this->data['host'] ?? null;
    }

    /**
     * Set port.
     *
     * @param  int $port
     * @return self
     */
    public function setPort(int $port): self
    {
        if ($port < 0 || $port > 65535) {
            throw new UrlError('Invalid port: %q', $port);
        }

        $this->data['port'] = $port;

        return $this;
    }

    /**
     * Get port.
     *
     * @return int|null
     */
    public function getPort(): int|null
    {
        return $this->data['port'] ?? null;
    }

    /**
     * Set user.
     *
     * @param  string $user
     * @return self
     */
    public function setUser(string $user): self
    {
        $this->data['user'] = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return string|null
     */
    public function getUser(): string|null
    {
        return $this->data['user'] ?? null;
    }

    /**
     * Set pass.
     *
     * @param  string $pass
     * @return self
     */
    public function setPass(string $pass): self
    {
        $this->data['pass'] = $pass;

        return $this;
    }

    /**
     * Get pass.
     *
     * @return string|null
     */
    public function getPass(): string|null
    {
        return $this->data['pass'] ?? null;
    }

    /**
     * Set path.
     *
     * @param  string $path
     * @return self
     */
    public function setPath(string $path): self
    {
        // Reduce slashes.
        $path = preg_replace('~(?<!:)/{2,}~', '/', $path);

        $this->data['path'] = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string|null
     */
    public function getPath(): string|null
    {
        return $this->data['path'] ?? null;
    }

    /**
     * Set query.
     *
     * @param  string|array|UrlQuery $query
     * @return self
     */
    public function setQuery(string|array|UrlQuery $query): self
    {
        if (!$query instanceof UrlQuery) {
            $query = new UrlQuery($query);
        }

        $this->data['query'] = $query;

        return $this;
    }

    /**
     * Get query.
     *
     * @return UrlQuery|null
     */
    public function getQuery(): UrlQuery|null
    {
        return $this->data['query'] ?? null;
    }

    /**
     * Set fragment.
     *
     * @param  string $fragment
     * @return self
     */
    public function setFragment(string $fragment): self
    {
        $this->data['fragment'] = $fragment;

        return $this;
    }

    /**
     * Get fragment.
     *
     * @return string|null
     */
    public function getFragment(): string|null
    {
        return $this->data['fragment'] ?? null;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc froq\common\interface\Stringable
     */
    public function toString(): string
    {
        return http_build_url($this->data);
    }

    /**
     * Parse a URL.
     *
     * @param  string $url
     * @param  array  $check
     * @return array
     * @causes UrlError
     */
    public static function parse(string $url, array $check = []): array
    {
        return (new self($url, $check))->toArray();
    }
}

/**
 * A URL-query class for working customized URL-queries.
 *
 * @package global
 * @class   UrlQuery
 * @author  Kerem Güneş
 * @since   6.5
 */
class UrlQuery implements Arrayable, Stringable, \Stringable, Countable, ArrayAccess
{
    /** For ArrayAccess and __set(),__get() etc. */
    use DataAccessTrait, DataAccessMagicTrait;

    /** For Countable and getInt(),getBool(),filter(),map() etc. */
    use CountTrait, GetTrait, FilterTrait, MapTrait;

    /** Data. */
    private array $data = [];

    /**
     * Constructor.
     *
     * @param string|array|null $source
     */
    public function __construct(string|array $source = null)
    {
        if ($source !== null) {
            if (is_string($source)) {
                $data = http_parse_query_string($source);
            } else {
                $data = array_map_recursive(
                    fn(mixed $value): mixed => $this->normalizeValue($value),
                    $source
                );
            }

            $this->data = $data;
        }
    }

    /**
     * @magic
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @magic
     */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * Check if a key was set & not null.
     *
     * @param  string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Set an item.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return self
     */
    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = $this->normalizeValue($value);

        return $this;
    }

    /**
     * Get an item (with dot notation).
     *
     * @param  string|array $key
     * @param  mixed|null   $default
     * @return mixed
     */
    public function get(string|array $key, mixed $default = null): mixed
    {
        $value = array_get($this->data, $key, $default);

        return $value;
    }

    /**
     * Remove an item (with dot notation).
     *
     * @param  string|array $key
     * @return self
     */
    public function remove(string|array $key): self
    {
        array_remove($this->data, $key);

        return $this;
    }

    /**
     * Get a subquery as a `UrlQuery` if it's an array or null.
     *
     * @param  string $key
     * @return UrlQuery|null
     */
    public function query(string $key): UrlQuery|null
    {
        $query = $this->get($key);

        return is_array($query) ? new UrlQuery($query) : null;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc froq\common\interface\Stringable
     */
    public function toString(): string
    {
        return http_build_query_string($this->data);
    }

    /**
     * Normalize given value casting as string.
     */
    private function normalizeValue(mixed $value): mixed
    {
        if (is_bool($value)) {
            $value = (int) $value;
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        if (is_array($value)) {
            foreach ($value as &$_value) {
                $_value = $this->normalizeValue($_value);
            }
        }
        return  $value;
    }
}
