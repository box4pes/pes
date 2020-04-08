<?php

namespace Pes\Dom\Node;

use Pes\Dom\Node\Attributes\AttributesInterface;
use Pes\Dom\Node\Attributes\NullAttributes;


/**
 * Description of NodeAbstract
 * @author pes2704
 */
abstract class NodeAbstract implements NodeInterface {

    protected $name;

    /**
     * @var AttributesInterface
     */
    protected $attributes;

    /**
     * @var Tag\TagInterface
     */
    protected $parent;

    /**
     *
     * @var NodeInterface array of
     */
    protected $childrens = array();

    /**
     * {@inheritdoc}
     * @return type
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Metody potomků getAttributesNode() jsou prakticky všechny stejné, ale mají v doc bloku nastavenou jinou návratovou hodnotu
     * - příslušný objekt atributů. Našeptávání tak je funkční.
     * Setter neexistuje, všechny tagy mají povinný typ objektu atributů a lze jen měnit hodnoty jednotlivých atributů
     * Příklad: $tag->getAttributes()->setAttribute($key, $value);
     * Nastavování objektu atributů objektu node není implementováno, node attributes má vždy hodnoru NullAttributes.
     */
    abstract function getAttributesNode();

    private function setDefaultAttributesIfUndefined() {
        if (!isset($this->attributes)) {
            $this->attributes = new NullAttributes;
        }
    }

    public function getAttribute($name) {
        $this->setDefaultAttributesIfUndefined();
        return $this->attributes->getAttribute($name);
    }

    public function setAttribute($name, $value): NodeInterface {
        $this->setDefaultAttributesIfUndefined();
        $this->attributes->setAttribute($name, $value);
        return $this;
    }

    public function hasAttribute($name) {
        $this->setDefaultAttributesIfUndefined();
        return $this->attributes->hasAttribute($name);
    }

    /**
     * {@inheritdoc}
     * @param NodeInterface $node
     * @return $this
     */
    public function addChild(NodeInterface $node) {
        $node->setParent($this);
        $this->childrens[] = $node;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(NodeInterface $node) {
        $this->parent = $node;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent() {
        return $this->parent;
    }

    public function hasChildrens() {
        return count($this->childrens) ? TRUE : FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrens() {
        return $this->childrens;
    }
}

