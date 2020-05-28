<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Router;

use Pes\Router\Resource\ResourceRegistryInterface;

use Pes\Router\Exception\RoutedSegmentPrefixNotFoundException;
use Pes\Router\Exception\RoutedSegmentResourceNotFoundException;

/**
 * Description of RouteSegmentGenerator
 *
 * @author pes2704
 */
class RouteSegmentGenerator implements RouteSegmentGeneratorInterface, \IteratorAggregate {

    private $resourceRegistry;
    /**
     *
     * @var RouteInterface
     */
    private $routes = [];

    public function __construct(ResourceRegistryInterface $resourceRegistry) {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * 
     * @param type $prefix
     * @param type $httpMethod
     * @param type $urlPattern
     * @param callable $action
     * @return void
     * @throws RoutedSegmentPrefixNotFoundException
     * @throws RoutedSegmentResourceNotFoundException
     */
    public function addRouteForAction($prefix, $httpMethod, $urlPattern, callable $action): void {
        if (!$this->resourceRegistry->hasPrefix($prefix)) {
            throw new RoutedSegmentPrefixNotFoundException("No resources with requested prefix: '$prefix'.");
        } elseif (!$this->resourceRegistry->hasHttpMethod($prefix, $httpMethod)) {
            throw new RoutedSegmentResourceNotFoundException("No resource with requested HTTP method: '$httpMethod'.");
        } elseif (!$this->resourceRegistry->hasUrlPattern($prefix, $httpMethod, $urlPattern)) {
            throw new RoutedSegmentResourceNotFoundException("No resource with requested url pattern: '$urlPattern'.");
        } else {
            $this->routes[] = (new Route())
                    ->setResource($this->resourceRegistry->getResource($prefix, $httpMethod, $urlPattern))
                    ->setAction($action);
        }
    }

    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->routes);
    }
}
