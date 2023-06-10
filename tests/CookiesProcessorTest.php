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
use Tobento\Service\Cookie\CookiesProcessor;
use Tobento\Service\Cookie\CookiesProcessorInterface;
use Tobento\Service\Cookie\CookieValues;
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookieValuesFactory;
use Tobento\Service\Cookie\Cookies;
use Tobento\Service\Encryption\Crypto;

/**
 * CookiesProcessorTest
 */
class CookiesProcessorTest extends TestCase
{
    public function testConstructMethod()
    {
        $processor = new CookiesProcessor(
            encrypter: null,
        );
        
        $this->assertInstanceof(CookiesProcessorInterface::class, $processor);
    }
    
    public function testProcessCookieValuesMethod()
    {
        $processor = new CookiesProcessor(
            encrypter: null,
        );
        
        $cookieValues = new CookieValues(['name' => 'value']);
        
        $cookieValues = $processor->processCookieValues(
            cookieValues: $cookieValues
        );

        $this->assertSame('value', $cookieValues->get('name'));
    }
    
    public function testProcessMethodsWithEncrypterAndWhitelist()
    {
        $key = (new Crypto\KeyGenerator())->generateKey();
        
        $encrypterFactory = new Crypto\EncrypterFactory();
        
        $encrypter = $encrypterFactory->createEncrypter(key: $key);
        
        $processor = new CookiesProcessor(
            encrypter: $encrypter,
            whitelistedCookies: ['foo'],
        );
        
        $processor->whitelistCookie(name: 'bar');
        $processor->whitelistCookie(name: 'option[bar]');

        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo', value: 'Foo');
        $cookies->add(name: 'bar', value: 'Bar');
        $cookies->add(name: 'color', value: 'Color');
        $cookies->add(name: 'meta[foo]', value: 'Meta Foo');
        $cookies->add(name: 'meta[bar][zoo]', value: 'Meta Bar Zoo');
        $cookies->add(name: 'option[foo]', value: 'Option Foo');
        $cookies->add(name: 'option[bar]', value: 'Option Bar');
        
        $this->assertSame('Foo', $cookies->get('foo')?->value());
        $this->assertSame('Bar', $cookies->get('bar')?->value());
        $this->assertSame('Color', $cookies->get('color')?->value());
        $this->assertSame('Meta Foo', $cookies->get('meta[foo]')?->value());
        $this->assertSame('Meta Bar Zoo', $cookies->get('meta[bar][zoo]')?->value());
        $this->assertSame('Option Foo', $cookies->get('option[foo]')?->value());
        $this->assertSame('Option Bar', $cookies->get('option[bar]')?->value());
        
        $cookies = $processor->processCookies(
            cookies: $cookies
        );
        
        $this->assertSame('Foo', $cookies->get('foo')?->value());
        $this->assertSame('Bar', $cookies->get('bar')?->value());
        $this->assertNotSame('Color', $cookies->get('color')?->value());
        $this->assertNotSame('Meta Foo', $cookies->get('meta[foo]')?->value());
        $this->assertNotSame('Meta Bar Zoo', $cookies->get('meta[bar][zoo]')?->value());
        $this->assertNotSame('Option Foo', $cookies->get('option[foo]')?->value());
        $this->assertSame('Option Bar', $cookies->get('option[bar]')?->value());
        
        $cookieValues = (new CookieValuesFactory())->createCookieValuesFromCookies($cookies);
        
        $cookieValues = $processor->processCookieValues(
            cookieValues: $cookieValues
        );
        
        $this->assertSame('Foo', $cookieValues->get('foo'));
        $this->assertSame('Bar', $cookieValues->get('bar'));
        $this->assertSame('Color', $cookieValues->get('color'));
        $this->assertSame('Meta Foo', $cookieValues->get('meta.foo'));
        $this->assertSame('Meta Bar Zoo', $cookieValues->get('meta.bar.zoo'));
        $this->assertSame('Option Bar', $cookieValues->get('option.bar'));
    }
}