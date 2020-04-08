<?php
namespace Pes\Collection;

/**
 * Description of SetCollection
 *
 * @author pes2704
 */
class SetCollection extends CollectionAbstract implements SetCollectionInterface {

    /**
     * Vytvoří novou kolekci.
     *
     */
    public function __construct(array $array=[]) {
        parent::__construct();
        foreach ($array as $value) {
            $this->set($value);
        }
    }

    /**
     * Přidá prvek do kolekce.
     * Pokud prvek je již v kolekci obsažen, metoda nahradí starý prvek novým.
     *
     * @param mixed $element Prvek, který má být přidán.
     */
    public function set($element) {
        $this->internalStorage->offsetSet($this->getIndex($element), $element);
        return $this;
    }

    public function getArrayCopy() {
        return array_values($this->internalStorage->getArrayCopy());
    }

    /**
     * Zjistí, zda je zadaný prvek v kolekci.
     * @param mixed $element Prvek, jehož přítomnost v kolekci zjišťuji.
     * @return boolean
     */
    public function has($element) {
        $index = $this->getIndex($element);
        return $this->hasByIndex($index);
    }

    /**
     * Odstraní zadaný prvek z kolekce.
     * @param mixed $element Prvek kolekce, který má být smazán..
     */
    public function remove($element) {
        $index = $this->getIndex($element);
        if ($this->hasByIndex($index)) {
            $this->internalStorage->offsetUnset($index);
        }
        return $this;
    }

    /**
     *
     * @param type $index
     * @return boolean
     */
    private function hasByIndex($index) {
        return $this->internalStorage->offsetExists($index) ? TRUE : FALSE;
    }

    /**
     * Privátní metoda - generuje index, pod kterým je prvek ukládán v kolekci.
     * @param type $param
     * @return type
     */
    private function getIndex($param) {
        if (is_object($param)){
            return spl_object_hash($param);
        } else {
            return $param;
        }
    }
}
