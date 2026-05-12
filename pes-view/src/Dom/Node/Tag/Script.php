<?php

namespace Pes\View\Dom\Node\Tag;

use Pes\View\Dom\Node\Attributes\ScriptAttributes;

/**
 * Description of Link
 *
 * @author pes2704
 */
class Script extends MetadataContent {

    public function __construct(array $attributes=[]) {
        $this->name = 'script';
        $this->attributes = new ScriptAttributes($attributes);
    }
    
    /**
     * @return ScriptAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }       
}
