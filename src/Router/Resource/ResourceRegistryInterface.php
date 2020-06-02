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
    public function register(ResourceInterface $resource): void;
    public function hasHttpMethod($httpMethod): bool;
    public function hasUrlPattern($httpMethod, $urlPattern): bool;

    /**
     *
     * @param type $httpMethod
     * @param type $urlPattern
     * @return \Pes\Router\Resource\ResourceInterface|null
     */
    public function getResource($httpMethod, $urlPattern): ?ResourceInterface;

}
