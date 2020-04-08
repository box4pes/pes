<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\InputAttributes;

/**
 * Description of Input
 * 
 * @author pes2704
 */
class Input extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'input';
        $this->attributes = new InputAttributes($attributes);
    }
    
    /**
     * 
     * @return InputAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
