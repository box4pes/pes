<?php
namespace Pes\Query\Matcher;

/**
 * Description of NotInMatcher
 * Ověřuje zda hodnota odpovídá vzoru obdobně jako SQL operátor NOT IN. 
 * @author pes2704
 */
class NotInMatcher implements MatcherInterface {
    /**
     * 
     * @param type $value Hodnota, která se hledá v poli hodnot.
     * @param array $pattern Pole hodnot, se kterými se porovnává.
     * @return boolean
     */
    public function match($value, $pattern) {
        $pattern = (array) $pattern;
        if (array_search($value, $pattern)===FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
