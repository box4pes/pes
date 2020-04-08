<?php
use PHPUnit\Framework\TestCase;

use Pes\Router\Route;
use Pes\Router\RouteInterface;
use Pes\Router\MethodEnum;
use Pes\Router\UrlPatternValidator;

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
        $route = new Route(new UrlPatternValidator());
        $this->assertTrue($route instanceof RouteInterface);
        $this->assertTrue($route instanceof Route);
    }

    /**
     *
     */
    public function testSetMethodGetMethod() {
        $route = new Route(new UrlPatternValidator());
        $route->setMethod(MethodEnum::GET);
        $this->assertEquals(MethodEnum::GET, $route->getMethod());
        $route->setMethod(MethodEnum::POST);
        $this->assertEquals(MethodEnum::POST, $route->getMethod());
        $route->setMethod(MethodEnum::PUT);
        $this->assertEquals(MethodEnum::PUT, $route->getMethod());
        $route->setMethod(MethodEnum::DELETE);
        $this->assertEquals(MethodEnum::DELETE, $route->getMethod());
        $route->setMethod(MethodEnum::OPTIONS);
        $this->assertEquals(MethodEnum::OPTIONS, $route->getMethod());
        $route->setMethod(MethodEnum::PATCH);
        $this->assertEquals(MethodEnum::PATCH, $route->getMethod());

        try {
            $route->setMethod('PIST');
        } catch (Pes\Type\Exception\ValueNotInEnumException $vnieException) {
            $this->assertStringStartsWith('Value is not in enum', $vnieException->getMessage());
        }
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

    public function testSetGetAction() {
        $route = new Route(new UrlPatternValidator());
        $action = function() {
            return 'Test action!';
        };
        $route->setAction($action);
        $this->assertEquals($action, $route->getAction());
    }

}
