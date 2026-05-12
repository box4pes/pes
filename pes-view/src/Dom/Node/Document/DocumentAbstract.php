<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Dom\Node\Document;

use Pes\View\Dom\Node\NodeAbstract;
use Pes\View\Dom\Node\NodeInterface;
use Pes\View\Dom\Node\Attributes\GlobalAttributes;

/**
 * Description of DocmentAbstract
 *
 * @author pes2704
 */
abstract class DocumentAbstract extends NodeAbstract implements NodeInterface {
    
    protected function __construct() {
        $this->name = '#document';        
        $this->attributes = new GlobalAttributes();
    }
    
    /**
     * 
     * @return AttributesInterface
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
