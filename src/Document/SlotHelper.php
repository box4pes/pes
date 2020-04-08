<?php

namespace Pes\Document;

/**
 * Description of Slot
 *
 * @author pes2704
 */
class SlotHelper {
    
    const DEFAULT_SLOT_INDEX = 'NEXT';
    
    /**
     * Metoda vrací řetězec vhodný jako slot do HTML dokumentu. Slot v dokumentu je řetězec, který může být nahrazován
     * například textem vloženého dokumentu. Slot pro HTML dokument je vytvořen zřetězením '<!-- %', parametru převedeného na velká písmena
     * a '% -->'. Vzniklý text slotu má formát html komentáře, proto se text slotu v html stránce v prohlížeči nezobrazuje když nebyl nahrazen.
     * @param string $text Zadaný text pro slot
     * @return string
     */
    public static function getSlotCode($text) {
        $text = (string) $text;
        return '<!-- %'.strtoupper($text).'% -->';
    }
    
    /**
     * Metoda vrací řetězec default slotu
     * @return string
     */
    public static function getDefaultSlotCode() {
        return self::getSlotCode(self::DEFAULT_SLOT_INDEX);
    }
}
