<?php
/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
namespace Pes\Http\Cookies;

use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\HeadersInterface;

use InvalidArgumentException;

/**
 * RequestCookies
 */
class RequestCookies implements RequestCookiesInterface {

    /**
     * Cookies from HTTP request
     *
     * @var array
     */
    protected $requestCookies = [];

    /**
     *
     * @param ServerRequestInterface $request
     * @return array
     */
    public function __construct(ServerRequestInterface $request) {
        foreach ($request->getCookieParams() as $name => $value) {
            $this->requestCookies[$name] = (new RequestCookie)->setName($name)->setValue($value);
        }
        return $this->requestCookies;
//        return (new Pes\Http\Factory\CookiesArrayFactory())->extractFromCookieHeader($request->getHeader('Cookie'));
    }

    /**
     * Get request cookie
     *
     * @param  string $name    Cookie name
     * @param  mixed  $default Cookie default value
     *
     * @return mixed Cookie value if present, else empty string
     */
    public function getRequestCookie($name)
    {
        return isset($this->requestCookies[$name]) ? $this->requestCookies[$name] : '';
    }
}
