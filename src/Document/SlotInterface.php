<?php

namespace Pes\Document;

/**
 *
 * @author pes2704
 */
interface SlotInterface {
    /**
     * Nastaví řetězec se jménem proměnné, do které bude dokument vkládán.
     * @param string $targetContextName
     */
    public function setTargetName($targetContextName);
    
    /**
     * Vrací řetězec se jménem proměnné, do které bude dokument vkládán.
     * @return string
     */
    public function getTargetName();
    
    /**
     * Zjistí, zda objekt má nastaven řetězec se jménem proměnné, do které bude dokument vkládán.
     * @return boolean
     */
    public function hasTargetName();
}