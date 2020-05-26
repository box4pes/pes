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
interface RegistryInterface {
    public function register($prefix, ActionInterface $action): void;
    public function getAction($prefix, $httpMethod, $urlPattern): ActionInterface;
    public function getRoutedSegment($prefix, $httpMethod): \Traversable;
}
