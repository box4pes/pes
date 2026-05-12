<?php

namespace Pes\View\Dom\Node\Tag;

use Pes\View\Dom\Node\Attributes\LinkAttributes;

/**
 * Description of Link
 *
 * @author pes2704
 */
class Link extends MetadataContent {

    public function __construct(array $attributes=[]) {
        $this->name = 'link';
        $this->attributes = new LinkAttributes($attributes);
    }
    
    /**
     * 
     * @return LinkAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
