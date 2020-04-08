<?php
use PHPUnit\Framework\TestCase;

use Pes\Database\Handler\DbTypeEnum;

/**
 * Test Pes\Type\DbTypeEnum
 *
 * @author pes2704
 */
class DbTypeEnumTest extends TestCase {

    /**
     * existence konstanty MySQL
     * zda hodnota konstanty je enum typu
     */
    public function testGetMysqlType() {
        $type = new DbTypeEnum();
        $this->assertSame("mysql", DbTypeEnum::MySQL);
        $this->assertSame("mysql", $type(DbTypeEnum::MySQL));
    }

    /**
     * existence konstanty MSSQL
     * zda hodnota konstanty je enum typu
     */
    public function testGetMssqlType() {
        $type = new DbTypeEnum();
        $this->assertSame("sqlsrv", DbTypeEnum::MSSQL);
        $this->assertSame("sqlsrv", $type(DbTypeEnum::MSSQL));
    }
}
