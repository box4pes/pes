<?php

namespace Pes\Router\Resource;

use Pes\Router\Exception\RouteRegistrySegmentPrefixNotFoundException;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Registry
 *
 * @author pes2704
 */
class ResourceRegistry implements ResourceRegistryInterface {

    /**
     * @var Action array of
     */
    private $resources=[];

    /**
     *
     * @param type $prefix
     * @param \Pes\Router\Resource\ResourceInterface $resource
     * @return void
     */
    public function register($prefix, ResourceInterface $resource): void {
        $httpMethod = $resource->getHttpMethod();
        $urlPattern = $resource->getUrlPattern();
        $this->resources[$prefix][$httpMethod][$urlPattern] = $resource;
    }

    public function hasPrefix($prefix): bool {
        return array_key_exists($prefix, $this->resources);
    }

    public function hasHttpMethod($prefix, $httpMethod): bool {
        return $this->hasPrefix($prefix) AND array_key_exists($httpMethod, $this->resources[$prefix]);
    }

    public function hasUrlPattern($prefix, $httpMethod, $urlPattern): bool {
        return $this->hasPrefix($prefix) AND $this->hasHttpMethod($prefix, $httpMethod) AND array_key_exists($urlPattern, $this->resources[$prefix][$httpMethod]);
    }

    /**
     *
     * @param string $prefix
     * @param string $httpMethod
     * @param string $urlPattern
     * @return \Pes\Router\Resource\ResourceInterface|null
     */
    public function getResource($prefix, $httpMethod, $urlPattern): ?ResourceInterface {
        return isset($this->resources[$prefix][$httpMethod][$urlPattern]) ? $this->resources[$prefix][$httpMethod][$urlPattern] : null;
    }
}
