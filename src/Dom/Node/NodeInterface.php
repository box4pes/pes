<?php

namespace Pes\Dom\Node;

use Pes\Dom\Node\Attributes\AttributesInterface;

/**
 *
 * @author pes2704
 */
interface NodeInterface {
    /**
     * Jméno node. Jméno node je nastavováno v konstruktoru a nelze ho měnit.
     *
     * @return string
     */
    public function getName();

    /**
     * Vrací objekt atributů.
     * Objekt atributů musí být vytvořen v konstruktoru.
     *
     * @return AttributesInterface
     */
    public function getAttributesNode();

    public function setAttribute($name, $value): self;

    public function getAttribute($name);

    public function hasAttribute($name);
    /**
     * @return TagInterface
     */
    public function getParent();

    /**
     * Nastaví rodiče.
     * @param NodeInterface $node
     */
    public function setParent(NodeInterface $node);

    /**
     * Přidá dalšího potomka - node.
     *
     * @param NodeInterface $node
     * @return $this
     */
    public function addChild(NodeInterface $node);

    public function hasChildrens();

    /**
     * Vrací pole potomků - pole nodů.
     * @return TagInterface array of
     */
    public function getChildrens();
}
