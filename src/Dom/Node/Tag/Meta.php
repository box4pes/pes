<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\MetaAttributes;

/**
 * Description of Link
 *
 * @author pes2704
 */
class Meta extends MetadataContent {

    public function __construct(array $attributes=[]) {
        $this->name = 'meta';
        $this->attributes = new MetaAttributes($attributes);
    }
    
    /**
     * 
     * @return MetaAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
