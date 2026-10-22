<?php

namespace Pes\View\Dom\Node\Tag;

use Pes\View\Dom\Node\Attributes\GlobalAttributes;

/**
 * Description of Header
 *
 * @author pes2704
 */
class Header extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'header';
        $this->attributes = new GlobalAttributes($attributes);
    }
    
    /**
     * 
     * @return GlobalAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
