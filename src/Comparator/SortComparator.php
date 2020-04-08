<?php
namespace Pes\Comparator;

use Pes\Query\Order;

/**
 * Description of SortComparator
 *
 * @author pes2704
 */
class SortComparator implements ComparatorInterface { 
    
    //TODO: přepracuj na __invoke() - viz http://stackoverflow.com/questions/888064/php-5-3-magic-method-invoke BrunoRB
    
    /**
     * Metoda vrací komparátor pro "zobrazovací" řazení skalárů, polí a objektů. Pokud jsou porovnávané hodnoty typu skalár nebo objekt 
     * s metodou __toString, řadí podle řetězcové hodnoty, nestringovatelný objekt serializuje a porovnává serializované podoby. V praxi je to použitelná 
     * metoda např. pro účely zobrazení dat v tabulce. Hodí se tedy pro kontroler apod. Tento komparátor je vhodný v situaci, kdy je upřednostňován
     * výsledek i když nepřesně seřazený před chybou.
     * Komparátor je také vhodná callable funkce pro použití v metodě uasort.
     * 
     * Vrácená callable funkce je vhodná i pro stanovení pořadí při "multisort", tedy třídění podle všech vlastností zadaných v poli $order. 
     * Porovnáná skaláry a objekty tak, že porovnává řetězcové vyjádření skaláru nebo objektu.
     * 
     * @param array $order Pole ve formátu array('nazev1'=>'ASC', 'nazev2=>'DESC'). nazev1, nazev 2 jsou názvy vlastností objektů - členů kolekce
     *                      a hodnoty 'ASC' 'DESC' určují směr razení. Přípustné hodnoty směru jsou pouze řetězce 'ASC' 'DESC'
     * @return callable
     * 
     */
    public static function getCompareFunction(Order $order) {
    // verze pro SORT - vylepšené řazení - pokud je to skalár nebo objekt s metodou __toString, řadí podle řetězcové hodnoty, 
    // nestringovatelný objekt serializuje a porovnává serializované podoby. V reálu to jde použít, obvykle pro řazení v zobrazeném seznamu.
    // myslím, že to bude umět např. objekt Identity
    
    //TODO: ošetři situaci, kdy $a má __toString a $b ne - takže dojde k jeho serializaci, pak toto dává nesmyslný výsledek
    //TODO: nedřív rozhodni o typech argumentů - oba numeric, oba nenumeric scalar, oba __toString - jinak chyba, pak podle typu porovnávej    
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
        //TODO: tuto část (return function...) převíst na trait - jednu pro sort a druhou pro order komparátory
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
