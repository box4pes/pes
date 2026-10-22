<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Request;

use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @author pes2704
 */
interface RequestParamsInterface {

    /**
     * Vrací parametr requestu získaný z body nebo query (v tomto pořadí).
     *
     * @param ServerRequestInterface $request Objekt request.
     * @param  string $key Klíč (index, jméno) parametru.
     * @param type $default Návratová hodnota, kterou metoda vrací, pokud parametr se zadaným klíčem v requestu neexistuje.
     *
     * @return mixed Hodnota paremetru.
     */
    public function getParam(ServerRequestInterface $request, $key, $default=NULL);

    /**
     * Vrací parametr získaný z parsovaného request body. Obyvklé se jedná o proměnné odeslané html formulářem metodou POST.
     *
     * @param ServerRequestInterface $request Objekt request
     * @param  string $key Klíč (index, jméno) parametru.
     * @param type $default Návratová hodnota, kterou metoda vrací, pokud parametr se zadaným klíčem v body neexistuje. Pokud není zadána je default hodnota NULL.
     *
     * @return mixed Hodnota paremetru.
     */
    public function getParsedBodyParam(ServerRequestInterface $request, $key, $default=NULL);

    /**
     * Vrací parametr získaný z query.
     *
     * @param ServerRequestInterface $request
     * @param  string $key Klíč (index, jméno) parametru.
     * @param type $default Návratová hodnota, kterou metoda vrací, pokud parametr se zadaným klíčem v query neexistuje.
     *
     * @return mixed Hodnota paremetru.
     */
    public function getQueryParam(ServerRequestInterface $request, $key, $default=NULL);

    /**
     * Vrací asociativní pole parametrů requestu získaných z body nebo query (v tomto pořadí).
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    public function getParams(ServerRequestInterface $request);

}
