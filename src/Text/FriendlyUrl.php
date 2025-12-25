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
     * Výsledný řetězec obsahuje pouze malá písmena, čísla pomlčky, podtržítka a vlnovky. Současně je výsledná text upraven tak, aby byl čitelný pro člověka.
     * 
     * Mezery a bílé znaky převede na pomlčku, znaky s háčky a čárkami na znaky bez háčků a čárek (provede transliteraci), lomítka / převede na vlovku ~.
     * Pokud je zadán prefix, připojí zadaný řetězec před vygenerované uri. Prefix musí být složen ze znaků bezpečných pro url, nijak se nekontroluje.
     * Pokud je zadána maximální délka, omezí výsledný řetězec na tuto délku - ořízne jej (celková délka včetně případného prefixu).
     * 
     * @param string $sourceText
     * @param string $prefix
     * @param int $maxLength
     * @return string
     */
    public static function friendlyUrlText($sourceText, $prefix = '', $maxLength = null) {
        $url = $sourceText;
        $url = str_replace('/', '~', $url);
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);  // nahradí pomlčkou: jeden nebo více znaků, které nejsou písmeno, číslice ani podtržítko
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);    // smaže (nahradí za prázdný řetězec):jeden nebo více znaků, které NEJSOU povoleny v dané množině - pomlčka, malá písmena a–z, číslice 0–9 a podtržítko
        $url = $prefix.$url;
        if (isset($maxLength) AND $maxLength>1) {
            $url = substr($url, 0, $maxLength);
        }
        return $url;
    }
}
