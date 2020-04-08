<?php
namespace Pes\Comparator;

use Pes\Query\Order;

/**
 *
 * @author pes2704
 */
interface ComparatorInterface {

    /**
     * Metoda vrací porovnávací funkci pro použití v metodě uasort.
     * Porovnávací funkce musí porovnávat členy kolekce, vracet při volání callback(první, druhý) tyto hodnoty: 
     * 1 pokud 'první' má být před 'druhý', 0 pokud je pořadí členů stejné, -1 pokud má být 'druhý' před 'první'.
     * @param Order $order
     */
    public static function getCompareFunction(Order $order);
}
