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

namespace Tobento\Service\Cookie\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Cookie\CookieValues;
use Tobento\Service\Cookie\CookieValuesInterface;

/**
 * CookieValuesTest
 */
class CookieValuesTest extends TestCase
{
    public function testConstructMethod()
    {
        $values = new CookieValues(
            values: [],
        );
        
        $this->assertInstanceof(CookieValuesInterface::class, $values);
    }
    
    public function testGetMethod()
    {
        $values = new CookieValues([
            'foo' => 'Foo',
        ]);
        
        $this->assertSame('Foo', $values->get('foo'));
        $this->assertSame(null, $values->get('bar'));
        $this->assertSame('Bar', $values->get('bar', default: 'Bar'));
    }
    
    public function testGetMethodWithNotation()
    {
        $values = new CookieValues([
            'foo' => [
                '1' => 'Foo 1',
            ],
        ]);
        
        $this->assertSame(['1' => 'Foo 1'], $values->get('foo'));
        $this->assertSame('Foo 1', $values->get('foo.1'));
        $this->assertSame(null, $values->get('foo.2'));
        $this->assertSame('Bar', $values->get('foo.2', default: 'Bar'));
    }
    
    public function testHasMethod()
    {
        $values = new CookieValues([
            'foo' => 'Foo',
        ]);
        
        $this->assertTrue($values->has('foo'));
        $this->assertFalse($values->has('bar'));
    }
    
    public function testHasMethodWithNotation()
    {
        $values = new CookieValues([
            'foo' => [
                '1' => 'Foo 1',
            ],
        ]);
        
        $this->assertTrue($values->has('foo'));
        $this->assertTrue($values->has('foo.1'));
        $this->assertFalse($values->has('foo.2'));
    }
    
    public function testAllMethod()
    {
        $values = new CookieValues([
            'foo' => 'Foo',
            'bar' => 'Bar',
        ]);
        
        $this->assertSame(2, count($values->all()));
        
        $iterated = [];
        
        foreach($values->all() as $name => $value) {
            $iterated[$name] = $value;
        }
        
        $this->assertSame(
            ['foo' => 'Foo', 'bar' => 'Bar'],
            $iterated
        );
    }
    
    public function testGetIteratorMethod()
    {
        $values = new CookieValues([
            'foo' => 'Foo',
            'bar' => 'Bar',
        ]);
        
        $iterated = [];
        
        foreach($values as $name => $value) {
            $iterated[$name] = $value;
        }
        
        $this->assertSame(
            ['foo' => 'Foo', 'bar' => 'Bar'],
            $iterated
        );
    }
    
    public function testMapMethod()
    {
        $values = new CookieValues([
            'foo' => 'Foo',
            'bar' => 'Bar',
        ]);
        
        $valuesNew = $values->map(function(mixed $value, string|int $name): mixed {
            return strtoupper((string)$value);
        });
        
        $this->assertFalse($values === $valuesNew);
        $this->assertSame(['FOO', 'BAR'], array_values($valuesNew->all()));
    }
    
    public function testWithValuesMethod()
    {
        $values = new CookieValues([
            'foo' => 'Foo',
        ]);
        
        $valuesNew = $values->withValues(['bar' => 'Bar']);
        
        $this->assertFalse($values === $valuesNew);
        $this->assertSame(['bar' => 'Bar'], $valuesNew->all());
    }
}