<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\StyleAttributes;

/**
 * Description of Style
 *
 * @author pes2704
 */
class Style extends MetadataContent {

    public function __construct(array $attributes=[]) {
        $this->name = 'style';
        $this->attributes = new StyleAttributes($attributes);
    }
    
    /**
     * 
     * @return StyleAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }        
}
