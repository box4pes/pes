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

use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;

/**
 * Description of DnProviderTest
 *
 * @author pes2704
 */
class DsnProviderMysqlTest extends TestCase {
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

    const USER = 'pes_tester';
    const PASS = 'pes_tester';

    public function testDsnProviderMysql() {

        $connectionInfos['bez dbName, charset, collation a dbPort'] = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST );
        $connectionInfos['bez dbName a dbPort'] = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8);
        $connectionInfos['s dbName, bez charset, collation a dbPort'] = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME);
        $connectionInfos['s dbName, charset, collation a bez dbPort'] = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8);
        $connectionInfos['se všemi parametry'] = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);

        foreach ($connectionInfos as $key => $connectionInfo) {
            $dsnProvider = new DsnProviderMysql();
            $this->assertTrue($dsnProvider instanceof DsnProviderMysql, 'Nevytvořil se objekt dsn provider.');
            $this->assertTrue(is_string($dsnProvider->getDsn($connectionInfo)), 'Metoda nevrací řetězec.');
            $dbh = new PDO($dsnProvider->getDsn($connectionInfo), self::USER, self::PASS);
            $this->assertTrue($dbh instanceof \PDO, 'Nevytvořil se objekt PDO z dsn poskytnutého dsn providerem a zadanými parametry: '.$key.'.');
        }

    }

}
