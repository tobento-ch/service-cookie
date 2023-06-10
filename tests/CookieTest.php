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
use Tobento\Service\Cookie\Cookie;
use Tobento\Service\Cookie\CookieInterface;
use Tobento\Service\Cookie\SameSite;
use Tobento\Service\Cookie\SameSiteInterface;

/**
 * CookieTest
 */
class CookieTest extends TestCase
{
    public function testConstructMethod()
    {
        $cookie = new Cookie(
            name: 'name',
        );
        
        $this->assertInstanceof(CookieInterface::class, $cookie);
        $this->assertSame('name', $cookie->name());
        $this->assertSame('', $cookie->value());
        $this->assertSame(null, $cookie->lifetime());
        $this->assertSame('/', $cookie->path());
        $this->assertSame('', $cookie->domain());
        $this->assertTrue($cookie->secure());
        $this->assertTrue($cookie->httpOnly());
        $this->assertSame(null, $cookie->sameSite());
    }
    
    public function testConstructMethodWithAllParams()
    {
        $cookie = new Cookie(
            name: 'name',
            value: 'value',
            lifetime: 3600,
            path: '/',
            domain: 'example.com',
            secure: true,
            httpOnly: true,
            sameSite: new SameSite(value: 'Lax'),
        );
        
        $this->assertInstanceof(CookieInterface::class, $cookie);
        $this->assertSame('name', $cookie->name());
        $this->assertSame('value', $cookie->value());
        $this->assertSame(3600, $cookie->lifetime());
        $this->assertSame('/', $cookie->path());
        $this->assertSame('example.com', $cookie->domain());
        $this->assertTrue($cookie->secure());
        $this->assertTrue($cookie->httpOnly());
        $this->assertInstanceof(SameSiteInterface::class, $cookie->sameSite());
    }
    
    public function testWithValueMethod()
    {
        $cookie = new Cookie(name: 'name', value: 'value');

        $this->assertSame('value', $cookie->value());
        
        $cookieNew = $cookie->withValue('foo');
        
        $this->assertFalse($cookie === $cookieNew);
        $this->assertSame('foo', $cookieNew->value());
    }
    
    public function testWithLifetimeMethod()
    {
        $cookie = new Cookie(name: 'name');

        $this->assertSame(null, $cookie->lifetime());
        
        $cookieNew = $cookie->withLifetime(3600);
        
        $this->assertFalse($cookie === $cookieNew);
        $this->assertSame(3600, $cookieNew->lifetime());
        $this->assertSame(null, $cookieNew->withLifetime(null)->lifetime());
    }
    
    public function testExpiresMethod()
    {
        $cookie = new Cookie(name: 'name');

        $this->assertSame(null, $cookie->expires());
        
        $cookie = new Cookie(name: 'name', lifetime: 3600);

        $this->assertIsInt($cookie->expires());
    }
    
    public function testToHeaderMethods()
    {
        $cookie = new Cookie(
            name: 'name',
            value: 'value',
            lifetime: 3600,
            path: '/',
            domain: 'example.com',
            secure: true,
            httpOnly: true,
            sameSite: new SameSite(value: 'Lax'),
        );
        
        $this->assertSame($cookie->toHeader(), (string)$cookie);
        
        $this->assertStringContainsString(
            'name=value;',
            $cookie->toHeader(),
        );
        
        $this->assertStringContainsString(
            'Max-Age=3600; Path=/; Domain=example.com; Secure; HttpOnly',
            $cookie->toHeader(),
        );
    }
}