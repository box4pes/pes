<?php

namespace Pes\Router\Resource;

use Pes\Router\Resource\ResourceInterface;

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
     * @var ResourceInterface[] array of
     */
    private $resources=[];

    /**
     * 
     * @param ResourceInterface $resource
     * @return void
     */
    #[\Override]
    public function register(ResourceInterface $resource): void {
        $httpMethod = $resource->getHttpMethod();
        $urlPattern = $resource->getUrlPattern();
        $this->resources[$httpMethod][$this->canonicalize($urlPattern)] = $resource; 
    }

    /**
     * 
     * @param string $httpMethod
     * @return bool
     */
    #[\Override]
    public function hasHttpMethod(string $httpMethod): bool {
        return array_key_exists($httpMethod, $this->resources);
    }

    /**
     * 
     * @param string $httpMethod
     * @param string $urlPattern
     * @return bool
     */
    #[\Override]
    public function hasUrlPattern(string $httpMethod, string $urlPattern): bool {
        return $this->hasHttpMethod($httpMethod) AND array_key_exists($this->canonicalize($urlPattern), $this->resources[$httpMethod]);
    }

    /**
     *
     * @param string $httpMethod
     * @param string $urlPattern
     * @return ?ResourceInterface|null; Null pokud není resource nalezena.
     */
    #[\Override]
    public function getResource(string $httpMethod, string $urlPattern): ?ResourceInterface {
        $canonPattern = $this->canonicalize($urlPattern);
        return $this->resources[$httpMethod][$canonPattern] ?? null;
    }

    private function canonicalize(string $urlPattern): string {
        // zamění parametry v pattern za :
        return preg_replace('/\\\:[a-zA-Z0-9\_\-]+/u', ':', preg_quote($urlPattern));

    }
}
