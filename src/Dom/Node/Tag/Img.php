<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\ImgAttributes;

/**
 * Description of Img
 * 
 * @author pes2704
 */
class Img extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'img';
        $this->attributes = new ImgAttributes($attributes);
    }
    
    /**
     * 
     * @return ImgAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
