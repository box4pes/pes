<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\LabelAttributes;

/**
 * Description of Chyby
 *
 * @author pes2704
 */
class Label extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name ='label';
        $this->attributes = new LabelAttributes($attributes);
    }
    
    /**
     * 
     * @return LabelAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}

