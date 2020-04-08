<?php
namespace Pes\Comparator;

use Pes\Query\Order;

/**
 * Description of OrderComparator
 *
 * @author pes2704
 */
class OrderComparatorClassMethods implements ComparatorInterface {
    /**
     * Metoda vrací komparátor pro základní řazení objektů, napodobuje SQL ORDER.
     * Komparátor je callable funkce vhodná pro použití v metodě uasort.
     * 
     * Vrácená callable funkce stanoví pořadí pro "multisort", tedy třídění podle všech vlastností zadaných v poli $order. 
     * Porovnáná podle hodnot vlastností objektů tak, že porovnává řetězcové vyjádření vlastností. 
     * To znamená, ře porovnává stejně, jako to dělá databáze na základě sql klauzule ORDER.
     * POZOR - pokud porovnánvané objekty jako vlastnosti mají objekty, které nejdou převést na řetězce (nemají __toString) vyhazuje warning 
     * "Warning: strcmp() expects parameter 1 to be string, object given".
     * 
     * @param array $order Pole ve formátu array('nazev1'=>'ASC', 'nazev2=>'DESC'). nazev1, nazev 2 jsou názvy vlastností objektů - členů kolekce
     *                      a hodnoty 'ASC' 'DESC' určují směr razení. Přípustné hodnoty směru jsou pouze řetězce 'ASC' 'DESC', názvy metod je třeba uvádět 
     *                      bez závorek  (např. "getA" nikoli "getA()").
     * @return callable
     * 
     */
    public static function getCompareFunction(Order $order) {
    // verze pro ORDER - řadí s použitím strcmp - pokud prvek pole nebo vlastnost onjektu není převoditelná na string hlásí warning
    //  - ale nějak taky třídí
    //  Tuto verzi lze použít tak, že pro databáze se stejné pole order použije pro vytvoření SELECTu pokud ještě nebyla načtena data 
    //  do kolekce, pokud už byla setřídí se se stejným parametrem kolekce
        
        $comparators = array(
            'ASC' => function ($a, $b) {return strcmp($a, $b);},
            'DESC' => function ($a, $b) {return strcmp($b, $a);},
        );
    // pro volání metod objektů vracejících porovnávané hodnoty se používá syntaxe "Variable functions", viz    http://php.net/manual/en/functions.variable-functions.php
        return function ($a, $b) use ($comparators, $order){
                                            foreach($order as $ordering){
                                                $classMethod = $ordering['attribute'];
                                                $res = $comparators[$ordering['type']]($a->$classMethod(), $b->$classMethod());
                                                if($res!=0) break;
                                            }
                                            return $res;
                                        };
    }
}
