<?php
use PHPUnit\Framework\TestCase;

use Pes\Query\OrderingEnum;

/**
 * Test Pes\Type\ColumnAccessEnum
 *
 * @author pes2704
 */
class OrderingEnumTest extends TestCase {

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetAscendingOrderingType() {
        $type = new OrderingEnum();
        $this->assertSame('ASC', OrderingEnum::ASCENDING);
        $this->assertSame('DESC', $type(OrderingEnum::DESCENDING));
    }


    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetDecsendingOrderingType() {
        $type = new OrderingEnum();
        $this->assertSame('DESC', OrderingEnum::DESCENDING);
        $this->assertSame('DESC', $type(OrderingEnum::DESCENDING));
    }
}
