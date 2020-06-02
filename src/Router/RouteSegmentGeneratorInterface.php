<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Router;

/**
 *
 * @author pes2704
 */
interface RouteSegmentGeneratorInterface {
    public function addRouteForAction($httpMethod, $urlPattern, callable $action): void;
}
