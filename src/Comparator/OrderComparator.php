<?php
namespace Pes\Comparator;

use Pes\Query\Order;

/**
 * Description of OrderComparator
 *
 * @author pes2704
 */
class OrderComparator implements ComparatorInterface {
    /**
     * Metoda vrací komparátor pro "řetězcové" řazení objektů, porovnává hodnoty řetězců a napodobuje SQL ORDER. Nedostakem je, že selhává, pokud
     * řazené objekty nelze převést na string, výhodou je, že přesně napodobuje funkci SQL ORDER.
     * Je vhodná k použití např. v repository, kdy před načtením dat z databáze je vhodné použít SQL příkaz s klasulí ORDER a po načtení dat lze
     * se stejným parametrem typu Order třídit kolekci.
     * Komparátor je callable funkce vhodná pro použití v metodě uasort.
     * 
     * Vrácená callable funkce je vhodná i pro stanovení pořadí při "multisort" třídění, tedy třídění podle všech vlastností zadaných v poli $order. 
     * Porovnáná podle hodnot vlastností objektů tak, že porovnává řetězcové vyjádření vlastností. 
     * To znamená, ře porovnává stejně, jako to dělá databáze na základě sql klauzule ORDER.
     * POZOR - pokud porovnánvané objekty jako vlastnosti mají objekty, které nejdou převést na řetězce (nemají __toString) vyhazuje warning 
     * "Warning: strcmp() expects parameter 1 to be string, object given".
     * 
     * @param array $order Pole ve formátu array('nazev1'=>'ASC', 'nazev2=>'DESC'). nazev1, nazev 2 jsou názvy vlastností objektů - členů kolekce
     *                      a hodnoty 'ASC' 'DESC' určují směr razení. Přípustné hodnoty směru jsou pouze řetězce 'ASC' 'DESC'
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
        return function ($a, $b) use ($comparators, $order){
                                            foreach($order as $ordering){
                                                $attribute = $ordering['attribute'];
                                                $res = $comparators[$ordering['type']]($a->$attribute, $b->$attribute);
                                                if($res!=0) break;
                                            }
                                            return $res;
                                        };
    }
}
