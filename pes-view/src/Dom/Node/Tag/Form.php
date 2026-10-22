<?php

namespace Pes\View\Dom\Node\Tag;

use Pes\View\Dom\Node\Attributes\FormAttributes;

/**
 * Description of Form
 *
 * @author pes2704
 */
class Form extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'form';
        $this->attributes = new FormAttributes($attributes);
    }
    
    /**
     * 
     * @return FormAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}

