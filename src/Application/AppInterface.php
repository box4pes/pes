<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Application;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Container\ContainerInterface;
use Pes\Application\UriInfoInterface;

/**
 *
 * @author pes2704
 */
interface AppInterface {

    /**
     * Výchozí http request, se kterým byl spuštěn skript
     * @return RequestInterface
     */
    public function getServerRequest(): ServerRequestInterface;

    /**
     * Výchozí http request, se kterým byl spuštěn skript
     * @param RequestInterface $request
     * @return AppInterface
     */
    public function setServerRequest(ServerRequestInterface $request): AppInterface;

    /**
     * Kontejner aplikace - kontejner poskytující služby společné pro celou aplikaci nebo jediný kontejner použtý v aplikaci
     * @return ContainerInterface
     */
    public function getAppContainer();

    /**
     * Kontejner aplikace - kontejner poskytující služby společné pro celou aplikaci nebo jediný kontejner použtý v aplikaci
     * @param ContainerInterface $appContainer
     * @return AppInterface
     */
    public function setAppContainer(ContainerInterface $appContainer): AppInterface;

    /**
     * Vykoná middleware.
     * Zadanému middleware předá request přijatý aplikací (předaný z HTTP serveru) a handler pro ošetření situace, kdy middleware není schpen request řádně zpracovat a pokusí se volat request handler.
     * Následně volá metodu process() připraveného middleware.
     *
     * @param MiddlewareInterface $middleware Middleware pro zpracování requestu
     * @param RequestHandlerInterface $fallbackHandler Handler pro vrácení korektního response v případě, že middleware nedokáže request zpracovat.
     * @return ResponseInterface Http response
     */
    public function run(MiddlewareInterface $middleware, RequestHandlerInterface $fallbackHandler): ResponseInterface;
}
