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
 * SameSiteInterface
 */
interface SameSiteInterface
{
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
     * Returns the valid values.
     *
     * @return array<int, string>
     */
    public function values(): array;
}