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
    public function testExceptionInvalidDataType() {
        $type = new ContextData();
        $this->expectException(InvalidDataTypeException::class);

    }


}
