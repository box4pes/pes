<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\LegendAttributes;

/**
 * Description of Chyby
 *
 * @author pes2704
 */
class Legend extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'legend';
        $this->attributes = new LegendAttributes($attributes);
    }
    
    /**
     * 
     * @return LegendAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}

