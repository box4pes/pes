<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Middleware;

use Psr\Http\Server\MiddlewareInterface;

use Pes\Application\AppInterface;
use Psr\Log\LoggerInterface;

/**
 *
 * @author pes2704
 */
interface AppMiddlewareInterface extends MiddlewareInterface {

    /**
     * Vrací aplikaci nastavenou metodou setApp().
     * @return AppInterface
     */
    public function getApp(): AppInterface;

    /**
     * Nastaví aplikaci. Metoda musí být volána vždy při spuštění middleware v aplikaci. Přidání zajistí funkčnost metody getApp() a dostupnost
     * aplikace a jejích metod v těle middleware.
     *
     * @param AppInterface $app
     * @return \Pes\Middleware\AppMiddlewareInterface
     */
    public function setApp(AppInterface $app): AppMiddlewareInterface;

    /**
     * Je nastaven logger?
     * @return bool
     */
    public function hasLogger(): bool;

    /**
     * Vrací nastavený logger.
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface;

    /**
     * Nastaví logger.
     * @param LoggerInterface $logger
     * @return \Pes\Middleware\AppMiddlewareInterface
     */
    public function setLogger(LoggerInterface $logger): AppMiddlewareInterface;
}
