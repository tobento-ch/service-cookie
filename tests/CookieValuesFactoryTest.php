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
use Tobento\Service\Cookie\CookieValuesFactory;
use Tobento\Service\Cookie\CookieValuesFactoryInterface;
use Tobento\Service\Cookie\CookieValuesInterface;
use Tobento\Service\Cookie\Cookies;
use Tobento\Service\Cookie\CookieFactory;

/**
 * CookieValuesFactoryTest
 */
class CookieValuesFactoryTest extends TestCase
{
    public function testConstructMethod()
    {
        $factory = new CookieValuesFactory();
        
        $this->assertInstanceof(CookieValuesFactoryInterface::class, $factory);
    }
    
    public function testCreateCookieValuesFromArrayMethod()
    {
        $factory = new CookieValuesFactory();
        
        $cookieValues = $factory->createCookieValuesFromArray(['foo' => 'Foo']);
        
        $this->assertInstanceof(CookieValuesInterface::class, $cookieValues);
        $this->assertSame(['foo' => 'Foo'], $cookieValues->all());
    }
    
    public function testCreateCookieValuesFromCookiesMethod()
    {
        $factory = new CookieValuesFactory();
        
        $cookies = new Cookies(
            cookieFactory: new CookieFactory(),
        );
        
        $cookies->add(name: 'foo', value: 'Foo');
        $cookies->add(name: 'option[foo]', value: 'Option Foo');
        $cookies->add(name: 'option[bar]', value: 'Option Bar');
        
        $cookieValues = $factory->createCookieValuesFromCookies($cookies);
        
        $this->assertInstanceof(CookieValuesInterface::class, $cookieValues);
        
        $this->assertSame(
            [
                'foo' => 'Foo', 
                'option' => [
                    'foo' => 'Option Foo',
                    'bar' => 'Option Bar',
                ],
            ],
            $cookieValues->all()
        );
    }
}