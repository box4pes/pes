<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Http;

use Pes\Core\Collection\MapCollection;
use UnexpectedValueException;

/**
 * Description of Environment
 *
 * @author pes2704
 */
class Environment extends MapCollection implements EnvironmentInterface {

    /**
     * Obvyklé použití je pro kolekci proměnných jako jsou hlavičky, cesty a umístění skriptů a vstupní stream.
     * Pak je třeba jako parametr konstruktoru zadat superglobální pole $_SERVER a vstupní stream 'php://input'.
     *
     * $_SERVER je pole obsahující informace, jako jsou hlavičky, cesty a umístění skriptů. Položky v tomto poli vytváří webový server.
     * 
     * @param array $entriesArray
     * @param mixed $inputStream
     * @throws UnexpectedValueException
     */
    public function __construct(array $entriesArray, $inputStream) {
        if (!is_resource($inputStream)) {
            throw new UnexpectedValueException("Parametr \$inpustream musí být typu resource.");
        }
        parent::__construct($entriesArray);
        $this->set(EnvironmentInterface::INPUT_STREAM, $inputStream);
    }
}
