<?php

namespace Pes\Dom\Node\Attributes;

/**
 * Description of Attributes
 * Objekt obsahuje všechny HTML Global Attributes HTML5 elementů - mimo data-*.
 *
 * @author pes2704
 */
class GlobalAttributes extends AttributesAbstract {
    public $accesskey;
    public $class;
    public $contenteditable;
    public $contextmenu;
    //public $data-*;  to neumím
    public $dir;
    public $draggable;
    public $dropzone;
    public $hidden;
    public $id;
    public $lang;
    public $spellcheck;
    public $style;
    public $tabindex;
    public $title;
    public $translate;
}
