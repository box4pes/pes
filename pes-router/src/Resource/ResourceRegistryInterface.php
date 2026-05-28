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
     * @param type $httpMethod
     * @return bool
     */
    public function hasHttpMethod($httpMethod): bool;
    
    /**
     * 
     * @param type $httpMethod
     * @param type $urlPattern
     * @return bool
     */
    public function hasUrlPattern($httpMethod, $urlPattern): bool;

    /**
     *
     * @param type $httpMethod
     * @param type $urlPattern
     * @return ResourceInterface|null
     */
    public function getResource($httpMethod, $urlPattern): ?ResourceInterface;

}
