<?php
namespace Pes\Query\Matcher;

/**
 * Description of LikeMatcher
 * Ověřuje zda hodnota odpovídá vzoru obdobně jako SQL operátor LIKE. Používá stejné wildcard znaky ('_' a '*') a akceptuje escape 
 * znak '\' pro escapování znaků '%' a '_', které nemají být chápány jako wildcard znaky. Escape znak je defaultně '\' a může být zadán jiný
 * pomocí parametru konstruktoru. Je shodně s SQL lIKE case insensitive.
 *
 * @author pes2704
 */
class LikeMatcher implements MatcherInterface {
    
    private $escape;
    
    /**
     * V konstruktoru akceptuje případný escape znak jiný než \. 
     * Tento znak je možné používat jako escape příznak před znaky ('%' nebo '_', které se jinak vyhodnocují jako sql wildcard znaky
     * <p>Užití</p>
     * <p>Pro SQL: sloupec LIKE 'bla%'</p>
     * <code>
     * (new LikeMatcher())->match($sloupec, 'bla%');
     * <code>
     * @param string $escape Jiný escape znak než defaultní ('\').
     */
    public function __construct($escape = '\\') {
        $this->escape = $escape;
    }
    
    /**
     * Matcher simuluje SQL LIKE. 
     * @param string $value
     * @param string $pattern
     */
    public function match($value, $pattern) {
        // http://stackoverflow.com/questions/11434305/simulating-like-in-php

        // Split the pattern into special sequences and the rest
        $expr = '/((?:'.preg_quote($this->escape, '/').')?(?:'.preg_quote($this->escape, '/').'|%|_))/';
        $parts = preg_split($expr, $pattern, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // Loop the split parts and convert/escape as necessary to build regex
        $expr = '/^';
        $lastWasPercent = FALSE;
        foreach ($parts as $part) {
            switch ($part) {
                case $this->escape.$this->escape:
                    $expr .= preg_quote($this->escape, '/');
                    break;
                case $this->escape.'%':
                    $expr .= '%';
                    break;
                case $this->escape.'_':
                    $expr .= '_';
                    break;
                case '%':
                    if (!$lastWasPercent) {
                        $expr .= '.*?';
                    }
                    break;
                case '_':
                    $expr .= '.';
                    break;
                default:
                    $expr .= preg_quote($part, '/');
                    break;
            }
            $lastWasPercent = $part == '%';
        }
        $expr .= '$/i';

        // Look for a match and return bool
        return (bool) preg_match($expr, $value);

    }
}
//public function like($needle, $haystack)
//{
//    // Escape meta-characters from the string so that they don't gain special significance in the regex
//    $needle = preg_quote($needle, '~');
//
//    // Replace SQL wildcards with regex wildcards
//    $needle = str_replace('%', '.*', $needle);
//    $needle = str_replace('_', '.', $needle);
//
//    // Add delimiters, modifiers and beginning + end of line
//    $needle = '~^' . $needle . '$~isu';
//
//    return (bool) preg_match($needle, $haystack);
//}