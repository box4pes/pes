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

    public function testRegister() {

        $segment = $this->registry->getRoutedSegment('pref1', 'POST');
        $this->assertIsIterable($segment);
        $this->assertCount(2, $segment);

    }

    public function testGetRoutedSegment() {
        $segment = $this->registry->getRoutedSegment('pref2', 'GET');
        $this->assertIsIterable($segment);
        $this->assertCount(1, $segment);

        $segment = $this->registry->getRoutedSegment('pref2', 'POST');
        $this->assertIsIterable($segment);
        $this->assertCount(1, $segment);
    }

    public function testGetRoutedSegmentWithInvalidPrefix() {
        $this->expectException(PHPUnit\Framework\Error\Notice::class);
        $segment = $this->registry->getRoutedSegment('non', 'POST');
        $this->assertIsIterable($segment);
        $this->assertCount(0,  $segment);
    }

    public function testGetRoutedSegmentWithInvalidMethod() {
        $this->expectException(PHPUnit\Framework\Error\Notice::class);
        $segment = $this->registry->getRoutedSegment('pref2', 'PUSH');
        $this->assertIsIterable($segment);
        $this->assertCount(0, $segment);
    }


}
