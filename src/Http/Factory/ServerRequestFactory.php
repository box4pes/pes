<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Factory;

use Psr\Http\Message\ServerRequestFactoryInterface;

use Pes\Http\Environment;
use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Request;


/**
 * Description of RequestFactory
 *
 * @author pes2704
 */
class ServerRequestFactory implements ServerRequestFactoryInterface, EnvironmentAcceptInterface {

    public function createServerRequest(string $method, $uri, array $serverParams = array()): ServerRequestInterface {
        $environment = EnvironmentFactory::createFromServerParams($serverParams);
        $headers = (new HeadersFactory())->createFromEnvironment($environment);
        $cookies = (new CookiesArrayFactory())->extractFromCookieHeader($headers->get('Cookie'));
        if (!isset($cookies)) {
            $cookies = $_COOKIE;
        }
        $body = ( new BodyFactory())->createFromEnvironment($environment);
        $uploadedFiles = (new FilesFactory())->createFiles();
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);

        return $request;
    }

    /**
     * Factory method. Create new request object (Pes\Http\Request) with data extracted from the Environment object
     *
     * @param  Environment $environment
     *
     * @return ServerRequestInterface
     */
    public function createFromEnvironment(Environment $environment): ServerRequestInterface
    {
        $method = $environment->get('REQUEST_METHOD');
        $uri = (new UriFactory())->createFromEnvironment($environment);
        $headers = (new HeadersFactory())->createFromEnvironment($environment);
        $cookies = (new CookiesArrayFactory())->extractFromCookieHeader($headers->get('Cookie'));
        if (!isset($cookies)) {
            $cookies = $_COOKIE;
        }
        $serverParams = $environment->getArrayCopy();
        $body = ( new BodyFactory())->createFromEnvironment($environment);
        $uploadedFiles = (new FilesFactory())->createFiles();
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);

        return $request;
    }

}
