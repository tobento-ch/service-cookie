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

/**
 * CookieFactory
 */
class CookieFactory implements CookieFactoryInterface
{
    /**
     * Create a new CookieFactory.
     *
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param string $sameSite
     */
    public function __construct(
        protected string $path = '/',
        protected string $domain = '',
        protected bool $secure = true,
        protected string $sameSite = 'Lax',
    ) {}
    
    /**
     * Create a new cookie.
     *
     * @param string $name
     * @param string $value
     * @param null|int $lifetime The duration in seconds until the cookie will expire.
     * @param null|string $path
     * @param null|string $domain
     * @param null|bool $secure
     * @param bool $httpOnly
     * @param null|string|SameSiteInterface $sameSite
     * @return CookieInterface
     */
    public function createCookie(
        string $name,
        string $value = '',
        null|int $lifetime = null,
        null|string $path = null,
        null|string $domain = null,
        null|bool $secure = null,
        bool $httpOnly = true,
        null|string|SameSiteInterface $sameSite = null,
    ): CookieInterface {
        
        $path = $path ?: $this->path;
        $domain = $domain ?: $this->domain;
        $secure = $secure ?: $this->secure;
        $sameSite = $sameSite ?: $this->sameSite;
        
        if (! $sameSite instanceof SameSiteInterface) {
            $sameSite = new SameSite(value: $sameSite);
        }

        return new Cookie(
            name: $name,
            value: $value,
            lifetime: $lifetime,
            path: $path,
            domain: $domain,
            secure: $secure,
            httpOnly: $httpOnly,
            sameSite: $sameSite,
        );
    }

    /**
     * Create a new cookie from array.
     *
     * @param array $cookie
     * @return CookieInterface
     */
    public function createCookieFromArray(array $cookie): CookieInterface
    {
        return $this->createCookie(
            name: $this->verifyString($cookie['name'] ?? ''),
            value: $this->verifyString($cookie['value'] ?? ''),
            lifetime: $this->verifyIntOrNull($cookie['lifetime'] ?? null),
            path: $this->verifyStringOrNull($cookie['path'] ?? null),
            domain: $this->verifyStringOrNull($cookie['domain'] ?? null),
            secure: $this->verifyBool($cookie['secure'] ?? true),
            httpOnly: $this->verifyBool($cookie['httpOnly'] ?? true),
            sameSite: $this->verifyStringOrNull($cookie['sameSite'] ?? null),
        );
    }
    
    /**
     * Verify string.
     *
     * @param mixed $value
     * @param string $default
     * @return string
     */
    protected function verifyString(mixed $value, string $default = ''): string
    {
        return is_string($value) ? $value : $default;
    }
    
    /**
     * Verify string or null.
     *
     * @param mixed $value
     * @param null|string $default
     * @return null|string
     */
    protected function verifyStringOrNull(mixed $value, null|string $default = null): null|string
    {
        return is_string($value) || is_null($value) ? $value : $default;
    }
    
    /**
     * Verify int or null.
     *
     * @param mixed $value
     * @param null|int $default
     * @return null|int
     */
    protected function verifyIntOrNull(mixed $value, null|int $default = null): null|int
    {
        if (is_numeric($value)) {
            return (int)$value;
        }
        
        return is_null($value) ? $value : $default;
    }
    
    /**
     * Verify bool.
     *
     * @param mixed $value
     * @param bool $default
     * @return bool
     */
    protected function verifyBool(mixed $value, bool $default = true): bool
    {
        return is_bool($value) ? $value : $default;
    }
}