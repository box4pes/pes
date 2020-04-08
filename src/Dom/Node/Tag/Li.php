<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\LiAttributes;

/**
 * Description of Li
 *
 * @author pes2704
 */
class Li extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'li';
        $this->attributes = new LiAttributes($attributes);
    } 
    
    /**
     * 
     * @return LiAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
