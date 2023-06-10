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

use Tobento\Service\Collection\Arr;

/**
 * CookiesFactory
 */
class CookiesFactory implements CookiesFactoryInterface
{
    /**
     * Create a new CookiesFactory.
     *
     * @param CookieFactoryInterface $cookieFactory
     */
    public function __construct(
        protected CookieFactoryInterface $cookieFactory,
    ) {}

    /**
     * Create a new cookies.
     *
     * @param CookieInterface ...$cookie
     * @return CookiesInterface
     */
    public function createCookies(CookieInterface ...$cookie): CookiesInterface
    {
        return new Cookies($this->cookieFactory, ...$cookie);
    }
    
    /**
     * Create a new cookies from key/value pairs.
     * May be used for creating cookies from the $_COOKIE superglobal.
     *
     * @param array $cookies
     * @return CookiesInterface
     */
    public function createCookiesFromKeyValuePairs(array $cookies): CookiesInterface
    {
        $c = $this->createCookies();
        
        Arr::flat($cookies, function(mixed $value, int|string $key) use ($c): mixed {
            
            if (is_scalar($value)) {
                $c->add(name: (string)$key, value: (string)$value);
            }
            
            return $value;
        });
        
        return $c;
    }
    
    /**
     * Create a new cookies from array.
     *
     * @param array $cookies
     * @return CookiesInterface
     */
    public function createCookiesFromArray(array $cookies): CookiesInterface
    {
        $c = $this->createCookies();
        
        foreach($cookies as $cookie) {
            if (is_array($cookie)) {
                $c->addCookie($this->cookieFactory->createCookieFromArray($cookie));
            }
        }
        
        return $c;
    }
}