<?php
use PHPUnit\Framework\TestCase;

use Pes\Router\Resource\ResourceRegistry;
use Pes\Router\Resource\Resource;

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

        $this->registry->register('pref1', $resource->withHttpMethod('POST')->withUrlPattern('/trdlo/:id/ruka/:lp/'));
        $this->registry->register('pref1', $resource->withHttpMethod('POST')->withUrlPattern('/trdlo/:id/noha/:lp/'));
        $this->registry->register('pref2', $resource->withHttpMethod('GET')->withUrlPattern('/trdlo/:id/ruka/:lp/'));
        $this->registry->register('pref2', $resource->withHttpMethod('POST')->withUrlPattern('/trdlo/:id/noha/:lp/'));
    }

    public function testHasPrefix() {
        $this->assertTrue($this->registry->hasPrefix('pref1'));
        $this->assertFalse($this->registry->hasPrefix('non'));
    }

    public function testHasHttpMethod() {
        $this->assertTrue($this->registry->hasHttpMethod('pref1', 'POST'));
        $this->assertFalse($this->registry->hasHttpMethod('pref1', 'GET'));
    }

    public function testHasUrlPattern() {
        $this->assertTrue($this->registry->hasUrlPattern('pref1', 'POST', '/trdlo/:id/ruka/:lp/'));
        $this->assertFalse($this->registry->hasUrlPattern('pref1', 'POST', '/qqqtrdlo/:id/ruka/:lp/'));
    }
}
