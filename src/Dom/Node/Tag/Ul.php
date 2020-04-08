<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\GlobalAttributes;

/**
 * Description of Ul
 *
 * @author pes2704
 */
class Ul extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'ul';
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
