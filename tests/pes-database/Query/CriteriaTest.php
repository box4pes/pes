<?php
use PHPUnit\Framework\TestCase;

use Pes\Query\Criteria;

/**
 * Test Pes\Query\Criteria
 *
 * @author pes2704
 */
class CriteriaTest extends TestCase {

    private $arrayParam;
    private $objectParam;

    /**
     * setUp - nastavuje arrayParam pro testování dat ve formě pole a současně také objectParam
     * pro testování dat ve formě objektu. Všechny asserty jsou vždy dvakrát - jednou pro pole, podruhé pro objekt
     */
    public function setUp(): void {
        $this->arrayParam['a'] = 555;
        $this->arrayParam['b'] = 666;
        $this->arrayParam['name'] = 'Alibaba';
        $this->objectParam = (object) $this->arrayParam;
    }

    /**
     *
     */
    public function testCriteriaAndConditionMatch() {
        //první subkritérium - pravdivé
        $subCriteria1 = new Criteria(); //AND
        $subCriteria1->addConditionByOperator("a", '=', 555);
        $subCriteria1->addConditionByOperator("b", '!=', 555);
        // druhé subkritérium - pravdivé
        $subCriteria2 = new Criteria(); //AND
        $subCriteria2->addConditionByOperator('name', '=', 'Alibaba');
        $subCriteria2->addConditionByOperator('name', '!=', 'Loupežník');
        // složené kritérium - $subCriteria1 OR $subCriteria2 - pravdivé
        $criteria = new Criteria('OR');
        $criteria->addSubCriteria($subCriteria1)->addSubCriteria($subCriteria2);
        //assert pravda
        $this->assertTrue($criteria->match($this->arrayParam));
        $this->assertTrue($criteria->match($this->objectParam));
        // k prvnímu subkritériu přidána další podmínka - výsledek nepravda
        $subCriteria1->addConditionByOperator("b", '=', 555);
        //ještě assert pravda $subCriteria1 OR $subCriteria2 -> FALSE OR TRUE = TRUE
        $this->assertTrue($criteria->match($this->arrayParam));
        $this->assertTrue($criteria->match($this->objectParam));
        // k druhému subkritériu přidána další podmínka - výsledek nepravda
        $subCriteria2->addConditionByOperator('name', '=', 'Loupežník');
        // už assert nepravda - $subCriteria1 OR $subCriteria2 -> FALSE OR FALSE = FALSE
        $this->assertFalse($criteria->match($this->arrayParam));
        $this->assertFalse($criteria->match($this->objectParam));
        // k složeném kritériu přidána podmínka - pravdivá
        $criteria->addConditionByOperator('name', 'LIKE', '%liba%');
        // assert pravda $subCriteria1 OR $subCriteria2 OR condition -> FALSE OR FALSE OR TRUE = TRUE
        $this->assertTrue($criteria->match($this->arrayParam));
        $this->assertTrue($criteria->match($this->objectParam));
    }

    /**
     * data provider pro testConditionsMatch
     * Generuje jednotlivá kritéria - paremtry pro volání metody testConditionsMatch()
     * @return type
     */
    public function conditionsMatchValuesProvider() {
//  Podmínky jsou: "=":"!=";"<>":"<":"<=":">":">=":"LIKE":"IN":"NOT IN":

        $data[] = [[['a', '=', 555]], '', TRUE];
        $data[] = [[['a', '=', '555']], '', TRUE];
        $data[] = [[['b', '=', '666']], '', TRUE];
        $data[] = [[['a', 'IN', [111, 222, 333, 444, 555]]], '', TRUE];
        $data[] = [[['a', 'NOT IN', [111, 222, 333, 444, 555]]], '', FALSE];
        $data[] = [[['b', 'IN', [111, 222, 333, 444, 555]]], '', FALSE];
        $data[] = [[['b', 'NOT IN', [111, 222, 333, 444, 555]]], '', TRUE];

        $data[] = [[['name', '=', 'Alibaba']], '', TRUE];
        $data[] = [[['qqqq', '=', 'Alibaba']], '', FALSE]; // qqqq v datech není -> vždy FALSE
        $data[] = [[['name', 'LIKE', '%liba%']], '', TRUE];

        $data[] = [[['a', '=', '555'], ['b', '=', '666']], 'AND', TRUE];
        $data[] = [[['a', '!=', '556'], ['b', '<>', 'q']], 'AND', TRUE];
        $data[] = [[['a', '=', '55']], '', FALSE];
        $data[] = [[['b', '=', '66']], '', FALSE];
        $data[] = [[['a', '=', '555'], ['b', '=', '66']], 'AND', FALSE];
        $data[] = [[['a', '!=', '555'], ['b', '<>', 'q']], 'AND', FALSE];

        $data[] = [[['a', '=', '555'], ['b', '<>', 666], ['name', 'LIKE', 'Ali%']], 'AND', FALSE];
        $data[] = [[['a', '=', '555'], ['b', '<>', 666], ['name', 'LIKE', 'Ali%']], 'OR', TRUE];
        #

        return $data;
    }

    /**
     * Testuje různé podmínky (jen některé vybrané). Daty tento test zásobuje data provider.
     * @param type $arrayConditions
     * @param type $logicalOperator
     * @param type $properResult
     *
     * @dataProvider conditionsMatchValuesProvider
     */
    public function testConditionsMatch($arrayConditions, $logicalOperator, $properResult) {
        $criteria = new Criteria($logicalOperator);
        foreach ($arrayConditions as $cond) {
            $criteria = $criteria->addConditionByOperator($cond[0], $cond[1], $cond[2]);
        }
        if ($properResult) {
            $this->assertTrue($criteria->match($this->arrayParam));
            $this->assertTrue($criteria->match($this->objectParam));
        } else {
            $this->assertFalse($criteria->match($this->arrayParam));
            $this->assertFalse($criteria->match($this->objectParam));
        }
    }

}
