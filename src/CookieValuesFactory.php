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
 * CookieValuesFactory
 */
class CookieValuesFactory implements CookieValuesFactoryInterface
{
    /**
     * Create a new cookie values from array.
     * May be used for creating cookie values from the $_COOKIE superglobal.
     *
     * @param array $values
     * @return CookieValuesInterface
     */
    public function createCookieValuesFromArray(array $values): CookieValuesInterface
    {
        return new CookieValues($values);
    }
    
    /**
     * Create a new cookie values from cookies.
     *
     * @param CookiesInterface $cookies
     * @return CookieValuesInterface
     */
    public function createCookieValuesFromCookies(CookiesInterface $cookies): CookieValuesInterface
    {
        $values = [];
        
        foreach($cookies->all() as $cookie) {
            $name = str_replace(['[]', '[', ']'], ['', '.', ''], $cookie->name());
            $values[$name] = $cookie->value();
        }
        
        return new CookieValues(Arr::undot($values));
    }
}