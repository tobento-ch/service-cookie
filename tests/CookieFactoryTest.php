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
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookieFactoryInterface;
use Tobento\Service\Cookie\CookieInterface;

/**
 * CookieFactoryTest
 */
class CookieFactoryTest extends TestCase
{
    public function testConstructMethod()
    {
        $factory = new CookieFactory(
            path: '/path',
            domain: 'example.com',
            secure: true,
            sameSite: 'Strict',
        );
        
        $this->assertInstanceof(CookieFactoryInterface::class, $factory);
    }
    
    public function testCreateCookieMethod()
    {
        $factory = new CookieFactory();
        
        $cookie = $factory->createCookie(
            name: 'name',
            value: 'value',
            lifetime: 3600,
            path: '/',
            domain: 'example.com',
            secure: true,
            httpOnly: true,
            sameSite: 'Strict',
        );
        
        $this->assertInstanceof(CookieInterface::class, $cookie);
        $this->assertSame('name', $cookie->name());
        $this->assertSame('value', $cookie->value());
        $this->assertSame(3600, $cookie->lifetime());
        $this->assertSame('/', $cookie->path());
        $this->assertSame('example.com', $cookie->domain());
        $this->assertTrue($cookie->secure());
        $this->assertTrue($cookie->httpOnly());
        $this->assertSame('Strict', $cookie->sameSite()?->value());
    }
    
    public function testCreateCookieMethodUsesDefaultValues()
    {
        $factory = new CookieFactory(
            path: '/path',
            domain: 'example.com',
            secure: true,
            sameSite: 'Strict',
        );
        
        $cookie = $factory->createCookie(
            name: 'name',
            value: 'value',
        );
        
        $this->assertInstanceof(CookieInterface::class, $cookie);
        $this->assertSame('name', $cookie->name());
        $this->assertSame('value', $cookie->value());
        $this->assertSame(null, $cookie->lifetime());
        $this->assertSame('/path', $cookie->path());
        $this->assertSame('example.com', $cookie->domain());
        $this->assertTrue($cookie->secure());
        $this->assertTrue($cookie->httpOnly());
        $this->assertSame('Strict', $cookie->sameSite()?->value());
    }
    
    public function testCreateCookieFromArrayMethod()
    {
        $factory = new CookieFactory();
        
        $cookie = $factory->createCookieFromArray([
            'name' => 'name',
            'value' => 'value',
            'lifetime' => 3600,
            'path' => '/path',
            'domain' => '.example.com',
            'secure' => true,
            'httpOnly' => true,
            'sameSite' => 'Strict',
        ]);
        
        $this->assertInstanceof(CookieInterface::class, $cookie);
        $this->assertSame('name', $cookie->name());
        $this->assertSame('value', $cookie->value());
        $this->assertSame(3600, $cookie->lifetime());
        $this->assertSame('/path', $cookie->path());
        $this->assertSame('.example.com', $cookie->domain());
        $this->assertTrue($cookie->secure());
        $this->assertTrue($cookie->httpOnly());
        $this->assertSame('Strict', $cookie->sameSite()?->value());
    }
    
    public function testCreateCookieFromArrayMethodWithEmptyArray()
    {
        $factory = new CookieFactory();
        
        $cookie = $factory->createCookieFromArray([]);
        
        $this->assertInstanceof(CookieInterface::class, $cookie);
        $this->assertSame('', $cookie->name());
        $this->assertSame('', $cookie->value());
        $this->assertSame(null, $cookie->lifetime());
        $this->assertSame('/', $cookie->path());
        $this->assertSame('', $cookie->domain());
        $this->assertTrue($cookie->secure());
        $this->assertTrue($cookie->httpOnly());
        $this->assertSame('Lax', $cookie->sameSite()?->value());
    }
    
    public function testCreateCookieFromArrayMethodWithInvalidTypes()
    {
        $factory = new CookieFactory();
        
        $cookie = $factory->createCookieFromArray([
            'name' => true,
            'value' => 20.00,
            'lifetime' => 'foo',
            'path' => 30,
            'domain' => 40,
            'secure' => 'foo',
            'httpOnly' => 'foo',
            'sameSite' => 50,
        ]);
        
        $this->assertInstanceof(CookieInterface::class, $cookie);
        $this->assertSame('', $cookie->name());
        $this->assertSame('', $cookie->value());
        $this->assertSame(null, $cookie->lifetime());
        $this->assertSame('/', $cookie->path());
        $this->assertSame('', $cookie->domain());
        $this->assertTrue($cookie->secure());
        $this->assertTrue($cookie->httpOnly());
        $this->assertSame('Lax', $cookie->sameSite()?->value());
    }
}