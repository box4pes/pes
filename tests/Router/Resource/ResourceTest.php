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
