<?php

namespace Pes\Slot;

/**
 * Description of ReplacementsCreator
 * Utilita pro vytváření pole replacements vhodného pro Replacer.
 * Vytváří pole replacemets z asociativního pole nebo z objektu s použitím zadaných značek začátku a konce slotu.
 *
 * @author pes2704
 */
class ReplacementsCreator {
    
    private $slotLeft;
    private $slotRight;
    
    public function __construct($slotLeft, $slotRight) {
        $this->slotLeft = $slotLeft;
        $this->slotRight = $slotRight;
    }
    
    /**
     * Vytvoří pole pro záměnu slotů z asociativního pole.
     * Z klíčů pole vytvoří klíče pole pro záměny tak, že vlavo přidá hodnoty použije jako hodnory pole pro záměny. Např. pro slot slotLeft= '--%' a slotLeft='%--' 
     * a pro klíč asociativního pole coDelaToZvire s hodnotou 'kváká" vytvoří položku '--%coDelaToZvire%--'=>'kváká'.
     * @param array $assotiativeArray
     * @return array
     */
    public function createFromArray(array $assotiativeArray) {
        foreach ($assotiativeArray as $key => $value) {
            $replacements[$this->slotLeft.$key.$this->slotRight] = $value;
        }
        return $replacements;
    }
    
    /**
     * Z objektu. Použije pouze public vlastnosti objektu
     * @param array $object
     * @return array
     */
    public function createFromObject( $object) {
        foreach ($object as $key => $value) {
            $replacements[$this->slotLeft.$key.$this->slotRight] = $value;
        }
        return $replacements;
    }
}
