<?php

namespace Pes\View\Dom\Node\Tag;

use Pes\View\Dom\Node\Attributes\TitleAttributes;

/**
 * Description of Title
 *
 * @author pes2704
 */
class Title extends MetadataContent {

    public function __construct(array $attributes=[]) {
        $this->name = 'title';
        $this->attributes = new TitleAttributes($attributes);
    }
    
    /**
     * 
     * @return TitleAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }        
}
