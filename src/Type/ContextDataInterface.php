<?php

namespace Pes\Type;

use Psr\Log\LoggerInterface;

/**
 *
 * @author pes2704
 */
interface ContextDataInterface extends  \IteratorAggregate, \Traversable, \ArrayAccess, \Serializable, \Countable {
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
    public function exchangeData($data): \ContextDataInterface;

    /**
     *
     * @param type $appendedData
     */
    public function appendData($appendedData);

    /**
     *
     * @param type $name
     * @param type $value
     */
    public function assign($name, $value);
}
