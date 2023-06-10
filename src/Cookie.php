<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Cookie;

use DateTime;

/**
 * Cookie
 *
 * @link https://www.php.net/manual/en/function.setcookie.php
 */
class Cookie implements CookieInterface
{
    /**
     * Create a new Cookie.
     *
     * @param string $name
     * @param string $value
     * @param null|int $lifetime The duration in seconds until the cookie will expire.
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @param null|SameSiteInterface $sameSite
     */
    public function __construct(
        protected string $name,
        protected string $value = '',
        protected null|int $lifetime = null,
        protected string $path = '/',
        protected string $domain = '',
        protected bool $secure = true,
        protected bool $httpOnly = true,
        protected null|SameSiteInterface $sameSite = null,
    ) {}
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    /**
     * Returns the value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
    
    /**
     * Returns a new instance with the specified value.
     *
     * @param string $value
     * @return static
     */
    public function withValue(string $value): static
    {
        $new = clone $this;
        $new->value = $value;
        return $new;
    }

    /**
     * Returns the duration in seconds until the cookie will expire.
     *
     * @return null|int
     */
    public function lifetime(): null|int
    {
        return $this->lifetime;
    }
    
    /**
     * Returns a new instance with the specified lifetime.
     *
     * @param null|int $lifetime
     * @return static
     */
    public function withLifetime(null|int $lifetime): static
    {
        $new = clone $this;
        $new->lifetime = $lifetime;
        return $new;
    }
    
    /**
     * Returns the time the cookie expires in Unix timestamp.
     *
     * @return null|int
     */
    public function expires(): null|int
    {
        if (is_null($this->lifetime())) {
            return null;
        }
        
        return time() + $this->lifetime();
    }
    
    /**
     * Returns the path.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }
    
    /**
     * Returns the domain.
     *
     * @return string
     */
    public function domain(): string
    {
        return $this->domain;
    }
    
    /**
     * Returns the secure.
     *
     * @return bool
     */
    public function secure(): bool
    {
        return $this->secure;
    }
    
    /**
     * Returns the httpOnly.
     *
     * @return bool
     */
    public function httpOnly(): bool
    {
        return $this->httpOnly;
    }
    
    /**
     * Returns the sameSite.
     *
     * @return null|SameSiteInterface
     */
    public function sameSite(): null|SameSiteInterface
    {
        return $this->sameSite;
    }
    
    /**
     * Returns the cookie as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toHeader();
    }
    
    /**
     * Returns the cookie as a HTTP header string.
     *
     * @return string
     */
    public function toHeader(): string
    {
        $header = [rawurlencode($this->name()).'='.rawurlencode($this->value())];

        if (! is_null($this->lifetime())) {
            $header[] = 'Expires='.gmdate(DateTime::COOKIE, $this->expires());
            $header[] = sprintf('Max-Age=%d', $this->lifetime());
        }

        if (!empty($this->path())) {
            $header[] = sprintf('Path=%s', $this->path());
        }

        if (!empty($this->domain())) {
            $header[] = sprintf('Domain=%s', $this->domain());
        }

        if ($this->secure()) {
            $header[] = 'Secure';
        }

        if ($this->httpOnly()) {
            $header[] = 'HttpOnly';
        }

        if (!is_null($this->sameSite())) {
            $header[] = sprintf('SameSite=%s', $this->sameSite()->value());
        }

        return implode('; ', $header);
    }
    
    /**
     * Send the cookie.
     *
     * @return bool
     */
    public function send(): bool
    {
        $options = [
            'expires' => (int)$this->expires(),
            'path' => $this->path(),
            'domain' => $this->domain(),
            'samesite' => $this->sameSite(),
            'secure' => $this->secure(),
            'httponly' => $this->httpOnly(),
        ];
        
        if (!is_null($this->sameSite())) {
            $options['samesite'] = $this->sameSite()->value();
        }
        
        return setcookie($this->name(), $this->value(), $options);
    }
    
    /**
     * __get For array_column support
     */
    public function __get(string $name)
    {
        return $this->{$name}();
    }

    /**
     * __isset For array_column support
     */
    public function __isset(string $name): bool
    {
        return method_exists($this, $name);
    }
}