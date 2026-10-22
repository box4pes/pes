<?php

namespace Pes\View\Document;

use LogicException;
use Pes\View\Dom\Node\Tag\Html;
use Pes\View\Renderer\NodeRenderer;

/**
 * Objekt html dokumentu.
 * Oobsahuje elementy !doctype a html. Elementu !doctype je možno nastavovat vlastnosti pouze souhrnně ve formě řetězce.
 * Výchozí vlastnost je 'html' a defaultně vznikne <!DOCTYPE html>
 * Element html je typu tag Html.</p>
 *
 * @author pes2704
 */
class HtmlDocument extends DocumentAbstract {

    const DEFAULT_DOCTYPE_PROPERTY = 'html';

    /**
     *
     * @var string
     */
    private $doctype;

    /**
     * @var Html
     */
    private $htmlTag;

    /**
     * Konstruktor nastaví deklaraci !doctype, pokud parametr není zadán, pak na default hodnotu danou konstantou třídy a element html
     * naplní prázným tagem Html
     */
    public function __construct($propertiesString=self::DEFAULT_DOCTYPE_PROPERTY) {
        $this->setDoctype($propertiesString);
        $this->setHtmlTag(new Html());
    }

    /**
     * Metoda nastaví hodnotu deklarace !doctype.
     * @param string $text Text, který bude použit jako atributy tagu !doctype
     * @return $this
     */
    public function setDoctype($text) {
        $this->doctype = trim((string) $text);
        return $this;
    }

    /**
     * Metoda vrací hodnotu deklarace !doctype ve formě prostého textu.
     * @return string
     */
    public function getDoctype() {
        return $this->doctype;
    }

    /**
     * Nastaví html element dokumentu
     * @param Html $html
     * @return $this
     */
    public function setHtmlTag(Html $html) {
        $this->htmlTag = $html;
        return $this;
    }

    /**
     * Metoda vrací html element html dokumentu.
     * @return Html
     */
    public function getHtmlTag() {
        return $this->htmlTag;
    }
    /**
     * Metoda vrací html dokument ve formě prostého textu. Pro obsah dokumentu html
     * se použije text elementu !doctype a render elementu html.
     *
     * @return string Obsah dokumentu html ve formě prostého textu.
     */
    public function getString() {
        return '<!DOCTYPE '.$this->doctype.'>'.PHP_EOL.(new NodeRenderer())->renderSubtree($this->htmlTag);
    }

    /**
     * Slučování dokumentů přes vnitřní DOM API zatím není napojeno na {@see Html}.
     */
    public function includeDocument(DocumentInterface $includedHtmlDocument) {
        throw new LogicException('HtmlDocument::includeDocument() není v této verzi DOM implementováno.');
    }
}
