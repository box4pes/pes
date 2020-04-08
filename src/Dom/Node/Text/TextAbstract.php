<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Dom\Node\Text;

use Pes\Dom\Node\NodeAbstract;
use Pes\Dom\Node\NodeInterface;
use Pes\Dom\Node\Attributes\NullAttributes;

/**
 * Description of TextAbstract
 *
 * @author pes2704
 */
abstract class TextAbstract extends NodeAbstract implements TextInterface {
    
    protected function __construct() {
        $this->name = '#text';        
        $this->attributes = new NullAttributes();
    }
    
    abstract public function getText();
    
    /**
     * 
     * @return AttributesInterface
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
    
    public function addChild(NodeInterface $node=NULL) {
        if ($node) {
            throw new \LogicException('Nelze přidat potomka, text node nemůže mít potomky.');
        }
    }
}
