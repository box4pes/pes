<?php
use PHPUnit\Framework\TestCase;

use Pes\Router\Resource\Resource;

use Pes\Router\MethodEnum;
use Pes\Router\UrlPatternValidator;

use Pes\Router\Resource\Exception\ResourceHttpMethodNotValid;
use Pes\Router\Resource\Exception\ResourcePathParameterDoesNotMatch;

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
class ResourceTest extends TestCase {

    private $resource;

    protected function setUp(): void {
        $this->resource = new Resource(new MethodEnum(), new UrlPatternValidator());
    }

    public function testWithMethod() {
        foreach ((new MethodEnum())->getConstList() as $enumMethod) {
            $resource = $this->resource->withHttpMethod($enumMethod);
            $this->assertEquals($resource->getHttpMethod(), $enumMethod);
        }
    }

    public function testWithMethodException() {
        $this->expectException(ResourceHttpMethodNotValid::class);
        $this->expectExceptionMessage("Passed HTTP method KVAK is not valid.");
        $resource = $this->resource->withHttpMethod('KVAK');
    }

    public function withUrlPattern() {
        $this->markTestIncomplete(
          'Tady není doděláno.'
        );
    }
    /**
     *
     */
    public function testSetUrlPattern() {
        $route = new Route(new UrlPatternValidator());

        $route->setUrlPattern('/');
        $route->setUrlPattern('/kuk/');
        $route->setUrlPattern('/kuk/:id/');
        $this->assertTrue(true);   // vždy splněno - testuji jen, že nenastala výjimka
    }

    public function testExceptionEmptyPattern() {
        $route = new Route(new UrlPatternValidator());
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Chybný formát pattern.');  // testuje, message obsahuje řetězec
        $route->setUrlPattern('');
    }

    public function testExceptionMissingLeftSlash() {
        $route = new Route(new UrlPatternValidator());
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Chybný formát pattern.');  // testuje, message obsahuje řetězec
        $route->setUrlPattern('kuk/');
    }

//    public function testExceptionMissingRightSlash() {
//        $route = new Route(new UrlPatternValidator());
//        $this->expectException(\UnexpectedValueException::class);
//        $this->expectExceptionMessage('Chybný formát pattern.');  // testuje, message obsahuje řetězec
//        $route->setUrlPattern('/kuk');
//    }

    public function testExceptionParemeterInFirstSection() {
        $route = new Route(new UrlPatternValidator());
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Chybný formát pattern.');  // testuje, message obsahuje řetězec
        $route->setUrlPattern('/:id/');
    }

    public function testSetGetUrlPattern() {
        $route = new Route(new UrlPatternValidator());
        $route->setUrlPattern('/trdlo/');
        $this->assertEquals('/trdlo/', $route->getUrlPattern());
    }

    public function testGetPatternPreg() {
        $route = new Route(new UrlPatternValidator());
        $route->setUrlPattern('/');
        $patternPreg = $route->getPatternPreg();
        $this->assertEquals("@^/$@D", $route->getPatternPreg());
        $route->setUrlPattern('/trdlo/');
        $patternPreg = $route->getPatternPreg();
        $this->assertEquals("@^/trdlo/$@D", $route->getPatternPreg());
        $route->setUrlPattern('/trdlo/:id/');
        $patternPreg = $route->getPatternPreg();
        $this->assertEquals("@^/trdlo/([a-zA-Z0-9\-\_]+)/$@D", $route->getPatternPreg());
    }
    
    public function withUrlPatternException() {
        $this->markTestIncomplete(
          'Tady není doděláno.'
        );
    }

    public function testGetPathFor() {
        $resource = $this->resource->withHttpMethod('GET')->withUrlPattern('/trdlo/:id/ruka/:lp/');

        $path = $resource->getPathFor(['lp'=>'levá', 'id'=>88]);
        $this->assertEquals("/trdlo/88/ruka/lev%C3%A1/", $path);
        $decodedPath = rawurldecode($path);
        $this->assertEquals("/trdlo/88/ruka/levá/", $decodedPath);  // enkóduje rezervované znaky v path
        $path = $resource->getPathFor(['lp'=>'lev%C3%A1', 'id'=>88]);
        $this->assertEquals("/trdlo/88/ruka/lev%C3%A1/", $path);  // neenkóduje již enkódované rezervované znaky v path - po dekódování by vznikl nesmysl
        $decodedPath = rawurldecode($path);
        $this->assertEquals("/trdlo/88/ruka/levá/", $decodedPath);
    }

    public function testGetPathForException() {
        $resource = $this->resource->withHttpMethod('GET')->withUrlPattern('/trdlo/:id/ruka/:lp/');

        $this->expectException(ResourcePathParameterDoesNotMatch::class);
        $this->expectExceptionMessage("Parameter: 'iii'. Replaced pattern: '/trdlo/:id/ruka/levá/'.");
        $path = $resource->getPathFor(['lp'=>'levá', 'iii'=>88]);

    }
}
