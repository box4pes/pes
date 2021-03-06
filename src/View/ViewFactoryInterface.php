<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\View;

use Pes\Dom\Node\NodeInterface;
use Psr\Container\ContainerInterface;

/**
 *
 * @author pes2704
 */
interface ViewFactoryInterface {
    public function setRendererContainer(ContainerInterface $rendererContainer): viewFactoryInterface;
    public function phpTemplateView($templateFilename, $data=null): View;
    public function phpTemplateCompositeView($templateFilename, $data=null): View;
    public function nodeTemplateView(NodeInterface $node): View;
}
