<?php

namespace Pes\Dom\Node\Text;

/**
 * Text je zjednodušeným modelem elementu node v DOM HTML. Má pouze textový obsah zadaný v konstruktoru.
 *
 * @author pes2704
 */
class Text extends TextAbstract implements TextInterface {

    /**
     * @var string Textový obsah elementu
     */    
    private $text;
    
    /**
     * 
     * Přijímá text, který použije jako textový obsah node.
     * @param string $text Textový obsah elementu.
     */
    public function __construct($text) {
        parent::__construct();
        $this->text = $text;
    }
    
    /**
     * (@inheritdoc)
     * Vrací textový obsah node, tedy text zadaný v konstruktoru.
     * @return string
     */
    public function getText() {
        return $this->text;
    } 
}
