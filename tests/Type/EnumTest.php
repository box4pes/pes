<?php
use PHPUnit\Framework\TestCase;

use Pes\Type\Enum;


class TestTypeEnum extends Enum {
    const UNO= 'uno';
    const DUE = 'due';
}

/**
 * Test Pes\Type\Enum
 *
 * @author pes2704
 */
class EnumTest extends TestCase {
    
    /**
     * data provider pro testGetTypeValue v bázové třídě testu
     * @return type
     */
    public function valuesProvider() {
        $type = new TestTypeEnum();
        foreach ($type->getConstList() as $value) {
            $data[] = array($type, $value);
        }
        return $data;
    }

###############################

    /**
     * vyhození výjimky pro hodnotu, která není enum typu
     */
    public function testExceptionValueNotInEnum() {
        try {
            $type = new TestTypeEnum();
            $blaType = $type('bla');   // Vyhodí výjimku
        } catch (UnexpectedValueException $uve) {
            $this->assertStringStartsWith('Value is not in enum', $uve->getMessage());
        }
    }

    /**
     * počet konstant = 2
     */
    public function testGetConstList() {
        $type = new TestTypeEnum();
        $this->assertEquals(2, count($type->getConstList()));
    }

    /**
     * pro všechny konstanty platí, že jsou daného enum typu
     * @param TestTypeEnum $enum
     * @param type $value
     *
     * @dataProvider valuesProvider
     */
    public function testGetTypeValue(TestTypeEnum $enum, $value) {
        $this->assertSame($value, $enum($value));
    }

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetUnoType() {
        $type = new TestTypeEnum();
        $this->assertSame("uno", TestTypeEnum::UNO);
        $this->assertSame("uno", $type(TestTypeEnum::UNO));
    }

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetDueType() {
        $type = new TestTypeEnum();
        $this->assertSame("due", TestTypeEnum::DUE);
        $this->assertSame("due", $type(TestTypeEnum::DUE));
    }
}
