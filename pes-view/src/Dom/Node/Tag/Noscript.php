<?php

namespace Pes\View\Dom\Node\Tag;

use Pes\View\Dom\Node\Attributes\NoscriptAttributes;

/**
 * Description of Link
 *
 * @author pes2704
 */
class Noscript extends MetadataContent {

    public function __construct(array $attributes=[]) {
        $this->name = 'noscript';
        $this->attributes = new NoscriptAttributes($attributes);
    }
    
    /**
     * 
     * @return NoscriptAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
