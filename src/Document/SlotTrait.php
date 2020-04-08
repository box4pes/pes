<?php

namespace Pes\Document;

/**
 * Description of Slot
 *
 * @author pes2704
 */
trait SlotTrait {

    /**
     * Jméno proměnné view context, která bude nahrazena tímto dokumentem při jeho vkládání
     * @var string 
     */
    protected $targetContextName;    
    
    /**
     * Nastaví řetězec se jménem proměnné, do které bude dokument vkládán.
     * @param type $targetContextName
     * @return $this
     */
    public function setTargetName($targetContextName) {
        $this->targetContextName = (string) $targetContextName;
        return $this;
    }
    
    /**
     * Vrací řetězec se jménem proměnné, do které bude dokument vkládán.
     * @return string
     */
    public function getTargetName() {
        return $this->targetContextName;
    }
    
    /**
     * Zjistí, zda objekt má nastaven řetězec se jménem proměnné, do které bude dokument vkládán.
     * @return boolean
     */
    public function hasTargetName() {
        return $this->targetContextName ? TRUE : FALSE;
    }   
}

