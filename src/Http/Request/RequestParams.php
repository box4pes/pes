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
 * Description of Request
 *
 * @author pes2704
 */
class RequestParams implements RequestParamsInterface {
    
    private $escapeStrings;

    public function __construct($escapeStrings = false) {
        $this->escapeStrings = $escapeStrings;
    }

    private function htmlSpecialChars($input) {
        return ($this->escapeStrings && is_string($input)) ? \htmlspecialchars($input) : $input;
    }
    
    /*******************************************************************************
     * Parameters (e.g., POST and GET data)
     ******************************************************************************/

    /**
     * Vrací parametr získaný z z parsovaného request body nebo query (v tomto pořadí). Pokud parametr neexistuje múže vracet default hodnotu.
     * Parametry z request body kóduje funkcí htmlspecialchars().
     * 
     * @param ServerRequestInterface $request Objekt request
     * @param  string $key Klíč (index, jméno) parametru.
     * @param type $default Návratová hodnota, kterou metoda vrací, pokud parametr se zadaným klíčem v body neexistuje. Pokud není zadána je default hodnota NULL.
     *
     * @return mixed Hodnota paremetru.
     */
    public function getParam(ServerRequestInterface $request, $key, $default=NULL) {
        $postParams = $request->getParsedBody();
        $getParams = $request->getQueryParams();
        if (is_array($postParams) && isset($postParams[$key])) {
            $result = $this->htmlSpecialChars($postParams[$key]);
        } elseif (is_object($postParams) && property_exists($postParams, $key)) {
            $result = $this->htmlSpecialChars($postParams->$key);
        } elseif (isset($getParams[$key])) {
            $result = $getParams[$key];
        } else {
            $result = $default;
        }
        return $result ?? null;
    }

    /**
     * Vrací jeden parametr získaný z parsovaného request body. Pokud parametr neexistuje múže vracet default hodnotu.
     * Parametry z request body kóduje funkcí htmlspecialchars().
     *
     * @param ServerRequestInterface $request
     * @param  string $key Klíč (index, jméno) parametru.
     * @param type $default Návratová hodnota, kterou metoda vrací, pokud parametr se zadaným klíčem v body neexistuje.
     *
     * @return mixed Hodnota paremetru.
     */
    public function getParsedBodyParam(ServerRequestInterface $request, $key, $default=NULL) {
        $postParams = $request->getParsedBody();
        if (is_array($postParams) && isset($postParams[$key])) {
            $input = $postParams[$key];
        } elseif (is_object($postParams) && property_exists($postParams, $key)) {
            $input = $postParams->$key;
        } else {
            $input = $default;
        }
        $result = $this->htmlSpecialChars($input);
        return $result ?? null;
    }

    /**
     * Vrací jeden parametr získaný z query. Pokud parametr neexistuje múže vracet default hodnotu.
     *
     * @param ServerRequestInterface $request
     * @param  string $key Klíč (index, jméno) parametru.
     * @param type $default Návratová hodnota, kterou metoda vrací, pokud parametr se zadaným klíčem v query neexistuje.
     *
     * @return mixed Hodnota paremetru.
     */
    public function getQueryParam(ServerRequestInterface $request, $key, $default=NULL) {
        $getParams = $request->getQueryParams();
        if (isset($getParams[$key])) {
            $result = $getParams[$key];
        } else {
            $result = $default;
        }

        return $result;
    }

    /**
     * Vrací všechny parametry requestu získané z parsovaného body nebo query (v tomto pořadí). Parametry vrací jako asociativní pole.
     * Při shodných indexech parametru v POST i GET vítězí parametr z body (t.j. POST).
     * Parametry z request body kóduje funkcí htmlspecialchars().
     *
     * Umí zpracovat výsledek parsování body jen pokud je to array. Pokud body parser, který je automaticky vybrán podle obsahu requestu
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    public function getParams(ServerRequestInterface $request) {
        $params = $request->getQueryParams();
        $postParams = $request->getParsedBody();
        foreach ($postParams as $key => $value) {
            $postResult[$key] = $this->htmlSpecialChars($value);
        }
        if ($postResult) {
            $params = array_merge($params, (array)$postResult);  // při shodných indexech vítězí druhý parametr, t.j. post
        }

        return $result;
    }

}
