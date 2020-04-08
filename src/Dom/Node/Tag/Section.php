<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\GlobalAttributes;

/**
 * Description of Footer
 *
 * @author pes2704
 */
class Section extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'section';
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
