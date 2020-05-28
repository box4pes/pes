<?php
use PHPUnit\Framework\TestCase;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\Resource\ResourceRegistry;

use Pes\Router\Resource\Resource;
use Pes\Router\MethodEnum;
use Pes\Router\UrlPatternValidator;
use Pes\Router\RouteInterface;
use Pes\Router\Resource\ResourceInterface;

use Pes\Router\Exception\RoutedSegmentPrefixNotFoundException;
use Pes\Router\Exception\RoutedSegmentResourceNotFoundException;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RouteSegmentGeneratorTest
 *
 * @author pes2704
 */
class RouteSegmentGeneratorTest extends TestCase {

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

    public function testAddRouteForActionAndIterator() {
        $generator = new RouteSegmentGenerator($this->registry);
        $this->assertCount(0, $generator->getIterator());
        $generator->addRouteForAction('pref1', 'POST', '/trdlo/:id/ruka/:lp/', function() {return '/trdlo/:id/ruka/:lp/';});
        $generator->addRouteForAction('pref1', 'POST', '/trdlo/:id/ruka/:lp/', function() {return '/trdlo/:id/noha/:lp/';});
        $this->assertCount(2, $generator->getIterator());
        foreach ($generator as $route) {
            $this->assertInstanceOf(RouteInterface::class, $route);
            /** @var RouteInterface $route */
            $this->assertInstanceOf(ResourceInterface::class, $route->getResource());
            $this->assertIsCallable($route->getAction());
        }
    }

    public function testRoutedSegmentPrefixNotFoundExceptionWrongPrefix() {
        $generator = new RouteSegmentGenerator($this->registry);
        $this->expectException(RoutedSegmentPrefixNotFoundException::class);
        $generator->addRouteForAction('non', 'POST', '/trdlo/:id/ruka/:lp/', function() {return '/trdlo/:id/ruka/:lp/';});
    }

    public function testRoutedSegmentResourceNotFoundExceptionWrongMethod() {
        $generator = new RouteSegmentGenerator($this->registry);
        $this->expectException(RoutedSegmentResourceNotFoundException::class);
        $this->expectExceptionMessage("requested HTTP method");
        $generator->addRouteForAction('pref1', 'PUK', '/trdlo/:id/ruka/:lp/', function() {return '/trdlo/:id/ruka/:lp/';});
    }

    public function testRoutedSegmentResourceNotFoundExceptionWrongPattern() {
        $generator = new RouteSegmentGenerator($this->registry);
        $this->expectException(RoutedSegmentResourceNotFoundException::class);
        $this->expectExceptionMessage("requested url pattern");
        $generator->addRouteForAction('pref1', 'POST', '/qqqtrdlo/:id/ruka/:lp/', function() {return '/trdlo/:id/ruka/:lp/';});
    }
}
