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
 * CookiesFactoryInterface
 */
interface CookiesFactoryInterface
{
    /**
     * Create a new cookies.
     *
     * @param CookieInterface ...$cookie
     * @return CookiesInterface
     */
    public function createCookies(CookieInterface ...$cookie): CookiesInterface;
    
    /**
     * Create a new cookies from key/value pairs.
     * May be used for creating cookies from the $_COOKIE superglobal.
     *
     * @param array $cookies
     * @return CookiesInterface
     */
    public function createCookiesFromKeyValuePairs(array $cookies): CookiesInterface;
    
    /**
     * Create a new cookies from array.
     *
     * @param array $cookies
     * @return CookiesInterface
     */
    public function createCookiesFromArray(array $cookies): CookiesInterface;
}