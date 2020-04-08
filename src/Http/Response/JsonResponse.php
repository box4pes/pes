<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Description of JsonResponse
 *
 * @author pes2704
 */
class JsonResponse {
    /**
     * Přidá do response Json data.
     *
     * Metoda přídá do body zadaného response zadaná data zakódovaná jako json a hlavičku 'Content-Type': 'application/json;charset=utf-8'.
     *
     * Pro kódování json používá php funkci json_encode(), nepovinný parametr $encodingOptions je předán funkci json_encode() - použití viz dokumntace.
     *
     * @param ResponseInterface $response
     * @param mixed $data   Data - budou zakódována do Json
     * @param int $status HTTP status code.
     * @param int $encodingOptions Json encoding options
     * @return ResponseInterface
     * @throws \RuntimeException
     */
    public function withJson(ResponseInterface $response, $data, $status = null, $encodingOptions = 0) {
        $body = $response->getBody();
        $body->rewind();
        $body->write($json = json_encode($data, $encodingOptions));

        // Ensure that the json encoding passed successfully
        if ($json === false) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        if (isset($status)) {
            $responseWithJson = $response->withHeader('Content-Type', 'application/json;charset=utf-8')->withStatus($status);
        } else {
            $responseWithJson = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
        }
        return $responseWithJson;
    }

}
