<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Action;

/**
 *
 * @author pes2704
 */
interface ResourceInterface {

    public function getHttpMethod();

    public function getUrlPattern();

    /**
     * @return string Path se zadanými parametry.
     */
    public function getPathFor(array $params);
}
