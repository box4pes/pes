<?php
namespace Pes\Query\Matcher;

/**
 * Description of CompareMatcher
 * Ověřuje zda hodnota odpovídá vzoru běžným způsobem používaným v SQL i jinde
 * @author pes2704
 */
class CompareMatcher implements MatcherInterface {
    
    private $compareFunction;
    
    /**
     * V parametru konstruktoru akceptuje běžné sql porovnávací operátory ("=", "!=":, "<>":, "<", "<=":, ">", ">=").
     * <p>Užití</p>
     * <code>
     * (new CompareMatcher('!='))->match($val, 555);
     * <úcode>
     * @param string $operator Nškterá z hodnot ("=", "!=":, "<>":, "<", "<=":, ">", ">=")
     * @throws \InvalidArgumentException
     */
    public function __construct($operator) {
        switch (trim($operator)) {
            case "=":
                $this->compareFunction = function ($value, $pattern) {return $value==$pattern;};
                break;
            case "!=":
                $this->compareFunction = function ($value, $pattern) {return $value!=$pattern;};
                break;
            case "<>":
                $this->compareFunction = function ($value, $pattern) {return $value<>$pattern;};
                break;
            case "<":
                $this->compareFunction = function ($value, $pattern) {return $value<$pattern;};
                break;
            case "<=":
                $this->compareFunction = function ($value, $pattern) {return $value<=$pattern;};
                break;
            case ">":
                $this->compareFunction = function ($value, $pattern) {return $value>$pattern;};
                break;
            case ">=":                
                $this->compareFunction = function ($value, $pattern) {return $value>=$pattern;};
                break;
            default:
                throw new \InvalidArgumentException('Invalid compare operator '.$operator);
        }
    }

    /**
     * 
     * @param string $value
     * @param string $pattern
     * @return boolean
     */
    public function match($value, $pattern) {
        $cmp = $this->compareFunction;
        return $cmp($value, $pattern);
    }
}
