<?php
use PHPUnit\Framework\TestCase;



/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use Pes\Database\Handler\ConnectionInfoInterface;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;

/**
 * Description of ConnectionInfoTest
 *
 * @author pes2704
 */
class ConnectionInfoTest extends TestCase {
    const DB_NAME = 'pes';
    const DB_HOST = 'localhost';
    const DB_PORT = '3306';
    const CHARSET_WINDOWS = 'cp1250';
    const COLLATION_WINDOWS = 'cp1250_czech_cs';
    const CHARSET_UTF8 = 'utf8';
    const COLLATION_UTF8 = 'utf8_czech_ci';
    const CHARSET_UTF8MB4 = 'utf8mb4';
    const COLLATION_UTF8MB4 = 'utf8mb4_czech_ci';

    const TESTOVACI_STRING = "Cyrilekoěščřžýáíéúů";

    public function testConnectionInfo() {
        $connectionInfo = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);
        $this->assertTrue($connectionInfo instanceof ConnectionInfoInterface, 'Nevytvořil se objekt ConnectionInfo.');
        $this->assertEquals(self::DB_NAME, $connectionInfo->getDbName(), 'Objekt $connectionInfo nevrací zadaný parametr.');
        $dbt = $connectionInfo->getDbType();
//        $this->assertEquals(DbTypeEnum::MySQL, $connectionInfo->getDbType(), 'Objekt $connectionInfo nevrací zadaný parametr.');
        $this->assertEquals(self::DB_HOST, $connectionInfo->getDbHost(), 'Objekt $connectionInfo nevrací zadaný parametr.');
        $this->assertEquals(self::CHARSET_UTF8, $connectionInfo->getCharset(), 'Objekt $connectionInfo nevrací zadaný parametr.');
        $this->assertEquals(self::COLLATION_UTF8, $connectionInfo->getCollation(), 'Objekt $connectionInfo nevrací zadaný parametr.');
        $this->assertEquals(self::DB_NAME, $connectionInfo->getDbName(), 'Objekt $connectionInfo nevrací zadaný parametr.');
        $this->assertEquals(self::DB_PORT, $connectionInfo->getDbPort(), 'Objekt $connectionInfo nevrací zadaný parametr.');
    }
}
