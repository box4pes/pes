<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\BaseAttributes;

/**
 * Description of Base
 *
 * @author pes2704
 */
class Base extends MetadataContent {
    
    public function __construct(array $attributes=[]) {
        $this->name = 'base';
        $this->attributes = new BaseAttributes($attributes);
    }

    /**
     * 
     * @return BaseAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
