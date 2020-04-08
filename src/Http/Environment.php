<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Http;

use Psr\Http\Message\StreamInterface;

use Pes\Collection\MapCollection;

/**
 * Description of Environment
 *
 * @author pes2704
 */
class Environment extends MapCollection {

    const INPUT_STREAM = 'INPUT_STREAM';

    /**
     * Obvyklé použití je pro kolekci proměnných jako jsou hlavičky, cesty a umístění skriptů.
     * Pak je třeba jako parametr konstruktoru zadat superglobální pole $_SERVER.
     *
     * $_SERVER is an array containing information such as headers, paths, and script locations. The entries in this array are created by the web server.
     *
     * @param array $$entriesArray, StreamInterface $inputStream
     */
    public function __construct($entriesArray, $inputStream) {
        if (!is_resource($inputStream)) {
            throw new \UnexpectedValueException("Parametr \$inpustream musí báýt tyou resource.");
        }
        parent::__construct($entriesArray);
        $this->set(self::INPUT_STREAM, $inputStream);
    }
}
