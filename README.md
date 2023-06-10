# Cookie Service

The cookie service provides a way for managing cookies for PHP applications.

## Table of Contents

- [Getting started](#getting-started)
    - [Requirements](#requirements)
    - [Highlights](#highlights)
- [Documentation](#documentation)
    - [Basic Usage](#basic-usage)
        - [Using Middleware](#using-middleware)
    - [Read Cookie](#read-cookie)
        - [Cookie Values Factory](#cookie-values-factory)
        - [Cookie Values](#cookie-values)
    - [Write Cookie](#write-cookie)
        - [Cookies Factory](#cookies-factory)
        - [Cookies](#cookies)
        - [Cookie Factory](#cookie-factory)
        - [Cookie](#cookie)
    - [Cookies Processor](#cookies-processor)
- [Credits](#credits)
___

# Getting started

Add the latest version of the cookie service project running this command.

```
composer require tobento/service-cookie
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

# Documentation

## Basic Usage

Simple example using the cookie service in your application:

```php
use Tobento\Service\Cookie;
use Tobento\Service\Encryption\EncrypterInterface;

// Set up cookie values for reading cookies:
$cookieValues = new Cookie\CookieValues($_COOKIE);

// You may use the default processor for decryption:
$processor = new Cookie\CookiesProcessor(
    encrypter: null, // null|EncrypterInterface
    whitelistedCookies: ['PHPSESSID'],
);

$cookieValues = $processor->processCookieValues($cookieValues);

// Start reading values:
$value = $cookieValues->get('foo');

// Set up Cookies for writing:
$cookies = new Cookie\Cookies(
    cookieFactory: new Cookie\CookieFactory(),
);

// Adding cookies:
$cookies->add('name', 'value');

// You may use the default processor for encryption:
$cookies = $processor->processCookies($cookies);

// Send cookies before any header is sent:
foreach($cookies as $cookie) {
    $cookie->send();
}
```

### Using Middleware

You may prefer to use the ```Cookies``` middlware:

```php
use Psr\Http\Server\MiddlewareInterface;
use Tobento\Service\Cookie\Middleware;
use Tobento\Service\Cookie\CookieValuesFactory;
use Tobento\Service\Cookie\CookiesFactory;
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookiesProcessor;
use Tobento\Service\Cookie\CookieValuesFactoryInterface;
use Tobento\Service\Cookie\CookiesFactoryInterface;
use Tobento\Service\Cookie\CookiesProcessorInterface;

$middleware = new Middleware\Cookies(
    // CookieValuesFactoryInterface
    cookieValuesFactory: new CookieValuesFactory(),
    
    // CookiesFactoryInterface
    cookiesFactory: new CookiesFactory(new CookieFactory()),
    
    // CookiesProcessorInterface
    cookiesProcessor: new CookiesProcessor(),
);

var_dump($middleware instanceof MiddlewareInterface);
// bool(true)
```

**Read and write cookies**

```php
use Psr\Http\Message\ServerRequestInterface;
use Tobento\Service\Cookie\CookieValuesInterface;
use Tobento\Service\Cookie\CookiesInterface;

// ...

public function index(ServerRequestInterface $request): void
{
    // read cookies:
    $cookieValues = $request->getAttribute(CookieValuesInterface::class);
    
    $value = $cookieValues->get('foo');
    
    // or
    var_dump($request->getCookieParams());
    
    // write cookies:
    $cookies = $request->getAttribute(CookiesInterface::class);
    
    $cookies->add('name', 'value');
}
```

## Read Cookie

### Cookie Values Factory

You may use the ```CookieValuesFactory``` to create cookie values:

**createCookieValuesFromArray**

```php
use Tobento\Service\Cookie\CookieValuesFactory;
use Tobento\Service\Cookie\CookieValuesFactoryInterface;
use Tobento\Service\Cookie\CookieValuesInterface;

$factory = new CookieValuesFactory();

var_dump($factory instanceof CookieValuesFactoryInterface);
// bool(true)

$cookieValues = $factory->createCookieValuesFromArray($_COOKIE);

var_dump($cookieValues instanceof CookieValuesInterface);
// bool(true)
```

You may check out the [Cookie Values](#cookie-values) to learn more about it.

**createCookieValuesFromCookies**

```php
use Tobento\Service\Cookie\CookieValuesFactory;
use Tobento\Service\Cookie\CookiesInterface;

$factory = new CookieValuesFactory();

$cookieValues = $factory->createCookieValuesFromCookies(
    $cookies // CookiesInterface
);
```

You may check out the [Cookie Values](#cookie-values) to learn more about it.

You may check out the [Cookies](#cookies) to learn more about it.

### Cookie Values

```php
use Tobento\Service\Cookie\CookieValues;
use Tobento\Service\Cookie\CookieValuesInterface;

$cookieValues = new CookieValues(['name' => 'value']);

var_dump($cookieValues instanceof CookieValuesInterface);
// bool(true)
```

**get**

Get a cookie value by name.

```php
use Tobento\Service\Cookie\CookieValues;

$values = new CookieValues([
    'name' => 'value',
    'meta' => [
        'color' => 'red',
    ],
]);

var_dump($values->get(name: 'name'));
// string(5) "value"

// supports array access:
var_dump($values['name']);

// supports dot notation:
var_dump($values->get(name: 'meta.color'));
// string(3) "red"

// with a default if not exists:
var_dump($values->get(name: 'foo', default: 'value'));
// string(5) "value"

var_dump($values->get(name: 'foo'));
// NULL
```

**has**

Check if a cookie value by name exists.

```php
use Tobento\Service\Cookie\CookieValues;

$values = new CookieValues([
    'name' => 'value',
    'meta' => [
        'color' => 'red',
    ],
]);

var_dump($values->has(name: 'name'));
// bool(true)

// supports dot notation:
var_dump($values->has(name: 'meta.color'));
// bool(true)

var_dump($values->has(name: 'foo'));
// bool(false)
```

**all**

Get a cookie values.

```php
use Tobento\Service\Cookie\CookieValues;

$values = new CookieValues([
    'name' => 'value',
    'meta' => [
        'color' => 'red',
    ],
]);

var_dump($values->all());
// array(2) {["name"]=> string(5) ... }

// or just
foreach($values as $value) {}
```

**map**

Map over each of the cookie values returning a new instance.

```php
use Tobento\Service\Cookie\CookieValues;

$values = new CookieValues([
    'name' => 'value',
]);

$valuesNew = $values->map(function(mixed $value, string|int $name): mixed {
    return $value;
});
```

**withValues**

Returns a new instance with the specified values.

```php
use Tobento\Service\Cookie\CookieValues;

$values = new CookieValues([
    'name' => 'value',
]);

$valuesNew = $values->withValues(['name' => 'value']);
```

## Write Cookie

### Cookies Factory

You may use the ```CookiesFactory``` to create cookies:

**createCookies**

```php
use Tobento\Service\Cookie\CookiesFactory;
use Tobento\Service\Cookie\CookiesFactoryInterface;
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookiesInterface;
use Tobento\Service\Cookie\CookieInterface;
use Tobento\Service\Cookie\Cookie;

$cookiesFactory = new CookiesFactory(
    cookieFactory: new CookieFactory(),
);

var_dump($cookiesFactory instanceof CookiesFactoryInterface);
// bool(true)

$cookies = $cookiesFactory->createCookies();

var_dump($cookies instanceof CookiesInterface);
// bool(true)

$cookies = $cookiesFactory->createCookies(
    new Cookie(name: 'name', value: 'value'),
);
```

You may check out the [Cookie Factory](#cookie-factory) to learn more about it.

You may check out the [Cookies](#cookies) to learn more about it.

**createCookiesFromKeyValuePairs**

Create new cookies from key/value pairs. May be used for creating cookies from the ```$_COOKIE``` superglobal.
     
```php
use Tobento\Service\Cookie\CookiesFactory;
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookiesInterface;

$cookiesFactory = new CookiesFactory(
    cookieFactory: new CookieFactory(),
);

$cookies = $cookiesFactory->createCookiesFromKeyValuePairs([
    'name' => 'value',
]);

var_dump($cookies instanceof CookiesInterface);
// bool(true)
```

You may check out the [Cookie Factory](#cookie-factory) to learn more about it.

You may check out the [Cookies](#cookies) to learn more about it.

**createCookiesFromArray**
     
```php
use Tobento\Service\Cookie\CookiesFactory;
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookiesInterface;

$cookiesFactory = new CookiesFactory(
    cookieFactory: new CookieFactory(),
);

$cookies = $cookiesFactory->createCookiesFromArray([
    [
        'name' => 'name',
        'value' => 'value',

        // The duration in seconds until the cookie will expire.
        'lifetime' => 3600,

        'path' => '/',
        'domain' => '.example.com',
        'secure' => true,
        'httpOnly' => true,
        'sameSite' => 'Lax',
    ],
]);

var_dump($cookies instanceof CookiesInterface);
// bool(true)
```

You may check out the [Cookie Factory](#cookie-factory) to learn more about it.

You may check out the [Cookies](#cookies) to learn more about it.

### Cookies

```php
use Tobento\Service\Cookie\Cookies;
use Tobento\Service\Cookie\CookiesInterface;
use Tobento\Service\Cookie\CookieFactory;

$cookies = new Cookies(
    cookieFactory: new CookieFactory(),
);

var_dump($cookies instanceof CookiesInterface);
// bool(true)
```

You may check out the [Cookie Factory](#cookie-factory) to learn more about it.

**addCookie**

Add a cookie object.

```php
use Tobento\Service\Cookie\Cookies;
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\Cookie;

$cookies = new Cookies(
    cookieFactory: new CookieFactory(),
);

$cookies->addCookie(
    new Cookie(name: 'name', value: 'value')
);
```

You may check out the [Cookie Factory](#cookie-factory) to learn more about it.

**add**

Add a cookie.

```php
use Tobento\Service\Cookie\Cookies;
use Tobento\Service\Cookie\CookieFactory;

$cookies = new Cookies(
    cookieFactory: new CookieFactory(),
);

$cookies->add(
    name: 'name', // string
    value: 'value', // string
    
    // The duration in seconds until the cookie will expire.
    lifetime: 3600, // null|int
    
    // if null (default) it uses default value from factory.
    path: '/', // null|string
    
    // if null (default) it uses default value from factory.
    domain: 'example.com', // null|string
    
    // if null (default) it uses default value from factory.
    secure: true, // null|bool
    
    httpOnly: true, // default true if not set
    
    // if null (default) it uses default value from factory.
    sameSite: 'Lax', // string
);
```

You may change the default values by a custom ```CookieFactory::class```.

You may check out the [Cookie Factory](#cookie-factory) to learn more about it.

**get**

Returns a cookie by the specified parameters or null if not found.

```php
$cookie = $cookies->get(name: 'name');

// by name and path:
$cookie = $cookies->get(name: 'name', path: 'path');

// by name and domain:
$cookie = $cookies->get(name: 'name', domain: 'example.com');

// by name, path and domain:
$cookie = $cookies->get(name: 'name', path: 'path', domain: 'example.com');
```

**clear**

Clear a cookie by the specified parameters.

```php
// clears all with the same name:
$cookies->clear(name: 'name');

// clears only with same name and path:
$cookies->clear(name: 'name', path: 'path');

// clears only with same name and domain:
$cookies->clear(name: 'name', domain: 'example.com');

// clears only with same name, path and domain:
$cookies->clear(name: 'name', path: 'path', domain: 'example.com');
```

**column**

Sometimes you may need only specific columns from the cookies returning an ```array```.

```php
$names = $cookies->column('name');

$values = $cookies->column('value');

// values keyed by name:
$values = $cookies->column('value', 'name');
```

**first**

Returns the first cookie, otherwise null.

```php
use Tobento\Service\Cookie\CookieInterface;

$cookie = $cookies->first();

var_dump($cookie instanceof CookieInterface);
// bool(true) or NULL
```

**all**

Returns all cookies.

```php
use Tobento\Service\Cookie\CookieInterface;

$cookies = $cookies->all();
// array<int, CookieInterface>

// or just
foreach($cookies as $cookie) {}
```

**filter**

Returns a new instance with the filtered cookies.

```php
use Tobento\Service\Cookie\CookieInterface;

$cookiesNew = $cookies->filter(
    fn(CookieInterface $c): bool => $c->name() === 'foo'
);
```

**name**

Returns a new instance with the name filtered.

```php
$cookiesNew = $cookies->name('foo');
```

**path**

Returns a new instance with the path filtered.

```php
$cookiesNew = $cookies->path('/');
```

**domain**

Returns a new instance with the domain filtered.

```php
$cookiesNew = $cookies->domain('example.com');
```

**map**

Map over each of the cookies returning a new instance.

```php
use Tobento\Service\Cookie\CookieInterface;

$cookiesNew = $cookies->map(function(CookieInterface $c): CookieInterface {
    return $c;
});
```

**toHeader**

Returns the cookie header.

```php
var_dump($cookies->toHeader());
// array(1) { [0]=> string(127) "name=value; Expires=Tuesday, 06-Jun-2023 18:34:46 GMT; Max-Age=3600; Path=/; Domain=example.com; Secure; HttpOnly; SameSite=Lax" }
```

### Cookie Factory

You may use the ```CookieFactory``` to create a cookie:

**createCookie**

```php
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookieFactoryInterface;
use Tobento\Service\Cookie\CookieInterface;

$cookieFactory = new CookieFactory(
    // default values:
    path: '/',
    domain: '',
    secure: true,
    sameSite: 'Lax',
);

var_dump($cookieFactory instanceof CookieFactoryInterface);
// bool(true)

$cookie = $cookieFactory->createCookie(
    name: 'name', // string
    value: 'value', // string
    
    // The duration in seconds until the cookie will expire.
    lifetime: 3600, // null|int
    
    // if null (default) it uses default value.
    path: '/', // null|string
    
    // if null (default) it uses default value.
    domain: 'example.com', // null|string
    
    // if null (default) it uses default value.
    secure: true, // null|bool
    
    httpOnly: true, // default true if not set
    
    // if null (default) it uses default value.
    sameSite: 'Lax', // string
);

var_dump($cookie instanceof CookieInterface);
// bool(true)
```

You may check out the [Cookie](#cookie) to learn more about it.

**createCookieFromArray**

```php
use Tobento\Service\Cookie\CookieFactory;
use Tobento\Service\Cookie\CookieInterface;

$cookieFactory = new CookieFactory(
    // default values:
    path: '/',
    domain: '',
    secure: true,
    sameSite: 'Lax',
);

$cookie = $cookieFactory->createCookieFromArray([
    'name' => 'name',
    'value' => 'value',

    // The duration in seconds until the cookie will expire.
    'lifetime' => 3600,

    'path' => '/',
    'domain' => '.example.com',
    'secure' => true,
    'httpOnly' => true,
    'sameSite' => 'Lax',
]);

var_dump($cookie instanceof CookieInterface);
// bool(true)
```

You may check out the [Cookie](#cookie) to learn more about it.

### Cookie

```php
use Tobento\Service\Cookie\Cookie;
use Tobento\Service\Cookie\CookieInterface;
use Tobento\Service\Cookie\SameSite;
use Tobento\Service\Cookie\SameSiteInterface;

$cookie = new Cookie(
    name: 'name', // string
    value: 'value', // string
    
    // The duration in seconds until the cookie will expire.
    lifetime: 3600, // null|int
    
    path: '/', // string
    
    domain: '', // string
    
    secure: true, // bool
    
    httpOnly: true, // bool
    
    sameSite: new SameSite(value: 'Lax'), // null|SameSiteInterface
);

var_dump($cookie instanceof CookieInterface);
// bool(true)

var_dump($cookie->name());
// string(4) "name"

var_dump($cookie->value());
// string(5) "value"

var_dump($cookie->lifetime());
// int(3600) or NULL

var_dump($cookie->path());
// string(1) "/"

var_dump($cookie->domain());
// string(0) ""

var_dump($cookie->secure());
// bool(true)

var_dump($cookie->httpOnly());
// bool(true)

var_dump($cookie->sameSite());
// null|SameSiteInterface

var_dump($cookie->sameSite()?->value());
// string(3) "Lax"

var_dump($cookie->expires());
// int(1686155135) or NULL

var_dump($cookie->toHeader());
// string(109) "name=value; Expires=Wednesday, 07-Jun-2023 16:27:47 GMT; Max-Age=3600; Path=/; Secure; HttpOnly; SameSite=Lax"

// send the cookie uses setcookie() method:
var_dump($cookie->send());
// bool(true) on success, otherwise false
```

## Cookies Processor

You may use the default cookies processor for encrypting and decrypting cookie values or you may create a custom processor suiting your needs.

```php
use Tobento\Service\Cookie\CookiesProcessor;
use Tobento\Service\Cookie\CookiesProcessorInterface;
use Tobento\Service\Encryption\EncrypterInterface;

$processor = new CookiesProcessor(
    encrypter: null, // null|EncrypterInterface
    whitelistedCookies: ['PHPSESSID'],
);

var_dump($processor instanceof CookiesProcessorInterface);
// bool(true)
```

Check out the [Encryption Service](https://github.com/tobento-ch/service-encryption) to learn more about it.

**processCookieValues**

Decrypting the cookie values.

```php
use Tobento\Service\Cookie\CookieValuesInterface;

$cookieValues = $processor->processCookieValues(
    cookieValues: $cookieValues // CookieValuesInterface
);
```

You may check out the [Cookie Values](#cookie-values) to learn more about it.

**processCookies**

Encrypting the cookie values.

```php
use Tobento\Service\Cookie\CookiesInterface;

$cookieValues = $processor->processCookies(
    cookies: $cookies // CookiesInterface
);
```

You may check out the [Cookies](#cookies) to learn more about it.

**whitelistCookie**

You may use the ```whitelistCookie``` to add a cookie the whitelist meaning no encryption/decryption is done.

```php
use Tobento\Service\Cookie\CookiesInterface;

$processor->whitelistCookie(name: 'foo');
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)