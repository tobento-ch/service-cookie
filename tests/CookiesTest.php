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
use Tobento\Service\Cookie\Cookies;
use Tobento\Service\Cookie\CookiesInterface;
use Tobento\Service\Cookie\CookieInterface;
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\Cookie;

/**
 * CookiesTest
 */
class CookiesTest extends TestCase
{
    public function testConstructMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $this->assertInstanceof(CookiesInterface::class, $cookies);
    }
    
    public function testAddCookieMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookie = new Cookie(name: 'name', value: 'value');
        
        $cookies->addCookie($cookie);
        
        $this->assertSame($cookie, $cookies->first());
    }
    
    public function testAddMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(
            name: 'name',
            value: 'value',
            lifetime: 3600,
            path: '/',
            domain: 'example.com',
            secure: true,
            httpOnly: true,
            sameSite: 'Strict',
        );
        
        $this->assertSame('name', $cookies->first()?->name());
    }
    
    public function testGetMethodByName()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'name', value: 'value');
        
        $this->assertSame('name', $cookies->get(name: 'name')?->name());
        $this->assertSame(null, $cookies->get(name: 'foo')?->name());
    }

    public function testGetMethodByNameAndPath()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'name', value: 'ch', path: '/ch');
        $cookies->add(name: 'name', value: 'de', path: '/de');
        
        $this->assertSame('ch', $cookies->get(name: 'name')?->value());
        $this->assertSame('ch', $cookies->get(name: 'name', path: '/ch')?->value());
        $this->assertSame('de', $cookies->get(name: 'name', path: '/de')?->value());
        $this->assertSame(null, $cookies->get(name: 'name', path: '/fr')?->value());
        $this->assertSame(null, $cookies->get(name: 'name', path: '')?->value());
    }
    
    public function testGetMethodByNameAndDomain()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'name', value: 'ch', domain: 'example.ch');
        $cookies->add(name: 'name', value: 'de', domain: 'example.de');
        
        $this->assertSame('ch', $cookies->get(name: 'name')?->value());
        $this->assertSame('ch', $cookies->get(name: 'name', domain: 'example.ch')?->value());
        $this->assertSame('de', $cookies->get(name: 'name', domain: 'example.de')?->value());
        $this->assertSame(null, $cookies->get(name: 'name', domain: 'example.fr')?->value());
        $this->assertSame(null, $cookies->get(name: 'name', domain: '')?->value());
    }
    
    public function testGetMethodByNamePathAndDomain()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'name', value: 'ch', path: '/ch', domain: 'example.ch');
        $cookies->add(name: 'name', value: 'de', path: '/de', domain: 'example.de');
        
        $this->assertSame('ch', $cookies->get(name: 'name')?->value());
        $this->assertSame('ch', $cookies->get(name: 'name', path: '/ch', domain: 'example.ch')?->value());
        $this->assertSame('de', $cookies->get(name: 'name', path: '/de', domain: 'example.de')?->value());
        $this->assertSame(null, $cookies->get(name: 'name', path: '/ch', domain: 'example.fr')?->value());
        $this->assertSame(null, $cookies->get(name: 'name', path: '/de', domain: 'example.ch')?->value());
        $this->assertSame(null, $cookies->get(name: 'name', path: '', domain: '')?->value());
    }
    
    public function testClearMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'name', value: 'ch', path: '/ch', domain: 'example.ch');
        $cookies->add(name: 'name', value: 'de', path: '/de', domain: 'example.de');
        $cookies->add(name: 'foo', value: 'foo');
        
        $this->assertSame(null, $cookies->get(name: 'name')?->expires());
        $this->assertSame(3, count($cookies->all()));
        
        $cookies->clear('name');
        
        $this->assertIsInt($cookies->get(name: 'name')?->expires());
    }
    
    public function testColumnMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo', value: 'foo value');
        $cookies->add(name: 'bar', value: 'bar value');
        
        $this->assertSame(['foo', 'bar'], $cookies->column('name'));
        $this->assertSame(['foo value', 'bar value'], $cookies->column('value'));
        $this->assertSame(['foo' => 'foo value', 'bar' => 'bar value'], $cookies->column('value', 'name'));
    }
    
    public function testFirstMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $this->assertSame(null, $cookies->first());
        
        $cookies->add(name: 'name');
        
        $this->assertInstanceof(CookieInterface::class, $cookies->first());
        $this->assertSame('name', $cookies->first()->name());
    }
    
    public function testAllMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $this->assertSame(0, count($cookies->all()));
        
        $cookies->add(name: 'foo');
        $cookies->add(name: 'bar');
        
        $this->assertSame(2, count($cookies->all()));
        
        $this->assertInstanceof(CookieInterface::class, $cookies->all()[0]);
    }
    
    public function testGetIteratorMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo');
        $cookies->add(name: 'bar');
        
        $iterated = [];
        
        foreach($cookies as $cookie) {
            $iterated[] = $cookie;
        }
        
        $this->assertInstanceof(CookieInterface::class, $iterated[0]);
    }
    
    public function testFilterMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo');
        $cookies->add(name: 'bar');
        
        $cookiesNew = $cookies->filter(fn(CookieInterface $c): bool => $c->name() === 'foo');
        
        $this->assertFalse($cookies === $cookiesNew);
        $this->assertSame(2, count($cookies->all()));
        $this->assertSame(1, count($cookiesNew->all()));
    }
    
    public function testNameMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo', value: 'foo value');
        $cookies->add(name: 'foo', value: 'foo value ch', domain: 'example.ch');
        $cookies->add(name: 'bar');
        
        $cookiesNew = $cookies->name('foo');
        
        $this->assertFalse($cookies === $cookiesNew);
        $this->assertSame(3, count($cookies->all()));
        $this->assertSame(2, count($cookiesNew->all()));
    }
    
    public function testPathMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo', path: '/ch');
        $cookies->add(name: 'foo', path: '/de');
        $cookies->add(name: 'bar');
        
        $cookiesNew = $cookies->path('/de');
        
        $this->assertFalse($cookies === $cookiesNew);
        $this->assertSame(3, count($cookies->all()));
        $this->assertSame(1, count($cookiesNew->all()));
    }
    
    public function testDomainMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo', domain: 'example.ch');
        $cookies->add(name: 'foo', domain: 'example.de');
        $cookies->add(name: 'bar');
        
        $cookiesNew = $cookies->domain('example.de');
        
        $this->assertFalse($cookies === $cookiesNew);
        $this->assertSame(3, count($cookies->all()));
        $this->assertSame(1, count($cookiesNew->all()));
    }
    
    public function testMapMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo', value: 'value');
        $cookies->add(name: 'bar', value: 'value');
        
        $cookiesNew = $cookies->map(function(CookieInterface $c): CookieInterface {
            return $c->withValue(strtoupper($c->value()));
        });
        
        $this->assertFalse($cookies === $cookiesNew);
        $this->assertSame(['VALUE', 'VALUE'], $cookiesNew->column('value'));
    }
    
    public function testToHeaderMethod()
    {
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo', value: 'value');
        $cookies->add(name: 'bar', value: 'value');
        
        $this->assertSame(
            [
                'foo=value; Path=/; Secure; HttpOnly; SameSite=Lax',
                'bar=value; Path=/; Secure; HttpOnly; SameSite=Lax'
            ],
            $cookies->toHeader()
        );
    }
}