<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Router\Resource;

/**
 *
 * @author pes2704
 */
interface ResourceInterface {
    
    public function withHttpMethod($httpMethod): ResourceInterface;

    public function withUrlPattern($urlPattern): ResourceInterface;

    public function getHttpMethod();

    public function getUrlPattern();

    /**
     * @return string Path se zadanými parametry.
     */
    public function getPathFor(array $params);
}
