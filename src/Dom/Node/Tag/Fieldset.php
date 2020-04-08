<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\FieldsetAttributes;

/**
 * Description of Fieldset
 *
 * @author pes2704
 */
class Fieldset extends TagAbstract {    

    public function __construct(array $attributes=[]) {
        $this->name = 'fieldset';
        $this->attributes = new FieldsetAttributes($attributes);
    }
    
    /**
     * 
     * @return FieldsetAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }
}

