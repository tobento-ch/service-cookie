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

namespace Tobento\Service\Cookie\Test\Middleware;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Cookie\Middleware\Cookies;
use Tobento\Service\Cookie\CookieValuesFactoryInterface;
use Tobento\Service\Cookie\CookieValuesFactory;
use Tobento\Service\Cookie\CookiesFactoryInterface;
use Tobento\Service\Cookie\CookiesFactory;
use Tobento\Service\Cookie\CookieFactoryInterface;
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookiesProcessorInterface;
use Tobento\Service\Cookie\CookiesProcessor;
use Tobento\Service\Cookie\CookieValuesInterface;
use Tobento\Service\Cookie\CookiesInterface;
use Tobento\Service\Encryption\EncrypterInterface;
use Tobento\Service\Encryption\Crypto;
use Tobento\Service\Middleware\MiddlewareDispatcher;
use Tobento\Service\Middleware\AutowiringMiddlewareFactory;
use Tobento\Service\Middleware\FallbackHandler;
use Tobento\Service\Container\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Factory\Psr17Factory;


/**
 * CookiesTest
 */
class CookiesTest extends TestCase
{
    public function testMiddleware()
    {
        $container = new Container();
        $container->set(CookieValuesFactoryInterface::class, CookieValuesFactory::class);
        $container->set(CookieFactoryInterface::class, CookieFactory::class);
        $container->set(CookiesFactoryInterface::class, CookiesFactory::class);
        $container->set(CookiesProcessorInterface::class, CookiesProcessor::class);
        
        // Response Test:
        $dispatcher = new MiddlewareDispatcher(
            new FallbackHandler((new Psr17Factory())->createResponse(404)),
            new AutowiringMiddlewareFactory($container)
        );

        $dispatcher->add(Cookies::class);

        $dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
            $cookies = $request->getAttribute(CookiesInterface::class);
            $cookies->add(name: 'foo', value: 'Foo');
            $cookies->add(name: 'bar', value: 'Bar');
            return $handler->handle($request);
        });

        $request = (new Psr17Factory())->createServerRequest('GET', 'https://example.com');

        $response = $dispatcher->handle($request);
        
        $fooCookie = $this->fetchCookies($response)['foo'] ?? '';
        
        $this->assertSame('Foo', $fooCookie);
        
        // Request Test:
        $dispatcher = new MiddlewareDispatcher(
            new FallbackHandler((new Psr17Factory())->createResponse(404)),
            new AutowiringMiddlewareFactory($container)
        );

        $dispatcher->add(Cookies::class);

        $dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
            $cookieValues = $request->getAttribute(CookieValuesInterface::class);            
            $response = $handler->handle($request);
            $response->getBody()->write($cookieValues->get('foo', ''));
            return $response;
        });

        $request = (new Psr17Factory())->createServerRequest('GET', 'https://example.com');
        $request = $request->withCookieParams($this->fetchCookies($response));
        
        $response = $dispatcher->handle($request);
        
        $this->assertSame('Foo', (string)$response->getBody());
    }
    
    public function testMiddlewareWithEncrypter()
    {
        $container = new Container();
        $container->set(CookieValuesFactoryInterface::class, CookieValuesFactory::class);
        $container->set(CookieFactoryInterface::class, CookieFactory::class);
        $container->set(CookiesFactoryInterface::class, CookiesFactory::class);
        $container->set(CookiesProcessorInterface::class, CookiesProcessor::class);
        
        $container->set(EncrypterInterface::class, function() {
            $key = (new Crypto\KeyGenerator())->generateKey();
            $encrypterFactory = new Crypto\EncrypterFactory();
            return $encrypterFactory->createEncrypter(key: $key);
        });
        
        // Response Test:
        $dispatcher = new MiddlewareDispatcher(
            new FallbackHandler((new Psr17Factory())->createResponse(404)),
            new AutowiringMiddlewareFactory($container)
        );

        $dispatcher->add(Cookies::class);

        $dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
            $cookies = $request->getAttribute(CookiesInterface::class);
            $cookies->add(name: 'foo', value: 'Foo');
            $cookies->add(name: 'bar', value: 'Bar');
            return $handler->handle($request);
        });

        $request = (new Psr17Factory())->createServerRequest('GET', 'https://example.com');

        $response = $dispatcher->handle($request);
        
        $fooCookie = $this->fetchCookies($response)['foo'] ?? '';
        
        $this->assertSame(
            'Foo',
            $container->get(EncrypterInterface::class)->decrypt($fooCookie)
        );
        
        // Request Test:
        $dispatcher = new MiddlewareDispatcher(
            new FallbackHandler((new Psr17Factory())->createResponse(404)),
            new AutowiringMiddlewareFactory($container)
        );

        $dispatcher->add(Cookies::class);

        $dispatcher->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
            $cookieValues = $request->getAttribute(CookieValuesInterface::class);            
            $response = $handler->handle($request);
            $response->getBody()->write($cookieValues->get('foo', ''));
            return $response;
        });

        $request = (new Psr17Factory())->createServerRequest('GET', 'https://example.com');
        $request = $request->withCookieParams($this->fetchCookies($response));
        
        $response = $dispatcher->handle($request);
        
        $this->assertSame('Foo', (string)$response->getBody());
    }
    
    protected function fetchCookies(ResponseInterface $response): array
    {
        $cookies = [];
        $cookieHeader = $response->getHeaders()['Set-Cookie'] ?? [];

        foreach($cookieHeader as $cookie) {
            $cookie = explode('=', $cookie);
            $cookies[$cookie[0]] = rawurldecode(substr(
                (string)$cookie[1],
                0,
                (int)strpos((string)$cookie[1], ';')
            ));
        }

        return $cookies;
    }
}