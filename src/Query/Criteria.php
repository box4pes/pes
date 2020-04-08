<?php
namespace Pes\Query;

use Pes\Query\Condition;
use Pes\Query\Matcher\CompareMatcher;   
use Pes\Query\Matcher\LikeMatcher;   
use Pes\Query\Matcher\InMatcher;   
use Pes\Query\Matcher\NotInMatcher;   

use Pes\Query\LogicOperatorEnum;

/**
 * Description of Criteria
 *
 * @author pes2704
 */
class Criteria implements CriteriaInterface {
    /**
     *
     * @var Condition array of 
     */
    private $conditions = array();
    private $logicOperator;
    
    /**
     *
     * @var CriteriaInterface array of 
     */
    private $subCriteria = array();
    
    public function __construct($logicOperator = LogicOperatorEnum::AND_OPERATOR) {
        $this->logicOperator = $logicOperator;
    }
    
    public function addSubCriteria(CriteriaInterface $subCriteria) {
        $this->subCriteria[] = $subCriteria;
        return $this;
    }
    
    /**
     * Přidá podmínku.
     * @param type $attributeName
     * @param \Pes\Query\ConditionInterface $condition
     */
    public function addCondition($attributeName, ConditionInterface $condition) {
        $this->conditions[$attributeName] = $condition;
        return $this;
    }
    
    /**
     * Helper metoda pro přidání podmínky. Metoda podle zadaného operátoru přidá tomuto objektu Criterie vhodný objekt Condition.
     * Rozpozná tyto operátory <code> "=":"!=";"<>":"<":"<=":">":">=":"LIKE":"IN":"NOT IN": </code>
     * @param string $attributeName
     * @param string $operator
     * @param mixed $pattern
     * @return \Pes\Query\Criteria
     * @throws \DomainException Unknown conditon operator.
     * @throws \InvalidArgumentException Operator must be a string.
     */
    public function addConditionByOperator($attributeName, $operator, $pattern) {
        if (is_string($attributeName) AND is_string($operator)) {
            $operator = strtoupper(trim($operator));
            switch ($operator) {
                case "=":
                case "!=":
                case "<>":
                case "<":
                case "<=":
                case ">":
                case ">=":
                    $this->addCondition($attributeName, (new Condition())->setOperator($operator)
                                                                        ->setMatcher(new CompareMatcher($operator))
                                                                        ->setPattern($pattern));
                    break;
                
                case "LIKE":
                    $this->addCondition($attributeName, $this->conditions[$attributeName] = (new Condition())->setOperator($operator)
                                                                        ->setMatcher(new LikeMatcher())
                                                                        ->setPattern($pattern));
                    break;

                case "IN":                 
                    $this->addCondition($attributeName, (new Condition())->setOperator($operator)
                                                                        ->setMatcher(new InMatcher())
                                                                        ->setPattern($pattern));
                    break;
                
                case "NOT IN":
                    $this->addCondition($attributeName, (new Condition())->setOperator($operator)
                                                                        ->setMatcher(new NotInMatcher())
                                                                        ->setPattern($pattern));
                    break;

                default:
                    throw new \DomainException("Unknown conditon operator: ".\var_export($operator, TRUE).".");
            }
        } else {
            throw new \InvalidArgumentException("Operator must be a string:".\var_export($operator, TRUE).".");            
        }
        return $this;
    }
    
    /**
     * Zjistí shodu dat s kritérii a podmínkami. Porovnává pouze prvky, které jsou použity v kritériích a podmínkách.
     * Jednitlivá kritéri a podmínky pak vševny vydnotí ve logickém součinu, pokud byla instanční proměnná $logicOperator zadána AND (nebo nezadána 
     * - defaultní hodnota je AND) případně v logickém součtu, pokud byla instanční proměnná $logicOperator zadána OR.
     * Pokud jsou data asociativní pole použije prvky pole. 
     * Pokud jsou data objekt použije public vlastnosti objektu.
     * @param array/object $data Data jeichž shoda s podmínkou se má ověřit. Data jsou asociatívní pole nebo objekt.
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function match($data) {
        $matches = array();
        foreach ($this->subCriteria as $subCriteria) {
            $matches[] = $subCriteria->match($data);
        }        
        if (is_array($data)) {
            foreach ($this->conditions as $name => $condition) {
                if (isset($data[$name])) {
                    $matches[] = $condition->getMatcher()->match($data[$name], $condition->getPattern());
                }
            }            
        } elseif (is_object($data)) {
            foreach ($this->conditions as $name => $condition) {
                if (isset($data->$name)) {                
                    $matches[] = $condition->getMatcher()->match($data->$name, $condition->getPattern());
                }
            }                        
        } else {
            throw new \InvalidArgumentException('Parameter must be a assotiative array or a object.');
        }
        if ($this->logicOperator === LogicOperatorEnum::AND_OPERATOR) {
            $match = (boolean) array_product($matches);
        } else {
            $match = (boolean) array_sum($matches);            
        }
        return $match;
    }
    
    public function getSqlString() {
        foreach ($this->subCriteria as $subCriteria) {
            $sqlToken[] = $subCriteria->getSqlString();
        }
        foreach ($this->conditions as $name => $condition) {
            $sqlToken[] = $name.' '.$condition->getOperator().' '.$condition->getPattern();
        }
        $sql = $sqlToken ? '('.implode(') '.$this->logicOperator.' (', $sqlToken).')' : '';
        return $sql;
    }
}
