<?php

namespace Pes\Router\Resource;

use Pes\Router\Resource\Exception\ResourceHttpMethodNotValid;
use Pes\Router\Resource\Exception\ResourceUrlPatternNotValid;
use Pes\Router\Resource\Exception\ResourceUrlPatternDuplicate;
use Pes\Router\Resource\Exception\ResourcePatternAndPrefixMismatch;

use Pes\Router\MethodEnum;
use Pes\Type\Exception\TypeExceptionInterface;
use Pes\Router\UrlPatternValidator;
use Pes\Router\Exception\WrongPatternFormatException;

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


    const SEPARATOR = '->';

    /**
     *
     * @param type $prefix
     * @param \Pes\Router\Resource\ResourceInterface $resource
     * @return void
     * @throws ResourceHttpMethodNotValid
     * @throws ResourceUrlPatternNotValid
     * @throws ResourceUrlPatternDuplicate
     */
    public function register($prefix, ResourceInterface $resource): void {

        $httpMethod = $resource->getHttpMethod();

        $urlPattern = $resource->getUrlPattern();

        if (array_key_exists($prefix, $this->resources) AND array_key_exists($urlPattern, $this->resources[$prefix])) {
            throw new ResourceUrlPatternDuplicate("Duplicitní url pattern '$urlPattern'.");
        } else {
            $this->resources[$prefix][$httpMethod][] = $resource;
        }
    }
    public function bindAction($prefix, $httpMethod, callable $action): void {
        if (!array_key_exists($prefix, $this->resources)) {

        } elseif (!array_key_exists($httpMethod, $this->resources[$prefix])){

        }


                ;
    }
    public function getRoutedSegment($prefix, $httpMethod): \Traversable {
        if (!array_key_exists($prefix, $this->resources)) {
            user_error("No resources with requested prefix: '$prefix'.", E_USER_NOTICE);
            return new \ArrayIterator([]);
        } elseif (!array_key_exists($httpMethod, $this->resources[$prefix])) {
            user_error("No resources with requested HTTP method: '$prefix'.", E_USER_NOTICE);
            return new \ArrayIterator([]);
        } else {
            return new \ArrayIterator($this->resources[$prefix][$httpMethod]);
        }
    }

}
