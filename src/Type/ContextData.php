<?php

namespace Pes\Type;

use Psr\Log\LoggerInterface;

use Pes\Type\ContextDataInterface;
use Pes\Type\Exception\InvalidDataTypeException;

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
     * @var LoggerInterface
     */
    protected $logger;

    private $debugMode=FALSE;

    private $contextStatus;

    /**
     * Třísa je wrapper pro ArrayObject. Tato třída přijímá data buď jako pole nebo jako ArrayObject.
     *
     * Při nastavení setDebugMode(true) zaznamenává užití dat - t.j. čtení, zápis dat pokud se s objektem pracuje jako s polem
     * (např. $x = $data['jmeno']  $data['jmeno'] = $y) a dotazy na existenci dat (např. isset($data['jmeno'])).
     * Užití dat lze zapsat do logu pomocí specializovaného objektu Pes\Type\ContextDataUsage.
     *
     * @param \ArrayObject $data
     * @param int $flags
     * @param string $iterator_class Default ArrayIterator
     * @throws InvalidDataTypeException
     */
    public function __construct($data = [], int $flags = 0, string $iterator_class = "ArrayIterator") {
        if (!(is_array($data) OR $data instanceof \ArrayObject)) {
            throw new InvalidDataTypeException('Argument data pro konstruktor ContextData musí být pole nebo ArrayObject. Data jsou typu'. gettype($data).'.');
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

    public function getContextStatus() {
        return $this->contextStatus;
    }

    public function offsetGet($index) {
        $g = parent::offsetGet($index);
        if ($this->debugMode) {
            if (isset($g)) {
                $this->contextStatus[$index][] = self::GET_EXISTING_VALUE;
            } else {
                $this->contextStatus[$index][] = self::GET_NONEXISTING_VALUE;
            }
        }
        return $g;
    }

    public function offsetExists($index) {
        $e = parent::offsetExists($index);
        if ($this->debugMode) {
            if ($e) {
                $this->contextStatus[$index][] = self::IS_EXISTING_VALUE;
            } else {
                $this->contextStatus[$index][] = self::IS_NONEXISTING_VALUE;
            }
        }
        return $e;
    }

    /**
     * {@inheritdoc}
     * @param mixed $appendedData array nebo \ArrayObject
     * @return ContextDataInterface
     * @throws InvalidDataTypeException
     */
    public function exchangeData($data): ContextDataInterface {
        if (is_array($data)) {
            $ret = parent::exchangeArray($data);
        } elseif ($data instanceof \ArrayObject) {
            $ret = parent::exchangeArray($data->getArrayCopy());
        } else {
            throw new InvalidDataTypeException('Argument musí být pole nebo ArrayObject.');
        }
        return $ret;
    }

    /**
     * {@inheritdoc}
     * @param mixed $appendedData array nebo \ArrayObject
     * @return ContextDataInterface
     * @throws InvalidDataTypeException
     */
    public function appendData($appendedData): ContextDataInterface  {
        if (is_array($appendedData)) {
            parent::exchangeArray(array_merge($this->getArrayCopy(), $appendedData));
        } elseif ($appendedData instanceof \ArrayObject) {
            parent::exchangeArray(array_merge($this->getArrayCopy(), $appendedData->getArrayCopy()));
        } else {
            throw new InvalidDataTypeException('Argument musí být pole nebo ArrayObject.');
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     * @param mixed $index
     * @param mixed $defaultValue
     * @return mixed|null
     */
    public function getContextVariable($index, $defaultValue=null) {
        return (string) $this->offsetExists($index) ? $this->offsetGet($index) : $defaultValue;
    }
}
