<?php
namespace Pes\Query;

use Pes\Query\Matcher\MatcherInterface;
/**
 *
 * @author pes2704
 */
interface ConditionInterface {
    public function getOperator();

    /**
     * 
     * @return MatcherInterface
     */
    public function getMatcher();

    /**
     * 
     * @return string
     */
    public function getPattern();

    /**
     * 
     * @param string $operator
     * @return \Pes\Query\Condition
     */
    public function setOperator($operator);

    /**
     * 
     * @param MatcherInterface $matcher
     * @return \Pes\Query\Condition
     */
    public function setMatcher(MatcherInterface $matcher);

    /**
     * 
     * @param string $pattern
     * @return \Pes\Query\Condition
     */
    public function setPattern($pattern);
}
