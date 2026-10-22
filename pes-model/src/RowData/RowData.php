<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\RowData;

/**
 * Description of RowData
 * Objekt reprezentuje položku relace (např. řádek dat db tabulky). Výchozí data se nastaví jako instatnční proměnná. Změněná a nová data
 * jsou objektem ukládána, pouze pokud se hodnoty dat liší proti výchozím hodnotám dat. Objekt pak poskytuje metody pro vrácení změněných a nových položek dat pro účel zápisu do uložiště.
 *
 * @author pes2704
 */
class RowData extends \ArrayObject implements RowDataInterface {

    /**
     *
     * @var array
     */
    private $changed = [];

    /**
     * V kostruktoru se mastaví způsob zápisu dat do RowData objektu na ARRAY_AS_PROPS a pokud je zadán parametr $data, zapíší se tato data
     * do interní storage objektu. Nastavení ARRAY_AS_PROPS způsobí, že každý zápis dalších dat je prováděn metodou offsetSet a vyhodnocuje se, jestli data byla změněna.
     * Pokud parament data je iterable, jsou tato data zadaná do konstruktoru zapsána jako změněná.
     *
     * @param type $data
     */
    public function __construct($data=[]) {
        parent::__construct($data, \ArrayObject::ARRAY_AS_PROPS);
    }

    public function isChanged(): bool {
        return $this->changed ? true : false;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function fetchChanged(): array {
        $changed = $this->changed;
        $this->flipData();
        return $changed;
    }

    /**
     * Přidá nebo změní data. Data přidá nebo změní pouze v těchto případech:
     *
     * - přidání nové položky dat (volání metody s novým indexem)
     * - změna hodnoty skalárních dat
     * - změna hodnoty na null
     * - změna hodnoty z null na not null
     * - záměna hodnoty dat typu objekt dat za jiný objekt nebo jinou instanci objektu stejného typu.
     *
     * Metoda nic nemění pokud se změní jen vlastnosti objektu, který je hodnotou položky dat typu objekt.
     *
     * @param type $key
     * @param type $value
     */
    public function offsetSet(mixed $key, mixed $value): void {
        // změněná nebo nová data
        if (parent::offsetExists($key)) {
            if (parent::offsetGet($key) !== $value) {
                $this->setNewValue($key, $value);
            }
        } else {
            $this->setNewValue($key, $value);
        }
    }

    public function offsetUnset(mixed $key): void {
        if (!$this->offsetExists($key) OR $this->offsetGet($key)!==null) {
            $this->setNewValue($key, null);
        }
    }

    /**
     * Přidá všechny položky uvedené v parametru. Všechna tato data přídá jako nové hodnoty.
     *
     * @param iterable $iterableData
     */
    public function import(iterable $iterableData) {
        if (is_iterable($iterableData)) {
            foreach ($iterableData as $index => $value) {
                $this->setNewValue($index, $value);
            }
        }
    }

    public function exchangeArray(object|array $array): array {
        // Zde by se musely v cyklu vyhodnocovat varianty byla/nebyla data x jsou/nejsou nová data
        throw new \LogicException('Nelze použít metodu exchangeArray(). Použijte offsetSet().');
    }

    public function append(mixed $value): void {
        throw new \LogicException('Nelze vkládat neindexovaná data. Použijte offsetSet().');
    }

    // pro PdoRowData
    // - pro asociované entity v agregátech - přidání asociované entity do rodičovské entity metodou offsetSet by vždy vedlo k tomu, že asoiciovaná entota je v rodičovském RowData
    // vložena jako changed
    // pro generované hodnoty - autoincement
    public function forcedSet($index, $value) {
        parent::offsetSet($index, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @return \ArrayObject
     */
    public function yieldChangedAsArrayObject(): \ArrayObject {
//        return new \ArrayObject(array_intersect_key($this->getArrayCopy(), array_flip($this->changed)));
        return new \ArrayObject($this->changed);
    }

    ###################


    private function setNewValue($index, $value) {
        $this->changed[$index] = $value;
    }

    private function flipData() {
        if ($this->isChanged()) {
            foreach ($this->changed as $index => $value) {
                if (isset($value)) {
                    parent::offsetSet($index, $value);
                } else {
                    if (parent::offsetExists($index)) {
                        parent::offsetUnset($index);
                    }
                }
            }
            $this->changed = [];
        }
    }
}
