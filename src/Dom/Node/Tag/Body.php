<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\GlobalAttributes;

/**
 * Description of Body
 *
 * Dědí Global
 * 
 * @author pes2704
 */
class Body extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'body';
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
