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
interface ResourceRegistryInterface {
    
    /**
     * 
     * @param ResourceInterface $resource
     * @return void
     */
    public function register(ResourceInterface $resource): void;
    
    /**
     * 
     * @param string $httpMethod
     * @return bool
     */
    public function hasHttpMethod(string $httpMethod): bool;
    
    /**
     * 
     * @param string $httpMethod
     * @param string $urlPattern
     * @return bool
     */
    public function hasUrlPattern(string $httpMethod, string $urlPattern): bool;

    /**
     *
     * @param string $httpMethod
     * @param string $urlPattern
     * @return ResourceInterface|null
     */
    public function getResource(string $httpMethod, string $urlPattern): ?ResourceInterface;

}
