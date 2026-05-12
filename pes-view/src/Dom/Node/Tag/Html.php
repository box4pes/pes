<?php

namespace Pes\View\Dom\Node\Tag;

use Pes\View\Dom\Node\Tag\Head;
use Pes\View\Dom\Node\Tag\Body;

use Pes\View\Dom\Node\NodeInterface;
use Pes\View\Dom\Node\Attributes\HtmlAttributes;

/**
 * Description of Html
 *
 * @author pes2704
 */
class Html extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name ='html';
        $this->attributes = new HtmlAttributes($attributes);
    }    
    
    public function addChild(NodeInterface $node) {
        if (!($node instanceof Head OR $node instanceof Body)) {
            trigger_error("Pokus o přidání potomka typu ".get_class($node)." selhal. Tagu Html lze přidávat pouze potomkovské elementy Head a Body.");
        }
        return parent::addChild($node);
    }
    
    /**
     * 
     * @return NullAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
