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
     * @param \Pes\Router\Resource\ResourceInterface $resource
     * @return void
     */
    public function register(ResourceInterface $resource): void {
        $httpMethod = $resource->getHttpMethod();
        $urlPattern = $resource->getUrlPattern();
        $this->resources[$httpMethod][$this->canonicalize($urlPattern)] = $resource;
    }

    public function hasHttpMethod($httpMethod): bool {
        return array_key_exists($httpMethod, $this->resources);
    }

    public function hasUrlPattern($httpMethod, $urlPattern): bool {
        return $this->hasHttpMethod($httpMethod) AND array_key_exists($this->canonicalize($urlPattern), $this->resources[$httpMethod]);
    }

    /**
     *
     * @param string $httpMethod
     * @param string $urlPattern
     * @return \Pes\Router\Resource\ResourceInterface|null
     */
    public function getResource($httpMethod, $urlPattern): ?ResourceInterface {
        $canonPattern = $this->canonicalize($urlPattern);
        return isset($this->resources[$httpMethod][$canonPattern]) ? $this->resources[$httpMethod][$canonPattern] : null;
    }

    private function canonicalize($urlPattern) {
        // zamění parametry v pattern za :
        return preg_replace('/\\\:[a-zA-Z0-9\_\-]+/u', ':', preg_quote($urlPattern));

    }
}
