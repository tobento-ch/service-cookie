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

namespace Tobento\Service\Cookie\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tobento\Service\Cookie\CookieValuesFactoryInterface;
use Tobento\Service\Cookie\CookiesFactoryInterface;
use Tobento\Service\Cookie\CookiesProcessorInterface;
use Tobento\Service\Cookie\CookieValuesInterface;
use Tobento\Service\Cookie\CookiesInterface;

/**
 * Cookies
 */
class Cookies implements MiddlewareInterface
{
    /**
     * Create a new Cookies.
     *
     * @param CookieValuesFactoryInterface $cookieValuesFactory
     * @param CookiesFactoryInterface $cookiesFactory
     * @param CookiesProcessorInterface $cookiesProcessor
     */
    public function __construct(
        protected CookieValuesFactoryInterface $cookieValuesFactory,
        protected CookiesFactoryInterface $cookiesFactory,
        protected CookiesProcessorInterface $cookiesProcessor,
    ) {}
    
    /**
     * Process the middleware.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // handle cookie values:
        $cookieValues = $this->cookieValuesFactory->createCookieValuesFromArray($request->getCookieParams());
        $cookieValues = $this->cookiesProcessor->processCookieValues($cookieValues);
        $request = $request->withCookieParams($cookieValues->all());
        $request = $request->withAttribute(CookieValuesInterface::class, $cookieValues);
        
        // handle cookies:
        $cookies = $this->cookiesFactory->createCookies();
        $request = $request->withAttribute(CookiesInterface::class, $cookies);
        
        // handle request:
        $response = $handler->handle($request);
        
        // handle cookies response:
        $cookies = $this->cookiesProcessor->processCookies($cookies);

        if (empty($cookies->all())) {
            return $response;
        }
        
        $cookieHeader = $response->getHeader('Set-Cookie');
        
        return $response->withHeader('Set-Cookie', $cookies->toHeader($cookieHeader));
    }
}