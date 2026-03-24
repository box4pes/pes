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
    public function view(?iterable $data=null);
    public function compositeView(?iterable $data=null);
    
    /**
     * Vytvoří View s nastaveným rendererem ImplodeRenderer a nstavenými daty, pokud byla zadána.
     * 
     * @param iterable $data
     */
    public function implodeView(?iterable $data=null);
    public function phpTemplateView($templateFilename, ?iterable $data=null): View;
    public function phpTemplateCompositeView($templateFilename, ?iterable $data=null): View;
    public function nodeTemplateView(NodeInterface $node): View;
}
