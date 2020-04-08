<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\PAttributes;

/**
 * Description of P
 *
 * @author pes2704
 */
class P extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'p';
        $this->attributes = new PAttributes($attributes);
    }
    
    /**
     * 
     * @return PAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }        
}
