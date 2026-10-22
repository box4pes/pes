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
    /**
     * @param string $httpMethod    
     * @return ResourceInterface
     */
    public function withHttpMethod(string $httpMethod): ResourceInterface;

    /**
     * @param string $urlPattern
     * @return ResourceInterface
     */
    public function withUrlPattern(string $urlPattern): ResourceInterface;

    /**
     * @return string
     */
    public function getHttpMethod(): string;

    /**
     * @return string
     */
    public function getUrlPattern(): string;

    /**
     * @param array $params
     * @return string Path se zadanými parametry.
     * @throws ResourcePathParameterDoesNotMatch
     */
    public function getPathFor(array $params): string;
}
