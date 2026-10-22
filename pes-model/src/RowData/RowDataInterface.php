<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\RowData;

/**
 *
 * @author pes2704
 */
interface RowDataInterface extends \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {

    // extenduje všechna rozhraní, která implementuje \ArrayObject mimo \Traversable - to nelze neb je prázdné

    /**
     * Vrací TRUE, pokud hodnoty položek byly změněny od instancování objektu nebo od posledního volání metody ->fetchChanged().
     *
     * Změnou hodnoty položky je i změna typu nebo záměna instance objektu za jinou.
     *
     * @return bool
     */
    public function isChanged(): bool;

    /**
     * Vrací pole jmen hodnot změněných od instancování RowData objektu nebo od posledního volání této metody.
     *
     * Změnou hodnoty položky je i změna typu nebo záměna instance objektu za jinou.
     *
     * Metoda vrací evidovaná jména změněných hodnot a evidenci jmen hodnot smaže. Další změny jsou pak dále evidovány a příští volání
     * této metody vrací jen tyto další změny.
     *
     * @return array
     */
    public function fetchChanged(): array;

    /**
     * Vrací nový ArrayObject obsahující změněné hodnoty. Původní RowData objekt zůstane nezměněn, ani evidované změny (changedNames()) se nemění.
     *
     * @return \ArrayObject
     */
    public function yieldChangedAsArrayObject(): \ArrayObject;
    
    /**
     * Přidá všechny položky iterable parametru. Všechna importovaná data přídá jako nové hodnoty.
     *
     * @param iterable $iterablaData
     */
    public function import(iterable $iterablaData);

    /**
     * Přidá hodnotu do objektu bez kontroly, zda jde o změněná data a bez vlivu na hodnoty zaregistrované ja změněné.
     * - pro PdoRowData metodu __set()
     * - pro asociované entity v agregátech - přidání asociované entity do rodičovské entity metodou offsetSet by vždy vedlo k tomu,
     *   že asociovaná entita je v rodičovském RowData vložena jako changed
     *
     * @param type $index
     * @param type $value
     */
    public function forcedSet($index, $value);
}
