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

use Pes\Http\Environment;

/**
 *
 * @author pes2704
 */
interface EnvironmentFactoryInterface {
    /**
     * Vytvoří objekt Environment z superglobálního pole $_SERVER a streamu, který vytvoří jako kopii php://input.
     *
     * Očekává pole vstupních proměnných s položkami, jaké jsou v poli $_SERVER.
     *
     * @return Environment
     */
    public function createFromGlobals() : Environment;

    /**
     * Vytvoří objekt Environment z pole vstupních proměnných a streamu, který vytvoří jako kopii php://input.
     *
     * Očekává pole vstupních proměnných s položkami, jaké jsou v poli $_SERVER.
     *
     * @param array $serverParams
     */
    public function createFromServerParams(array $serverParams = array()) : Environment;

    /**
     * Vytvoří objekt Environment ze zadaného pole vstupních proměnných a zadaného streamu.
     *
     * Očekává pole vstupních proměnných s položkami, jaké jsou v poli $_SERVER. Očekává, že stream obsahuje body vstupního requestu.
     *
     * $_SERVER is an array containing information such as headers, paths, and script locations. The entries in this array are created by the web server.
     * Pokud v poli $_SERVER neexistuje proměnná HTTP_AUTHORIZATION, metoda se pokusí získat hodnotu HTTP_AUTHORIZATION
     * z HTTP hlavičky Authorization. Hlavičky získá PHP funkcí getallheaders() - alias k apache_request_headers() - pokud tato funkce existuje.
     *
     * @param array $serverParams
     * @param StreamInterface $inputStream
     * @return Environment
     */
    public function create(array $serverParams, $inputStream) : Environment;
}
