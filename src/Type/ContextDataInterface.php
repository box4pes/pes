<?php

namespace Pes\Type;

use Psr\Log\LoggerInterface;

/**
 *
 * @author pes2704
 */
interface ContextDataInterface extends  \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {
    /**
     *
     */
    public function setDebugMode();

    /**
     *
     */
    public function getDebugMode();

    /**
     *
     */
    public function getStatus();

    /**
     * Metoda přidá data z pole nebo \ArrayObject zadaného jako parametr.
     * @param mixed $appendedData array nebo \ArrayObject
     * @return \ContextDataInterface
     */
    public function exchangeData($data): ContextDataInterface;

    /**
     * Metoda přidá data z pole nebo \ArrayObject zadaného jako parametr.
     * @param mixed $appendedData array nebo \ArrayObject
     * @return \ContextDataInterface
     */
    public function appendData($appendedData): ContextDataInterface ;

    /**
     * Metoda vrací hodnotu uloženou se zadaným indexem převedenou na string. Pokud hodnota se zadaným indexem neexistuje, vrací default hodnotu.
     * Pokud default hodnota nebyla zadána je default hodnotou prázdný řetězec.
     *
     * @param type $index
     * @param string $defaultValue
     * @return string
     */
    public function getStringValueIfExists($index, $defaultValue=''): string;
}
