<?php
use PHPUnit\Framework\TestCase;

use Pes\Action\Resource;

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
    public function testGetPathFor() {
        $resource = new Resource('GET', '/trdlo/:id/ruka/:lp/');

        $path = $resource->getPathFor(['lp'=>'levá', 'id'=>88]);
        $this->assertEquals("/trdlo/88/ruka/lev%C3%A1/", $path);
        $decodedPath = rawurldecode($path);
        $this->assertEquals("/trdlo/88/ruka/levá/", $decodedPath);  // enkóduje rezervované znaky v path
        $path = $resource->getPathFor(['lp'=>'lev%C3%A1', 'id'=>88]);
        $this->assertEquals("/trdlo/88/ruka/lev%C3%A1/", $path);  // neenkóduje již enkódované rezervované znaky v path - po dekódování by vznikl nesmysl
        $decodedPath = rawurldecode($path);
        $this->assertEquals("/trdlo/88/ruka/levá/", $decodedPath);
    }


}
