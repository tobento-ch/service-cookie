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
 * CookieFactoryInterface
 */
interface CookieFactoryInterface
{
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
    ): CookieInterface;

    /**
     * Create a new cookie from array.
     *
     * @param array $cookie
     * @return CookieInterface
     */
    public function createCookieFromArray(array $cookie): CookieInterface;
}