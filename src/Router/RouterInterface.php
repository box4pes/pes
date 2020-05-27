<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Router;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Action\ResourceInterface;

/**
 *
 * @author pes2704
 */
interface RouterInterface extends MiddlewareInterface {

    /**
     *
     * @param \Pes\Router\RouteInterface $route
     * @return void
     */
    public function addRoute(RouteInterface $route): void ;
    public function route(ServerRequestInterface $request);
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;

    /**
     * Vrací objekt Route, který byl použit při posledním routování.
     *
     * @return \Pes\Router\RouteInterface
     */
    public function getMatchedRoute(): RouteInterface;

    /**
     * Vrací request použitý při posledním routování.
     * @return ServerRequestInterface
     */
    public function getMatchedRequest(): ServerRequestInterface;

}
