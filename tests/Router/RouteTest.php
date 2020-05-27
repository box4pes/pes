<?php
use PHPUnit\Framework\TestCase;

use Pes\Router\Route;
use Pes\Router\RouteInterface;
use Pes\Router\MethodEnum;
use Pes\Router\UrlPatternValidator;
use Pes\Router\Resource\Resource;
use Pes\Router\Resource\ResourceInterface;

/**
 * Test Pes\Type\DbTypeEnum
 *
 * @author pes2704
 */
class RouteTest extends TestCase {

    /**
     *
     */
    public function testConstructor() {
        $route = new Route();
        $this->assertTrue($route instanceof RouteInterface);
        $this->assertTrue($route instanceof Route);
    }

    /**
     *
     */
    public function testSetGetResource() {
        $route = new Route();
        $resource = new Resource(new MethodEnum(), new UrlPatternValidator());
        $route->setResource($resource->withHttpMethod(MethodEnum::GET)->withUrlPattern('/'));
        $this->assertInstanceOf(ResourceInterface::class, $route->getResource());
    }



    public function testSetGetAction() {
        $route = new Route();
        $route->setAction(function() {return "Action!";});
        $action = $route->getAction();
        $this->assertEquals("Action!", $action());
    }

}
