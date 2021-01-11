<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Template;

use Pes\View\Renderer\NodeRenderer as DefaultRenderer;

use Pes\View\NodeFactory\NodeFactoryInterface;
use Pes\Dom\Node\NodeInterface;

/**
 * Description of NodeTemplate
 *
 * @author pes2704
 */
class NodeTemplate implements NodeTemplateInterface {

    protected $nodeFactory;

    public function __construct(NodeFactoryInterface $nodeFactory) {
        $this->nodeFactory = $nodeFactory;
    }

    /**
     *
     * @param mixed $data template může data použít pro vytvoření Node.
     * @return NodeInterface
     */
    public function getNode($classMap=null): NodeInterface {
        return $this->nodeFactory->create();
    }

    /**
     * Vrací jméno třídy NodeRenderer.
     * @return string
     */
    public function getDefaultRendererService() {
        return DefaultRenderer::class;
    }

}
