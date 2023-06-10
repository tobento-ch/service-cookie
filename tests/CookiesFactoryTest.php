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
use Tobento\Service\Cookie\CookiesFactory;
use Tobento\Service\Cookie\CookiesFactoryInterface;
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookiesInterface;
use Tobento\Service\Cookie\Cookie;

/**
 * CookiesFactoryTest
 */
class CookiesFactoryTest extends TestCase
{
    public function testConstructMethod()
    {
        $factory = new CookiesFactory(
            cookieFactory: new CookieFactory(),
        );
        
        $this->assertInstanceof(CookiesFactoryInterface::class, $factory);
    }
    
    public function testCreateCookiesMethod()
    {
        $factory = new CookiesFactory(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies = $factory->createCookies();
        
        $this->assertInstanceof(CookiesInterface::class, $cookies);
        
        $foo = new Cookie(name: 'foo', value: 'value');
        $bar = new Cookie(name: 'bar', value: 'value');
        
        $cookies = $factory->createCookies($foo, $bar);
        
        $this->assertInstanceof(CookiesInterface::class, $cookies);
        $this->assertSame($foo, $cookies->all()[0]);
        $this->assertSame($bar, $cookies->all()[1]);
    }
    
    public function testCreateCookiesFromKeyValuePairs()
    {
        $factory = new CookiesFactory(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies = $factory->createCookiesFromKeyValuePairs([
            'foo' => 'foo value',
            'bar' => [
                'bar1' => 'bar 1',
                'bar2' => [
                    'bar2-1' => 'bar 2-1'
                ],
            ],
        ]);
        
        $this->assertInstanceof(CookiesInterface::class, $cookies);

        $this->assertSame(
            [
                'foo' => 'foo value',
                'bar[bar1]' => 'bar 1',
                'bar[bar2][bar2-1]' => 'bar 2-1',
            ],
            $cookies->column('value', 'name')
        );
    }
    
    public function testCreateCookiesFromArray()
    {
        $factory = new CookiesFactory(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies = $factory->createCookiesFromArray([
            [
                'name' => 'name',
                'value' => 'value',
                'lifetime' => 3600,
                'path' => '/',
                'domain' => '.example.com',
                'secure' => true,
                'httpOnly' => true,
                'sameSite' => 'Lax',
            ],
            'foo' => 'invalid',
        ]);
        
        $this->assertInstanceof(CookiesInterface::class, $cookies);

        $this->assertSame(
            [
                'name' => 'value',
            ],
            $cookies->column('value', 'name')
        );
    }
}