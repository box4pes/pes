<?php
namespace Pes\Query;

use Pes\Query\ConditionInterface;
use Pes\Query\Matcher\MatcherInterface;
/**
 *
 * @author pes2704
 */
interface CriteriaInterface {
    public function addCondition($attributeName, ConditionInterface $condition);
    public function addSubCriteria(CriteriaInterface $criteria);
    public function match($param);
    public function getSqlString();
}
