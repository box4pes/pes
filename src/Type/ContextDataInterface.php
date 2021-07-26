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

}
