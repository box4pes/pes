<?php
use PHPUnit\Framework\TestCase;

use Pes\Router\Resource\ResourceRegistry;
use Pes\Router\Resource\Resource;
use Pes\Router\Resource\ResourceInterface;

use Pes\Router\Resource\Exception\ResourceHttpMethodNotValid;
use Pes\Router\Resource\Exception\ResourceUrlPatternNotValid;
use Pes\Router\Resource\Exception\ResourceUrlPatternDuplicate;
use Pes\Router\Resource\Exception\ResourcePatternAndPrefixMismatch;

use Pes\Router\UrlPatternValidator;
use Pes\Router\MethodEnum;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceTest
 *
 * @author pes2704
 */
class ResourceRegistryTest extends TestCase {

    /**
     * @var ResourceRegistry
     */
    private $registry;

    protected function setUp(): void {
        $this->registry = new ResourceRegistry();
        $resource = new Resource(new MethodEnum(), new UrlPatternValidator());

        $this->registry->register($resource->withHttpMethod('POST')->withUrlPattern('/trdlo/:id/ruka/:lp/'));
        $this->registry->register($resource->withHttpMethod('POST')->withUrlPattern('/trdlo/:id/noha/:lp/'));
        $this->registry->register($resource->withHttpMethod('GET')->withUrlPattern('/trdlo/:id/ruka/:lp/'));
        $this->registry->register($resource->withHttpMethod('POST')->withUrlPattern('/trdlo/:id/noha/:lp/'));
    }

    public function testHasHttpMethod() {
        $this->assertTrue($this->registry->hasHttpMethod('POST'));
        $this->assertTrue($this->registry->hasHttpMethod('GET'));
        $this->assertFalse($this->registry->hasHttpMethod('PUSH'));
    }

    public function testHasUrlPattern() {
        $this->assertTrue($this->registry->hasUrlPattern('POST', '/trdlo/:id/ruka/:lp/'));
        $this->assertFalse($this->registry->hasUrlPattern('POST', '/qqqtrdlo/:id/ruka/:lp/'));
    }

    public function testGetResource() {
        $this->assertInstanceOf(ResourceInterface::class, $this->registry->getResource('POST', '/trdlo/:id/ruka/:lp/'));
        $this->assertInstanceOf(ResourceInterface::class, $this->registry->getResource('POST', '/trdlo/:id/noha/:lp/'));
        $this->assertInstanceOf(ResourceInterface::class, $this->registry->getResource('GET', '/trdlo/:id/ruka/:lp/'));
        $this->assertInstanceOf(ResourceInterface::class, $this->registry->getResource('POST', '/trdlo/:id/noha/:lp/'));

        $this->assertInstanceOf(ResourceInterface::class, $this->registry->getResource('POST', '/trdlo/:qq/ruka/:ee/'));
        $this->assertInstanceOf(ResourceInterface::class, $this->registry->getResource('POST', '/trdlo/:rr/noha/:tt/'));
        $this->assertInstanceOf(ResourceInterface::class, $this->registry->getResource('GET', '/trdlo/:qq/ruka/:ggggggg/'));
        $this->assertInstanceOf(ResourceInterface::class, $this->registry->getResource('POST', '/trdlo/:hkjhkjh/noha/:l/'));

        $this->assertNull($this->registry->getResource('PUSH', '/trdlo/:id/noha/:lp/'));
        $this->assertNull($this->registry->getResource('GET', '/trdlooo/:id/ruka/:lp/'));
    }
}
