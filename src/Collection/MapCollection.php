<?php
namespace Pes\Collection;

/**
 * Kolekce párů klíč-hodnota
 *
 */
class MapCollection extends CollectionAbstract implements MapCollectionInterface {

    /**
     * Vytvoří novou kolekci
     *
     */
    public function __construct(array $array=[]) {
        parent::__construct();
        foreach ($array as $key=>$value) {
            $this->set($key, $value);
        }
    }

    /**
     * Přidá do kolekce hodnotu se zadaným klíčem.
     * Podle klíče, se kterým byl prvek přidán lze hodnotu získa metodou get().
     *
     * @param mixed $key Klíč, se kterým bude hodnota přidána.
     * @param mixed $value Hodnota prvku.
     *
     * @return void Nevrací žádnou návratovou hodnotu.
     */
    public function set($key, $value) {
        $this->internalStorage->offsetSet($key, $value);
        return $this;
    }

    /**
     * Odstraní člena kolekce podle zadaného klíče.
     * Pokud prvek se zadaným klíčem v kolekci neexistuje, metoda nedělá nic.
     *
     * @param mixed $key Klíč prvku, který má být odstraněn.
     * @return void Nevrací žádnou návratovou hodnotu.
     */
    public function remove($key) {
        if ($this->has($key)) {
            $this->internalStorage->offsetUnset($key);
        }
        return $this;
    }

    /**
     * Vrací hodnotu člena kolekce se zadaným klíčem.
     * Pokud prvek se zadaným klíčem v kolekci neexistuje, metoda vrací NULL.
     *
     * @param mixed $key Klíč prvku.
     * @return mixed Hodnota prvku se zadaným klíčem nebo NULL.
     */
    public function get($key) {
        if ($this->has($key)) {
            return $this->internalStorage->offsetGet($key);
        }
    }

    /**
     * Zjistí, zda prvek se zadaným klíčem je v kolekci.
     * @param mixed $key Klíč prvku.
     * @return boolean
     */
    public function has($key) {
        return $this->internalStorage->offsetExists($key);
    }
}

