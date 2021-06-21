<?php
use PHPUnit\Framework\TestCase;

use Pes\Type\ContextData;
use Pes\Type\Exception\InvalidDataTypeException;

/**
 * Test Pes\Type\Enum
 *
 * @author pes2704
 */
class ContextDataTest extends TestCase {

    /**
     * data provider pro testGetTypeValue v bázové třídě testu
     * @return type
     */
//    public function valuesProvider() {
//        $type = new TestTypeEnum();
//        foreach ($type->getConstList() as $value) {
//            $data[] = array($type, $value);
//        }
//        return $data;
//    }

###############################

    /**
     * vyhození výjimky pro hodnotu, která není povoleného typu
     */
    public function testExceptionInvalidDataTypeObjArg() {
        $this->expectException(InvalidDataTypeException::class);
        $type = new ContextData(new \stdClass());
    }

    /**
     * Vytvoření objektu pro hodnotu, která je povoleného typu
     */
    public function testConstructForNoArg() {
        $type = new ContextData();
        $this->assertInstanceOf(ContextData::class, $type);
    }

    /**
     * Vytvoření objektu pro hodnotu, která je povoleného typu
     */
    public function testConstructForArrayArg() {
        $type = new ContextData([]);
        $this->assertInstanceOf(ContextData::class, $type);
    }

    /**
     * Vytvoření objektu pro hodnotu, která je povoleného typu
     */
    public function testConstructForArrayObjectArg() {
        $type = new ContextData(new \ArrayObject());
        $this->assertInstanceOf(ContextData::class, $type);
    }

    /**
     * Vytvoření objektu pro hodnotu, která je povoleného typu
     */
    public function testConstructForNoArg() {
        $type = new ContextData();
        $this->assertInstanceOf(ContextData::class, $type);
    }

    /**
     * Vytvoření objektu pro hodnotu, která je povoleného typu
     */
    public function testExchangeDataForArrayArg() {
        $type = new ContextData();
        $type->exchangeData([]);
        $this->assertInstanceOf(ContextData::class, $type);
    }

    /**
     * Vytvoření objektu pro hodnotu, která je povoleného typu
     */
    public function testExchangeDataForArrayObjectArg() {
        $type = new ContextData();
        $type->exchangeData(new \ArrayObject());
        $this->assertInstanceOf(ContextData::class, $type);
    }
}
