<?php

namespace Pes\Type;

use Psr\Log\LoggerInterface;

/**
 * Description of ContextData
 *
 * @author pes2704
 */
class ContextData extends \ArrayObject implements ContextDataInterface {

    const GET_EXISTING_VALUE = 'get';
    const GET_NONEXISTING_VALUE = 'try to get unsetted value';
    const IS_EXISTING_VALUE = 'isset on existing value';
    const IS_NONEXISTING_VALUE = 'isset on nonexisting value';


    /**
     *
     * @var \ArrayObject or array
     */
    protected $context;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    private $debugMode=FALSE;

    private $status;

    /**
     * Třífa je wrapper pro ArrayObject. Tato třída přijímá data pouze buď jako pole nebo jako ArrayObject.
     * Zaznamenává užití dat - t.j. čtení, zápis dat pokud se s objektem pracuje jako s polem
     * (např. $x = $data['jmeno']  $data['jmeno'] = $y) a dotazy na existenci dat (např. isset($data['jmeno']))
     *
     * @param \ArrayObject $data
     * @param int $flags
     * @param string $iterator_class
     * @throws UnexpectedValueException
     */
    public function __construct($data = '[]', int $flags = 0, string $iterator_class = "ArrayIterator") {
        if (!(is_array($data) OR $data instanceof \ArrayObject)) {
            throw new UnexpectedValueException('Argument musí být pole nebo ArrayObject.');
        }
        parent::__construct($data, $flags, $iterator_class);
    }

    public function setDebugMode($debug=TRUE) {
        $this->debugMode = (boolean) $debug;
        return $this;
    }

    public function getDebugMode() {
        return $this->debugMode;
    }

    public function getStatus() {
        return $this->status;
    }

    public function offsetGet($index) {
        $g = parent::offsetGet($index);
        if ($this->debugMode) {
            if (isset($g)) {
                $this->status[$index][] = self::GET_EXISTING_VALUE;
            } else {
                $this->status[$index][] = self::GET_NONEXISTING_VALUE;
            }
        }
        return $g;
    }

    public function offsetExists($index) {
        $e = parent::offsetExists($index);
        if ($this->debugMode) {
            if ($e) {
                $this->status[$index][] = self::IS_EXISTING_VALUE;
            } else {
                $this->status[$index][] = self::IS_NONEXISTING_VALUE;
            }
        }
        return $e;
    }

    public function exchangeData($data): \ContextDataInterface {
        if (is_array($data)) {
            $ret = parent::exchangeArray($data);
        } elseif ($data instanceof \ArrayObject) {
            $ret = parent::exchangeArray($data->getArrayCopy());
        } else {
            throw new UnexpectedValueException('Argument musí být pole nebo ArrayObject.');
        }
        return $ret;
    }

    /**
     * (@inheritdoc)
     * @throws UnexpectedValueException
     */
    public function appendData($appendedData) {
        if (is_array($appendedData)) {
            parent::exchangeArray(array_merge($this->getArrayCopy(), $appendedData));
        } elseif ($appendedData instanceof \ArrayObject) {
            parent::exchangeArray(array_merge($this->getArrayCopy(), $appendedData->getArrayCopy()));
        } else {
            throw new UnexpectedValueException('Argument musí být pole nebo ArrayObject.');
        }
        return $this;
    }

    public function assign($name, $value) {
        $this->offsetSet($name, $value);
        return $this;
    }
}
