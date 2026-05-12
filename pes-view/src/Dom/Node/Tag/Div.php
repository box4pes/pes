<?php

namespace Pes\View\Dom\Node\Tag;

use Pes\View\Dom\Node\Attributes\DivAttributes;

/**
 * Description of Div
 *
 * @author pes2704
 */
class Div extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'div';
        $this->attributes = new DivAttributes($attributes);
    }

    /**
     *
     * @return DivAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }
}
