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

use ArrayAccess;
use IteratorAggregate;

/**
 * CookieValuesInterface
 */
interface CookieValuesInterface extends ArrayAccess, IteratorAggregate
{
    /**
     * Get a cookie value by name.
     *
     * @param string $name
     * @param mixed $default A default value.
     * @return mixed The the default value if not exist.
     */
    public function get(string $name, mixed $default = null): mixed;
    
    /**
     * Check if a value by name exists.
     *
     * @param string|int $key The key.
     * @param mixed $default A default value.
     * @return mixed The the default value if not exist.
     */
    public function has(string $name): bool;
    
    /**
     * Returns all values.
     *
     * @return array
     */
    public function all(): array;
    
    /**
     * Returns a new instance with the mapped values.
     *
     * @param callable $mapper
     * @return static
     */
    public function map(callable $mapper): static;
    
    /**
     * Returns a new instance with the specified values.
     *
     * @param array $values
     * @return static
     */
    public function withValues(array $values): static;
}