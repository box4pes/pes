<?php

namespace Pes\Document;

/**
 * Description of Text
 *
 * @author pes2704
 */
class TextDocument extends DocumentAbstract {

    private $text;   
    
    /**
     * Přidá další text na místo kódu slotu nebo na konec obasahu.
     * @param string $text
     * @return string
     */
    private function includeText($text, $slot="") {
        if ($slot) {
            $this->text = str_replace($slot, $text, $this->text.$slot);
        } else {
            $this->text .= (string) $text;
        }
        return $this->text;
    }
     
    /**
     * Metoda vloží dokument do tohoto dokumentu. Pokud v textu tohoto dokumentu je řetězec $slot,
     * vloží text vkládaného dokumentu před řetězec $slot, výsledný dokument tedy obsahuje tentýž řetězec $slot pro případné 
     * další vložení do stejného slotu. Jinak vloží text vkládaného dokumentu na konec. 
     * Vkládaný dokument je vždy vložen jako text (řetězec). Pro převod je použita metoda getString() vkládaného dokumentu.
     * @param DocumentInterface $document
     * @param type $slot kód slotu. Je doporučeno získávat tento kód pomocí HelperSlot::getSlotCode(). 
     * Pokud parametr není zadán, metoda použije vždy default slot vracený HelperSlot::getDefaultSlotCode().
     * @return type
     */
    public function includeDocument(DocumentInterface $document, $slot="") {
        if ($slot) {
            return $this->includeText($document->getString(), $slot);
        } else {
            return $this->includeText($document->getString(), SlotHelper::getDefaultSlotCode());
        }
    }
    
    /**
     * Vrací obsah dokunmetu.
     * @return mixed
     */
    public function getString() {
        return $this->text;
    }  
}
