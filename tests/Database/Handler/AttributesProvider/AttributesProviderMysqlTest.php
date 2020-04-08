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

use Psr\Log\NullLogger;

// TOTO JE TŘÍDA STATEMENT OBJEKTU, KTERÝ JE VYTVÁŘEN METODOU HANDLERU ->prepare()
use Pes\Database\Statement\Statement;

/**
 * Description of DnProviderTest
 *
 * @author pes2704
 */
class AttributesProviderMysqlTest extends TestCase {
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


    public function testAttributesProviderMysql() {
        // testuji pouze nastavení návratového objektu statement. Netestuji vyhazování výjimak místo chyb a netestuji jestli MySQL skuečně používá prepares statementy.
        $user = new Account(self::USER, self::PASS);
        $connectionInfoUtf8 = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);
        $dsnProvider = new DsnProviderMysql();
        $optionsProvider = new OptionsProviderMysql();
        $logger = new NullLogger();

        //test vrcení BASE_STATEMENT_TYPE místo PDO
        //set s použitím AttributesProviderDefaultt - měl by vracet default typ AttributesProviderDefault::BASE_STATEMENT_TYPE, t.j. 'Pes\Database\Statement\Statement'
        $attributesProviderDefault = new AttributesProvider($logger);
        $dbh = new Handler($user, $connectionInfoUtf8, $dsnProvider, $optionsProvider, $attributesProviderDefault, $logger);
        $stmt = $dbh->query('SELECT name, surname FROM person');
        $this->assertNotFalse($stmt, 'Není statement z Handler->query.');
        $this->assertEquals(Statement::class, get_class($stmt), 'Objekt statement vytvořený handlerem není '
                .Statement::class.'. Je '.get_class($stmt).'.');
    }

}
