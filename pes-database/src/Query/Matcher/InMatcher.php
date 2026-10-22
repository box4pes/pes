<?php
namespace Pes\Query\Matcher;

/**
 * Description of InMatcher
 * Ověřuje zda hodnota odpovídá vzoru obdobně jako SQL operátor IN. 
 * @author pes2704
 */
class InMatcher implements MatcherInterface {
    /**
     * 
     * @param type $value Hodnota, která se hledá v poli hodnot.
     * @param array $pattern Pole hodnot, se kterými se porovnává.
     * @return boolean
     */
    public function match($value, $pattern) {
        $pattern = (array) $pattern;
        if (array_search($value, $pattern)===FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
