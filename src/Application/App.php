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

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use Pes\Middleware\AppMiddlewareInterface;
use Pes\Http\Factory\BodyFactory;

/**
 * Description of App
 *
 * @author pes2704
 */
class App implements AppInterface {

    /**
     * @var RequestInterface
     */
    protected $serverRequest;

    /**
     * @var UrlInfoInterface
     */
    protected $uriInfo;

    /**
     *
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * @var ContainerInterface
     */
    protected $appContainer;

    /**
     * {@inheritdoc}
     * @return ServerRequestInterface
     */
    public function getServerRequest(): ServerRequestInterface {
        return $this->serverRequest;
    }

    /**
     * {@inheritdoc}
     * @param ServerRequestInterface $appRequest
     * @return \Pes\Application\AppInterface
     */
    public function setServerRequest(ServerRequestInterface $appRequest): AppInterface {
        $this->serverRequest = $appRequest;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return ContainerInterface
     */
    public function getAppContainer() {
        return $this->appContainer;
    }

    /**
     * {@inheritdoc}
     * @param ContainerInterface $appContainer
     * @return AppInterface
     */
    public function setAppContainer(ContainerInterface $appContainer): AppInterface {
        $this->appContainer = $appContainer;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return bool
     */
    public function hasLogger(): bool {
        return isset($this->logger);
    }

    /**
     * {@inheritdoc}
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     * @param LoggerInterface $logger
     * @return \Pes\Middleware\AppMiddlewareInterface
     */
    public function setLogger(LoggerInterface $logger): AppMiddlewareInterface {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Vykoná middleware.
     * Zadanému middleware předá request přijatý aplikací (předaný z HTTP serveru) a handler pro ošetření situace, kdy middleware není schpen request řádně zpracovat a pokusí se volat request handler.
     * Tato implementace jako request handler pro takovou situaci nastaví Pes\Middleware\NoMatchSelectorItemRequestHandler.
     * Následně volá metodu process() připraveného middleware.
     *
     * @param MiddlewareInterface $middleware Middleware pro zpracování requestu
     * @param RequestHandlerInterface $fallbackHandler Handler pro vrácení korektního response v případě, že middleware nedokáže request zpracovat.
     * @return ResponseInterface Http response
     */
    public function run(MiddlewareInterface $middleware, RequestHandlerInterface $fallbackHandler): ResponseInterface {
        if ($middleware instanceof AppMiddlewareInterface) {
            $middleware->setApp($this);
        }
        $response = $middleware->process($this->serverRequest, $fallbackHandler);

        /**
         * This is to be in compliance with RFC 2616, Section 9.
         * If the incoming request method is HEAD, we need to ensure that the response body
         * is empty as the request may fall back on a GET route handler which could potentially append content to the response body
         * https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
         */
        $method = strtoupper($this->serverRequest->getMethod());
        if ($method === 'HEAD') {
            $emptyBody = (new BodyFactory())->createStream('');
            $response = $response->withBody($emptyBody);
        }
        return $response;
    }
}
