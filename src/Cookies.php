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

use Generator;

/**
 * Cookies
 */
final class Cookies implements CookiesInterface
{
    /**
     * @var array<int, CookieInterface>
     */
    private array $cookies = [];
    
    /**
     * Create a new Cookies.
     *
     * @param CookieFactoryInterface $cookieFactory
     * @param CookieInterface ...$cookie
     */
    public function __construct(
        private CookieFactoryInterface $cookieFactory,
        CookieInterface ...$cookie
    ) {
        $this->cookies = $cookie;
    }

    /**
     * Add a cookie.
     *
     * @param CookieInterface $cookie
     * @return static $this
     */
    public function addCookie(CookieInterface $cookie): static
    {
        $this->cookies[] = $cookie;
        
        return $this;
    }
    
    /**
     * Add a cookie.
     *
     * @param string $name
     * @param string $value
     * @param null|int $lifetime The duration in seconds until the cookie will expire.
     * @param null|string $path
     * @param null|string $domain
     * @param null|bool $secure
     * @param bool $httpOnly
     * @param null|string|SameSiteInterface $sameSite
     * @return static $this
     */
    public function add(
        string $name,
        string $value = '',
        null|int $lifetime = null,
        null|string $path = null,
        null|string $domain = null,
        null|bool $secure = null,
        bool $httpOnly = true,
        null|string|SameSiteInterface $sameSite = 'Lax',
    ): static {
        $cookie = $this->cookieFactory->createCookie(
            name: $name,
            value: $value,
            lifetime: $lifetime,
            path: $path,
            domain: $domain,
            secure: $secure,
            httpOnly: $httpOnly,
            sameSite: $sameSite,
        );
        
        $this->addCookie($cookie);
        
        return $this;
    }
    
    /**
     * Returns a cookie by the specified parameters or null if not found.
     *
     * @param string $name
     * @param null|string $path
     * @param null|string $domain
     * @return null|CookieInterface
     */
    public function get(string $name, null|string $path = null, null|string $domain = null): null|CookieInterface
    {
        $cookies = $this->name($name);
        
        if (!is_null($path)) {
            $cookies = $cookies->path($path);
        }
        
        if (!is_null($domain)) {
            $cookies = $cookies->domain($domain);
        }
        
        return $cookies->first();
    }
    
    /**
     * Clear a cookie by the specified parameters.
     *
     * @param string $name
     * @param null|string $path
     * @param null|string $domain
     * @return static
     */
    public function clear(string $name, null|string $path = null, null|string $domain = null): static
    {
        foreach($this->cookies as $key => $cookie) {
            if ($name !== $cookie->name()) {
                continue;
            }
            
            if (!is_null($path) && $path !== $cookie->path()) {
                continue;
            }
            
            if (!is_null($domain) && $domain !== $cookie->domain()) {
                continue;
            }
            
            unset($this->cookies[$key]);
        }
        
        $this->add(
            name: $name,
            value: '',
            path: $path,
            domain: $domain,
            lifetime: -86400,
        );
        
        return $this;
    }
    
    /**
     * Gets cookies column.
     *
     * @param string $columnKey
     * @param string $indexKey
     * @return array
     */
    public function column(string $columnKey = 'name', null|string $indexKey = null): array
    {
        return array_column($this->all(), $columnKey, $indexKey);
    }
    
    /**
     * Returns the first cookie, otherwise null.
     *
     * @return null|CookieInterface
     */
    public function first(): null|CookieInterface
    {
        $cookies = $this->all();
        
        $key = array_key_first($cookies);
        
        if (is_null($key)) {
            return null;
        }
        
        return $cookies[$key];
    }
    
    /**
     * Returns all cookies.
     *
     * @return array<int, CookieInterface>
     */
    public function all(): array
    {
        return $this->cookies;
    }
    
    /**
     * Returns an iterator for the cookies.
     *
     * @return Generator
     */
    public function getIterator(): Generator
    {
        foreach($this->all() as $key => $cookie) {
            yield $key => $cookie;
        }
    }
    
    /**
     * Returns a new instance with the filtered cookies.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $filtered = array_filter($this->all(), $callback);
        
        return new static($this->cookieFactory, ...$filtered);
    }
    
    /**
     * Returns a new instance with the mapped cookies.
     *
     * @param callable $mapper
     * @return static
     */
    public function map(callable $mapper): static
    {
        $mapped = [];
        
        foreach($this->all() as $cookie) {
            $mapped[] = $mapper($cookie);
        }
        
        return new static($this->cookieFactory, ...$mapped);
    }

    /**
     * Returns a new instance with the name filtered.
     *
     * @param string $name
     * @return static
     */
    public function name(string $name): static
    {
        return $this->filter(fn(CookieInterface $c): bool => $c->name() === $name);
    }
    
    /**
     * Returns a new instance with the path filtered.
     *
     * @param string $path
     * @return static
     */
    public function path(string $path): static
    {
        return $this->filter(fn(CookieInterface $c): bool => $c->path() === $path);
    }
    
    /**
     * Returns a new instance with the domain filtered.
     *
     * @param string $domain
     * @return static
     */
    public function domain(string $domain): static
    {
        return $this->filter(fn(CookieInterface $c): bool => $c->domain() === $domain);
    }
    
    /**
     * Returns the cookie header.
     *
     * @param array $cookieHeader
     * @return array
     */
    public function toHeader(array $cookieHeader = []): array
    {
        foreach($this->all() as $cookie) {
            $cookieHeader[] = $cookie->toHeader();
        }
        
        return $cookieHeader;
    }
}