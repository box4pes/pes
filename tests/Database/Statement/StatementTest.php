<?php
use PHPUnit\Framework\TestCase;

use Pes\Database\Handler\Account;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;

use Psr\Log\NullLogger;
use Pes\Logger\FileLogger;

/**
 * Model
 */
class Person {
    public $number;
    public $name;
    public $surname;
}

/**
 * Description of StatementTest
 *
 * @author pes2704
 */
class StatementTest extends TestCase {
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

    public function setUp(): void {
        //fixture:
        //vymaaže tabulku, zapíše tři řádky v UTF8
        $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME ;
        $dbh = new PDO($dsn, self::USER, self::PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        $dbh->exec('DELETE FROM person');
        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (1, "Adam","Adamov")');
        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (2, "Božena","Boženová")');
        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (3, "Cyril","'.self::TESTOVACI_STRING.'")');

//        $this->connectionInfoUtf8 = new ConnectionInfo(self::NICK, DbTypeEnum::MySQL, self::DB_HOST, self::USER, self::PASS, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);
//        $this->dsnProvider = new DsnProviderMysql();
//        $this->optionsProvider = new OptionsProviderMysql();
//        $this->logger = new NullLogger();
//        $this->attributesProviderDefault = new AttributesProviderDefault($this->logger);

        $baseLogsDir="_logs/";
        FileLogger::setBaseLogsDirectory($baseLogsDir);
    }

    /**
     * Testuje Statement s různým fetch mode - jen s SQL SELECT
     */
    public function testStatement() {
        $user = new Account(self::USER, self::PASS);
        $connectionInfoUtf8 = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);
        $dsnProvider = new DsnProviderMysql();
        $optionsProvider = new OptionsProviderMysql();
        $logger = new NullLogger();
        $attributesProviderDefault = new AttributesProvider($logger);
        $dbh = new Handler($user, $connectionInfoUtf8, $dsnProvider, $optionsProvider, $attributesProviderDefault, $logger);
        // čtu 2 sloupce -> fetch() (default \PDO::FETCH_BOTH) vrací pole se 4. položkami s \PDO::FETCH_ASSOC pole s 2. položkami
        $stmt = $dbh->query('SELECT name, surname FROM person');
        $this->assertNotFalse($stmt, 'Není statement z Handler->query.');
        // 1. řádek - Adam - bez fetch mode (default \PDO::FETCH_BOTH)
        $res1 = $stmt->fetch();
        $this->assertTrue(is_array($res1) AND count($res1)==4, 'Fetch bez nastavení fetch mode nevrátil pole nebo pele nemá 4 položky.');
        // 2. řádek Božena - fetch mode \PDO::FETCH_ASSOC
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $res2 = $stmt->fetch();
        $this->assertTrue(is_array($res2) AND count($res2)==2, 'Fetch s nastavením fetch mode PDO::FETCH_ASSOC nevrátil pole nebo pole nemá 2 položky.');
        // 3. řádek Cyril - fetch mode \PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, 'Person'
        $stmt->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, 'Person');
        $res3 = $stmt->fetch();
        $this->assertEquals('Person', get_class($res3), 'Objekt vytvořený fetch není nastaveného typu Person. Je '.get_class($res3).'.');
    }

    // test jen pro rychlost handleru - potřebné závislosti se vytváření v SetUp()
//    public function testStatementJenHandler() {
//        $connectionInfoUtf8 = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::USER, self::PASS, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);
//        $dsnProvider = new DsnProviderMysql();
//        $optionsProvider = new OptionsProviderMysql();
//        $logger = new NullLogger();
//        $attributesProviderDefault = new AttributesProviderDefault();
//        $dbh = new Handler($this->connectionInfoUtf8, $this->dsnProvider, $this->optionsProvider, $this->attributesProviderDefault, $logger);
//    }

    public function testStatementWithFileLogger() {
        $dir = 'LogsFromStatementTests/';
        $file = get_called_class().'.log';
        $logger = FileLogger::getInstance($dir, $file, FileLogger::REWRITE_LOG);

        $user = new Account(self::USER, self::PASS);
        $connectionInfoUtf8 = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);
        $dsnProvider = new DsnProviderMysql($logger);
        $optionsProvider = new OptionsProviderMysql($logger);
        $attributesProviderDefault = new AttributesProvider($logger);
        $dbh = new Handler($user, $connectionInfoUtf8, $dsnProvider, $optionsProvider, $attributesProviderDefault, $logger);

        for ($i = 1; $i <= 3; $i++) {
            $prestmt = $dbh->prepare('SELECT name, surname FROM person WHERE number=:number');
            $prestmt->bindParam(':number', $i);
            $prestmt->execute();
            $prestmt->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, 'Person');
            $res[] = $prestmt->fetch();
        }
        $this->assertEquals(3, count($res));     //  aby byl aspoň nějaký assert
    }
}
