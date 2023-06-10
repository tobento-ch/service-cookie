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
use Generator;

/**
 * CookieValues
 */
final class CookieValues implements CookieValuesInterface
{
    /**
     * Create a new CookieValues.
     *
     * @param array $values
     */
    public function __construct(
        private array $values,
    ) {}
    
    /**
     * Get a cookie value by name.
     *
     * @param string $name
     * @param mixed $default A default value.
     * @return mixed The the default value if not exist.
     */
    public function get(string $name, mixed $default = null): mixed
    {
        return Arr::get($this->values, $name, $default);
    }
    
    /**
     * Check if a value by name exists.
     *
     * @param string|int $key The key.
     * @param mixed $default A default value.
     * @return mixed The the default value if not exist.
     */
    public function has(string $name): bool
    {
        return Arr::has($this->values, $name);
    }
    
    /**
     * Returns all values.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->values;
    }
    
    /**
     * Returns a new instance with the mapped values.
     *
     * @param callable $mapper
     * @return static
     */
    public function map(callable $mapper): static
    {
        $mapped = [];
        
        foreach($this->all() as $name => $value) {
            $mapped[$name] = $mapper($value, $name);
        }
        
        return new static($mapped);
    }
    
    /**
     * Returns a new instance with the specified values.
     *
     * @param array $values
     * @return static
     */
    public function withValues(array $values): static
    {
        return new static($values);
    }
    
    /**
     * Returns an iterator for the values.
     *
     * @return Generator
     */
    public function getIterator(): Generator
    {
        foreach($this->all() as $name => $value) {
            yield $name => $value;
        }
    }
    
    /**
     * Determine if a value exists at an offset.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    /**
     * Get a value at a given offset.
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->values[$offset];
    }

    /**
     * Set a value at a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    /**
     * Unset a value at a given offset.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset]);
    }
}