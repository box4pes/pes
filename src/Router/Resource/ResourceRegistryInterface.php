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
    public function register($prefix, ResourceInterface $resource): void;
    public function hasPrefix($prefix): bool;
    public function hasHttpMethod($prefix, $httpMethod): bool;
    public function hasUrlPattern($prefix, $httpMethod, $urlPattern): bool;
    
    /**
     *
     * @param type $prefix
     * @param type $httpMethod
     * @param type $urlPattern
     * @return \Pes\Router\Resource\ResourceInterface|null
     */
    public function getResource($prefix, $httpMethod, $urlPattern): ?ResourceInterface;

}
