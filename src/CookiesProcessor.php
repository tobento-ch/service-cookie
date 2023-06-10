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

use Tobento\Service\Encryption\EncrypterInterface;
use Tobento\Service\Encryption\DecryptException;
use Tobento\Service\Collection\Arr;

/**
 * CookiesProcessor
 */
class CookiesProcessor implements CookiesProcessorInterface
{
    /**
     * Create a new CookiesProcessor.
     *
     * @param null|EncrypterInterface $encrypter
     * @param array $whitelistedCookies
     */
    public function __construct(
        protected null|EncrypterInterface $encrypter = null,
        protected array $whitelistedCookies = [],
    ) {}

    /**
     * Whitelist a cookie by name.
     *
     * @param string $name
     * @return static $this
     */
    public function whitelistCookie(string $name): static
    {
        $this->whitelistedCookies[] = $name;
        return $this;
    }
    
    /**
     * Process cookie values.
     *
     * @param CookieValuesInterface $cookieValues
     * @return CookieValuesInterface
     */
    public function processCookieValues(CookieValuesInterface $cookieValues): CookieValuesInterface
    {
        if (is_null($this->encrypter)) {
            return $cookieValues;
        }
        
        $flat = Arr::flat($cookieValues->all(), function(mixed $value, string $key): mixed {            
            return $this->decryptCookieValue($key, $value);
        });
        
        return $cookieValues->withValues(Arr::unflat($flat));
    }
    
    /**
     * Process cookies.
     *
     * @param CookiesInterface $cookies
     * @return CookiesInterface
     */
    public function processCookies(CookiesInterface $cookies): CookiesInterface
    {
        if (is_null($this->encrypter)) {
            return $cookies;
        }
        
        return $cookies->map(function(CookieInterface $cookie): CookieInterface {
            
            if (
                empty($cookie->value())
                || $this->isWhitelistedCookie($cookie->name())
            ) {
                return $cookie;
            }
            
            return $cookie->withValue($this->encrypter->encrypt($cookie->value()));
        });
    }
    
    /**
     * Process cookie values.
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function decryptCookieValue(string $name, mixed $value): mixed
    {
        if (
            empty($value)
            || $this->isWhitelistedCookie($name)
            || !is_string($value)
        ) {
            return $value;
        }

        try {
            return $this->encrypter->decrypt($value);
        } catch (DecryptException $e) {
            return null;
        }
    }
    
    /**
     * Return true if cookie is whitelisted, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    protected function isWhitelistedCookie(string $name): bool
    {
        return in_array($name, $this->whitelistedCookies, true);
    }
}