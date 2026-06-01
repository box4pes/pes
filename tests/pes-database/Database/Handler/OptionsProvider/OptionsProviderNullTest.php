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

use Pes\Database\Handler\Account;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderNull;
use Pes\Database\Handler\AttributesProvider\AttributesProviderNull;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;

use Pes\Database\Statement\StatementInterface;

use Psr\Log\NullLogger;

/**
 * Description of DnProviderTest
 *
 * @author pes2704
 */
class OptionsProviderNullTest extends TestCase {
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

    /**
     * Připraví db data.
     * @throws RuntimeException
     */
    public function setUp(): void {
        //fixture:
        //vymaaže tabulku, zapíše tři řádky v UTF8
        $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME ;
        $dbh = new PDO($dsn, self::USER, self::PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        if (!$dbh) {
            throw new RuntimeException('Nevytvořil se db handler v setUp.');
        }
        $dbh->exec('DELETE FROM person');
        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (1, "Adam","Adamov")');
        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (2, "Božena","Boženová")');
        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (3, "Cyril","'.self::TESTOVACI_STRING.'")');
        $read3 = $dbh->query('SELECT number, name, surname FROM person WHERE number=3')->fetchAll();
        $a=1;
        }

    public function testMysqlOptionProvider() {
        $user = new Account(self::USER, self::PASS);
        $connectionInfoUtf8 = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);
        $dsnProvider = new DsnProviderMysql();
//        $optionsProvider = new OptionsProviderMysql();
        $optionsProvider = new OptionsProviderNull();
        $logger = new NullLogger();
        $attributesProviderNull = new AttributesProviderNull($logger);
        // kontrolní UPDATE bez nastavení options provideru
        $dbh = new Handler($user, $connectionInfoUtf8, $dsnProvider, $optionsProvider, $attributesProviderNull, $logger);
        $this->assertTrue($dbh instanceof Handler, 'Nevytvořil se objekt Handler z dsn poskytnutého dsn providerem (a zadanými parametry).');
        $stmt = $dbh->query('UPDATE person SET surname="'.self::TESTOVACI_STRING.'" WHERE name="Cyril"');
        $this->assertTrue($stmt instanceof \PDOStatement, 'Nevytvořil se objekt typu PDOStatement z Handler->query.');
        $rCount = $stmt->rowCount();
        $stmt = $dbh->query('UPDATE person SET surname="'.self::TESTOVACI_STRING.'" WHERE name="Cyril"');
        $rCount = $stmt->rowCount();
        //  Buď se změnila funkčnost handleru nebo není v db správně fixture s hodnotami setUp() metody
        $this->assertEquals(0, $rCount,'UPDATE řádku stejnými hodnotami jako již v řádku jsou, bez nastavení atributů handleru vrací nenulovou hodnotu ('.$rCount.').');
    }

}
