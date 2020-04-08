<?php

namespace Pes\Document;

use Pes\Document\Html\Tag\Html;

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
        $this->setHtmlElement(new Html());
    }
    
    /**
     * Metoda nastaví hodnotu deklarace !doctype.
     * @param string $text Text, který bude použit jako atributy tagu !doctype
     * @return Framework_Document_Html
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
        return '<!DOCTYPE '.$this->doctype.'>'.PHP_EOL.$this->htmlTag->render();
    }
    
    /**
     * Vloží html dokument do tohoto dokumentu. Pokud v elementu '<body>' tohoto html dokumentu je řetězec $slot,
     * vloží text elementu '<body>' vkládaného html dokumentu místo řetězce $slot. Jinak vloží 
     * text elementu '<body>' vkládaného html dokumentu na konec. Obsah elementu '<head>' vkládaného 
     * dokumentu vloží vždy na konec elementu '<head>' tohoto dokumentu.
     * @param Framework_Document_DocumentInterface $includedHtmlDocument Metoda akceptuje pouze dokument typu Framework_Document_Html
     * @throws LogicException 'Není možno zahrnout dokument '.get_class($mergedHtmlDocument).' do dokumentu '.get_class($mergedHtmlDocument).'.'
     * @throws LogicException Není možno sloučit html dokumenty s různými atributy !doctype.
     * @return Framework_Document_Html
     */
    public function includeDocument(Framework_Document_DocumentInterface $includedHtmlDocument, $targetSlot="") {
        if (get_class($includedHtmlDocument)==get_class($this)) {
            $includedDoctype = $includedHtmlDocument->getDoctype();
            if ($this->doctype != $includedDoctype) {
                throw new LogicException('Není možno sloučit html dokumenty s různými atributy !doctype: '.$includedDoctype.' a '.$this->doctype);
            }
            $includedHeadText = $includedHtmlDocument->getHtmlElement()->getHeadElement()->getInnerHtml();
            $this->htmlTag->getHeadElement()->mergeInnerHtml($includedHeadText, $targetSlot);
            $includedBodyText = $includedHtmlDocument->getHtmlElement()->getBodyElement()->getInnerHtml();
            $this->htmlTag->getBodyElement()->mergeInnerHtml($includedBodyText, $targetSlot);
        } else {
            throw new LogicException('Není možno zahrnout dokument '.get_class($includedHtmlDocument).' do dokumentu '.get_class($includedHtmlDocument).'.');
        }
        return $this;
    }
}
