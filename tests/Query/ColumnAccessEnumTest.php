<?php
use PHPUnit\Framework\TestCase;

use Pes\Query\ColumnAccessEnum;

/**
 * Test Pes\Type\ColumnAccessEnum
 *
 * @author pes2704
 */
class ColumnAccessEnumTest extends TestCase {

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetDefaultAccessType() {
        $type = new ColumnAccessEnum();
        $this->assertSame('default_access', ColumnAccessEnum::DEFAULT_ACCESS);
        $this->assertSame('default_access', $type(ColumnAccessEnum::DEFAULT_ACCESS));
    }


    /**
     * existence konstanty
     * zda hodnota konstanty je enum typuype
     */
    public function testGetWritingProhibitedType() {
        $type = new ColumnAccessEnum();
        $this->assertSame('writing_prohibited', ColumnAccessEnum::WRITING_PROHIBITED);
        $this->assertSame('writing_prohibited', $type(ColumnAccessEnum::WRITING_PROHIBITED));
    }


    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetUpdateProhibitedType() {
        $type = new ColumnAccessEnum();
        $this->assertSame('update_prohibited', ColumnAccessEnum::UPDATE_PROHIBITED);
        $this->assertSame('update_prohibited', $type(ColumnAccessEnum::UPDATE_PROHIBITED));
    }


    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetAlwaysWriteableType() {
        $type = new ColumnAccessEnum();
        $this->assertSame('always_writeable', ColumnAccessEnum::ALWAYS_WRITEABLE);
        $this->assertSame('always_writeable', $type(ColumnAccessEnum::ALWAYS_WRITEABLE));
    }
}
