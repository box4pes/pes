<?php
namespace Pes\Comparator;

use Pes\Query\Order;

/**
 * Description of SortComparator
 *
 * @author pes2704
 */
class SortComparatorClassMethods implements ComparatorInterface {  
    /**
     * Metoda vrací komparátor pro pokročilejší řazení skalárů, polí a objektů.
     * Komparátor je callable funkce vhodná pro použití v metodě uasort.
     * 
     * Vrácená callable funkce stanoví pořadí pro "multisort", tedy třídění podle všech vlastností zadaných v poli $order. 
     * Porovnáná skaláry a objekty tak, že porovnává řetězcové vyjádřenískaláru nebo objektu.
     * 
     * @param array $order Pole ve formátu array('nazev1'=>'ASC', 'nazev2=>'DESC'). nazev1, nazev 2 jsou názvy metod objektů - členů kolekce
     *                      a hodnoty 'ASC' 'DESC' určují směr razení. Přípustné hodnoty směru jsou pouze řetězce 'ASC' 'DESC', názvy metod je třeba uvádět 
     *                      bez závorek  (např. "getA" nikoli "getA()").
     * @return callable
     * 
     */
    public static function getCompareFunction(Order $order) {
    // verze pro SORT - vylepšené řazení - pokud je to skalár nebo objekt s metodou __toString, řadí podle řetězcové hodnoty, 
    // nestringovatelný objekt serializuje a porovnává serializované podoby. V reálu to jde použít, obvykle pro řazení v zobrazeném seznamu.
    // myslím, že to bude umět např. objekt Identity
        
    $comparators = array(
            'ASC' => function ($a, $b) {
                    if (is_scalar($a) OR method_exists($a, '__toString')) {
                        $stra = (string) $a;
                    } else {
                        $stra = serialize($a);
                    }
                    if (is_scalar($b) OR method_exists($b, '__toString')) {
                        $strb = (string) $b;
                    } else {
                        $strb = serialize($b);
                    }
                    return strcmp($stra, $strb);
                },
            'DESC' => function ($a, $b) {
                    if (is_scalar($a) OR method_exists($a, '__toString')) {
                        $stra = (string) $a;
                    } else {
                        $stra = serialize($a);
                    }
                    if (is_scalar($b) OR method_exists($b, '__toString')) {
                        $strb = (string) $b;
                    } else {
                        $strb = serialize($b);
                    }
                    return strcmp($strb, $stra);
                },
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
