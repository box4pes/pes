<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\NodeAbstract;


/**
 * Description of TagAbstract
 * @author pes2704
 */
abstract class TagAbstract extends NodeAbstract implements TagInterface {

    /**
     * Párový tag je renderován s otevírací i koncovou značkou, nepárový bez koncové značky s tím, že počáteční značka začíná
     * posloupností </
     * Defaultní hodnota pairTag je TRUE, pro povinně nepárové tagy je třeba nastavit pairTag=FALSE.
     *
     * @var boolean
     */
    protected $pairTag = TRUE;

    protected $childrens = array();

    /**
     * Informuje, zda tag je párový.
     * @return boolean
     */
    public function isPairTag() {
        return $this->pairTag;
    }

    /**
     * Nastavuje, zda tag je párový. Tato vlastnost ovlivňuje renderování tagu.
     * Párový tag je renderován s otevírací i koncovou značkou, nepárový bez koncové značky s tím, že počáteční značka začíná
     * posloupností </ .
     * Příklad: párový tag <a></a>, nepárový tag </br>.
     *
     * @param boolean $pairTag
     * @return $this
     */
    public function setPairTag($isPairTag = TRUE) {
        $this->pairTag = $isPairTag;
        return $this;
    }
}

