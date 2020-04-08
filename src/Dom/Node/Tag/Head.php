<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\Attributes\GlobalAttributes;
use Pes\Dom\Node\Tag\MetadataContent;
use Pes\Dom\Node\Text\TextAbstract;
use Pes\Dom\Node\NodeInterface;

/**
 * Description of Head
 * 
 * Dědí Global
 *
 * @author pes2704
 */
class Head extends TagAbstract {

    public function __construct(array $attributes=[]) {
        $this->name = 'head';
        $this->attributes = new GlobalAttributes($attributes);
    }    

    /**
     * Přetěžuje netodu addChild() třídy TagAbstract.
     * Jako prametr (potomkovský tag) přijímá pouze tagy typu MetadataContent. MetadataContent je společný společný předek všem tagům s metadata obsahem: 
     * Title, Style, Base, Link, Meta, Script, Noscript. Metadata tagy jsou tagy přípustné jako potomci tagu Head.
     * 
     * @param MetadataContent $node
     * @return $this
     */
    public function addChild(NodeInterface $node) {
        if (!($node instanceof MetadataContent OR $node instanceof TextAbstract)) {
            throw new \UnexpectedValueException('Tag head jako potomky může obsahovat pouze tagy - potomky MetadataContent nebo text. Pokus o přidání potomka '. get_class($node).' selhal.');
        }
        return parent::addChild($node);
    }
    
    /**
     * 
     * @return GlobalAttributes
     */
    public function getAttributesNode() {
        return $this->attributes;
    }    
}
