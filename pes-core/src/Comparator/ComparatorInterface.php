<?php
namespace Pes\Core\Comparator;

/**
 *
 * @author pes2704
 */
interface ComparatorInterface {

    /**
     * Metoda vrací porovnávací funkci pro použití v metodě uasort.
     * Porovnávací funkce musí porovnávat členy kolekce, vracet při volání callback(první, druhý) tyto hodnoty:
     * 1 pokud 'první' má být před 'druhý', 0 pokud je pořadí členů stejné, -1 pokud má být 'druhý' před 'první'.
     *
     * @param iterable<int, array{attribute: string, type: string}> $order Položky řazení (např. z {@see \Pes\Query\Order})
     */
    public static function getCompareFunction(iterable $order);
}
