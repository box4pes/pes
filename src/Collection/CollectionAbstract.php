<?php
namespace Pes\Collection;

use ArrayObject;

/**
 * CollectionAbstract
 */
abstract class CollectionAbstract implements CollectionInterface {

    /**
     *
     * @var \ArrayObject
     */
    protected $internalStorage;

    /**
     * Vytvoří novou prázdnou kolekci
     *
     */
    public function __construct() {
        $this->internalStorage = new ArrayObject();
    }

    public function __clone() {
        $this->internalStorage = clone $this->internalStorage;
    }

    public function count() {
        return $this->internalStorage->count();
    }

    public function getArrayCopy() {
        return $this->internalStorage->getArrayCopy();
    }

    public function getIterator() {
        return $this->internalStorage->getIterator();
    }

    /**
     * Metoda seřadí prvky kolekce s pouřitím porovnávací funkce zadané jako parametr.
     * Porovnávací funkce musí porovnávat členy kolekce, vracet při volání callback(první, druhý) tyto hodnoty:
     * 1 pokud 'první' má být před 'druhý', 0 pokud je pořadí členů stejné, -1 pokud má být 'druhý' před 'první'.
     * Metoda interně používá metodu uasort, pro porobnosti o porovnávací funkci viz dokumentace php.
     * Po použití metody probíhá následné iterování kolekce (foreach) v setříděném pořadí, jen pozor - indexy členů kolekce se tříděním nijak nemění.
     *
     * @param \Pes\Collection\callable $comparator Porovnávaví funkce
     */
    public function sort(callable $comparator) {
        $this->internalStorage->uasort($comparator);
    }
}

