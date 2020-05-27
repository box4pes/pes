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
    public function bindAction($prefix, $httpMethod, callable $action): void;
    public function getRoutedSegment($prefix, $httpMethod): \Traversable;
}
