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

use Stringable;

/**
 * CookieInterface
 */
interface CookieInterface extends Stringable
{
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string;
    
    /**
     * Returns the value.
     *
     * @return string
     */
    public function value(): string;
    
    /**
     * Returns a new instance with the specified value.
     *
     * @param string $value
     * @return static
     */
    public function withValue(string $value): static;

    /**
     * Returns the duration in seconds until the cookie will expire.
     *
     * @return null|int
     */
    public function lifetime(): null|int;
    
    /**
     * Returns a new instance with the specified lifetime.
     *
     * @param null|int $lifetime
     * @return static
     */
    public function withLifetime(null|int $lifetime): static;
    
    /**
     * Returns the time the cookie expires in Unix timestamp.
     *
     * @return null|int
     */
    public function expires(): null|int;
    
    /**
     * Returns the path.
     *
     * @return string
     */
    public function path(): string;
    
    /**
     * Returns the domain.
     *
     * @return string
     */
    public function domain(): string;
    
    /**
     * Returns the secure.
     *
     * @return bool
     */
    public function secure(): bool;
    
    /**
     * Returns the httpOnly.
     *
     * @return bool
     */
    public function httpOnly(): bool;
    
    /**
     * Returns the sameSite.
     *
     * @return null|SameSiteInterface
     */
    public function sameSite(): null|SameSiteInterface;
    
    /**
     * Returns the cookie as a HTTP header string.
     *
     * @return string
     */
    public function toHeader(): string;
    
    /**
     * Send the cookie.
     *
     * @return bool
     */
    public function send(): bool;
}