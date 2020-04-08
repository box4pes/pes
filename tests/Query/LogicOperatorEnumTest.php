<?php
use PHPUnit\Framework\TestCase;

use Pes\Query\LogicOperatorEnum;
/**
 * Test Pes\Type\LogicOperatorEnumTest
 *
 * @author pes2704
 */
class LogicOperatorEnumTest extends TestCase {

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetDefaultAccessType() {
        $type = new LogicOperatorEnum();
        $this->assertSame('AND', LogicOperatorEnum::AND_OPERATOR);
        $this->assertSame('AND', $type(LogicOperatorEnum::AND_OPERATOR));
    }


    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetWritingProhibitedType() {
        $type = new LogicOperatorEnum();
        $this->assertSame('OR', LogicOperatorEnum::OR_OPERATOR);
        $this->assertSame('OR', $type(LogicOperatorEnum::OR_OPERATOR));
    }
}
