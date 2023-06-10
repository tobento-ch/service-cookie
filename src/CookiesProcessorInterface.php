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
 * CookiesProcessorInterface
 */
interface CookiesProcessorInterface
{
    /**
     * Whitelist a cookie by name.
     *
     * @param string $name
     * @return static $this
     */
    public function whitelistCookie(string $name): static;
    
    /**
     * Process cookie values.
     *
     * @param CookieValuesInterface $cookieValues
     * @return CookieValuesInterface
     */
    public function processCookieValues(CookieValuesInterface $cookieValues): CookieValuesInterface;
    
    /**
     * Process cookies.
     *
     * @param CookiesInterface $cookies
     * @return CookiesInterface
     */
    public function processCookies(CookiesInterface $cookies): CookiesInterface;
}