<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Slot;

/**
 * Description of Replacer
 *
 * @author pes2704
 */
class Replacer {
    
    private $replacementKeys = array();
    private $replacementValues = array();
   
    /**
     * Nastaví asociativní pole náhrad.
     * Pole náhrad je asociativní pole, indexy jsou řetězce slotů. Metoda nijak nehlídá, jestli je pole korektní, má všechny klíče a hodnoty apod.
     * 
     * @param array $replacements
     */
    public function setReplacements(array $replacements) {
        $this->replacementKeys = array_keys($replacements);
        $this->replacementValues = array_values($replacements);
    }
    
    /**
     * Nahradí všechny nalezení řetězce slotů nalezené v obsahu hodnotami z pole náhrad.
     * 
     * @param string $content
     * @return string
     */
    public function replace($content) {
        return str_replace($this->replacementKeys, $this->replacementValues, $content );                   
    }
}
