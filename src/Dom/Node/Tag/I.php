<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\IAttributes;

/**
 * Description of P
 *
 * @author pes2704
 */
class I extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'i';
        $this->attributes = new IAttributes($attributes);
    }

    /**
     *
     * @return IAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }
}
