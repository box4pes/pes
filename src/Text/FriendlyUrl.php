<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Text;

/**
 * Description of FriendlyUrl
 *
 * @author pes2704
 */
class FriendlyUrl implements FriendlyUrlInterface {

    /**
     * Převede zdrojový text na řetězec vhodný jako část url složený pouze ze znaků, které se nebudou v url kódovat. 
     * Současně je výsledná text upraven tak, aby byl čitelný pro člověka.
     * 
     * Výsledný řetězec obsahuje pouze malá písmena, čísla pomlčky a podtržítka. Mezery a bílé znaky převede na pomlčku, 
     * znaky s háčky a čárkami na znaky bez háčků a čárek (provede transliteraci).
     * Pokud je zadána maximální délka, omezí řetězec na tuto délku.
     * 
     * @param string $sourceText
     * @param type $maxLength
     * @return string
     */
    public static function friendlyUrlText($sourceText, $maxLength = null) {
        $url = $sourceText;
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
        if (isset($maxLength) AND $maxLength>1) {
            $url = substr($url, 0, $maxLength);
        }
        return $url;
    }
}
