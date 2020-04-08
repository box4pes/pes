<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\AAttributes;

/**
 * Description of A
 *
 * @author pes2704
 */
class A extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'a';
        $this->attributes = new AAttributes($attributes);
    }

    /**
     *
     * @return AAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }
}
