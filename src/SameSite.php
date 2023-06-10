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
 * SameSite
 */
final class SameSite implements SameSiteInterface
{
    public const STRICT = 'Strict';
    public const LAX    = 'Lax';
    public const NONE   = 'None';
    
    /**
     * Create a new SameSite.
     *
     * @param string $value
     */
    public function __construct(
        private string $value,
    ) {
        if (!in_array(ucfirst(strtolower($value)), $this->values(), true)) {
            $this->value = static::LAX;
        }
    }
    
    /**
     * Returns the value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
    
    /**
     * Returns a new instance with the specified value.
     *
     * @param string $value
     * @return static
     */
    public function withValue(string $value): static
    {
        return new static($value);
    }
    
    /**
     * Returns the valid values.
     *
     * @return array<int, string>
     */
    public function values(): array
    {
        return [static::STRICT, static::LAX, static::NONE];
    }
}