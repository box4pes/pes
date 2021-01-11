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
     * Přijímá string nebo objekt převeditelný na string, který použije jako textový obsah node.
     * @param string|stringable $text Textový obsah elementu.
     */
    public function __construct($text) {
        parent::__construct();
        $this->text = $text;
    }

    /**
     * (@inheritdoc)
     * Převede parametr konstruktoru na text (string) a ten vrací textový obsah node.
     * @return string
     */
    public function getText() {
        return (string) $this->text;
    }
}
