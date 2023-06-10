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

use IteratorAggregate;

/**
 * CookiesInterface
 */
interface CookiesInterface extends IteratorAggregate
{
    /**
     * Add a cookie.
     *
     * @param CookieInterface $cookie
     * @return static $this
     */
    public function addCookie(CookieInterface $cookie): static;
    
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
    ): static;
    
    /**
     * Returns a cookie by the specified parameters or null if not found.
     *
     * @param string $name
     * @param null|string $path
     * @param null|string $domain
     * @return null|CookieInterface
     */
    public function get(string $name, null|string $path = null, null|string $domain = null): null|CookieInterface;
    
    /**
     * Clear a cookie by the specified parameters.
     *
     * @param string $name
     * @param null|string $path
     * @param null|string $domain
     * @return static
     */
    public function clear(string $name, null|string $path = null, null|string $domain = null): static;
    
    /**
     * Gets cookies column.
     *
     * @param string $columnKey
     * @param string $indexKey
     * @return array
     */
    public function column(string $columnKey = 'name', null|string $indexKey = null): array;
    
    /**
     * Returns the first cookie, otherwise null.
     *
     * @return null|CookieInterface
     */
    public function first(): null|CookieInterface;
    
    /**
     * Returns all cookies.
     *
     * @return array<int, CookieInterface>
     */
    public function all(): array;
    
    /**
     * Returns a new instance with the filtered cookies.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static;
    
    /**
     * Returns a new instance with the mapped cookies.
     *
     * @param callable $mapper
     * @return static
     */
    public function map(callable $mapper): static;

    /**
     * Returns a new instance with the name filtered.
     *
     * @param string $name
     * @return static
     */
    public function name(string $name): static;
    
    /**
     * Returns a new instance with the path filtered.
     *
     * @param string $path
     * @return static
     */
    public function path(string $path): static;
    
    /**
     * Returns a new instance with the domain filtered.
     *
     * @param string $domain
     * @return static
     */
    public function domain(string $domain): static;
    
    /**
     * Returns the cookie header.
     *
     * @param array $cookieHeader
     * @return array
     */
    public function toHeader(array $cookieHeader = []): array;
}