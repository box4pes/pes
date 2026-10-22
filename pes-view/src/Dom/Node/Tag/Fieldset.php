<?php

namespace Pes\View\Dom\Node\Tag;

use Pes\View\Dom\Node\Attributes\FieldsetAttributes;

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

