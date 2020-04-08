<?php

namespace Pes\Query;

use Pes\Query\ConditionInterface;
use Pes\Query\Matcher\MatcherInterface;

/**
 * Description of Condition
 *
 * @author pes2704
 */
class Condition implements ConditionInterface {
    /**
     *
     * @var string 
     */
    private $operator;
    /**
     *
     * @var MatcherInterface 
     */
    private $matcher;
    /**
     *
     * @var string 
     */
    private $pattern;
    
    /**
     * 
     * @return string
     */
    public function getOperator() {
        return $this->operator;
    }

    /**
     * 
     * @return MatcherInterface
     */
    public function getMatcher() {
        return $this->matcher;
    }

    /**
     * 
     * @return string
     */
    public function getPattern() {
        return $this->pattern;
    }

    /**
     * 
     * @param string $operator
     * @return \Pes\Query\Condition
     */
    public function setOperator($operator) {
        $this->operator = $operator;
        return $this;
    }

    /**
     * 
     * @param MatcherInterface $matcher
     * @return \Pes\Query\Condition
     */
    public function setMatcher(MatcherInterface $matcher) {
        $this->matcher = $matcher;
        return $this;
    }

    /**
     * 
     * @param string $pattern
     * @return \Pes\Query\Condition
     */
    public function setPattern($pattern) {
        $this->pattern = $pattern;
        return $this;
    }


}
