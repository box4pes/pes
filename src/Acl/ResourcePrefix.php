<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Acl;

/**
 * Description of ResourcePrefix
 *
 * @author pes2704
 */
class ResourcePrefix implements ResourcePrefixInterface {

    private $httpMethod;
    private $urlPatternPrefix;

    public function __construct($httpMethod, $urlPatternPrefix) {
        $this->httpMethod = $httpMethod;
        $this->urlPatternPrefix =$urlPatternPrefix;
    }

    public function getHttpMethod() {
        return $this->httpMethod;
    }

    public function getUrlPatternPrefix() {
        return $this->urlPatternPrefix;
    }
}
