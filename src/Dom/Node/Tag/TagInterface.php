<?php

namespace Pes\Dom\Node\Tag;

use Pes\Dom\Node\NodeInterface;
use Pes\Dom\Node\Attributes\AttributesInterface;

/**
 * Description of TagInterface
 *
 * @author pes2704
 */
interface TagInterface extends NodeInterface {

    /**
     * Informace o tom, zda je tag "párový" a má otevírací i koncovou značku (např. <tag> a </tag>) nebo povinně "nepárový"
     * a má tedy značku např. <tag />.
     *
     * Párový tag má počáteční a koncovou značku a může mít obsah - buď text (Node\Text) nebo potmkovské tagy.
     * Jako nepárové jsou ve frameworku implementovány pouze povinně nepárové tagy,
     * jako párové jsou implementovány povinně párové tagy, tak i volitelně párové tagy.
     * Povinně a nepovinně párové tagy se nerozlišují a jsou defaultně renderovány vždy s otevírací i koncovou značkou.
     *
     * @return type
     */
    public function isPairTag();

    public function setPairTag();
}

