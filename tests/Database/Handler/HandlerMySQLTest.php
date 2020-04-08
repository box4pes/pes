<?php
use PHPUnit\Framework\TestCase;

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

use Pes\Logger\FileLogger;

class AttributesProviderForTest extends AttributesProvider {

    const BASE_STATEMENT_TYPE = 'StatementForTest';

    /**
     * @param \Pes\Database\Handler\Handler $handler Metoda využívá parametr handler
     * @return array
     */
    public function getAttributesArray(array $attributes=[]) {
        $attributes = parent::getAttributesArray();
        $attributes[\PDO::ATTR_STATEMENT_CLASS] = array(self::BASE_STATEMENT_TYPE,  array());  // Statement bez loggeru
        return $attributes;
    }
}

class StatementForTest extends \PDOStatement implements StatementInterface {
    private $logger;
    protected function __construct() {  //bez loggeru
        // konstruktor musí být deklarován i když je prázdný
        // bez toho nefunguje PDO::setAttribute(PDO::ATTR_STATEMENT_CLASS, ...
    }

    public function getInstanceInfo() {
        return 'instance objektu StatementForTest';
    }

    public function setLogger(\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }
}

/**
 * Description of HandlerMySQLTest
 *
 * @author pes2704
 */
class HandlerMySQLTest extends TestCase {

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
     *
     * @var FileLogger
     */
    private $logger;

    /**
     * Připraví db data.
     * @throws RuntimeException
     */
    public function setUp(): void {
        //fixture:
        //vymaže tabulku, zapíše tři řádky v UTF8
        $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME ;
        $dbh = new PDO($dsn, self::USER, self::PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        if (!$dbh) {
            throw new RuntimeException('Nevytvořil se db handler v setUp.');
        }
        $dbh->exec('DELETE FROM person');
        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (1, "Adam","Adamov")');
        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (2, "Božena","Boženová")');
        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (3, "Cyril","'.self::TESTOVACI_STRING.'")');

        // nastaví logger pro použití v testech
        $baseLogsDir="_logs/";
        $dir = 'LogsFromHandlerTests/';
        $file = get_called_class().'.log';
            // base
        FileLogger::setBaseLogsDirectory($baseLogsDir);
        $this->logger = FileLogger::getInstance($dir, $file, FileLogger::REWRITE_LOG);
    }

    /**
     * Testuje vytvoření objektu - bez options, loggeru, set attribute, identificator formatter, statement cache
     */
    public function testHandlerCreate() {
        //netestuji chybné user, pass
        //Chybné dbName, dbHost a charset způsobí výjimky PDOException.
        //Chybný dbPort se neprojeví nijak. Podle testů se zdá, že je úplně jedno jaká hodnota port
        //je zadána, dotaz jde vždy na 3306 (a na internetu jsou obdobné dotazy se stejným závěrem).

        //asserty bez nastavení kódování -> implicitně utf8
        // vytvoření handleru - bez options, set attribute, identificator formatter, statement cache
        $user = new Account(self::USER, self::PASS);
        $connectionInfoUtf8 = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);

        $dsnProvider = new DsnProviderMysql();
        $dsnProvider->setLogger($this->logger);
        $optionsProvider = new OptionsProviderMysql();
        $optionsProvider->setLogger($this->logger);

        $attributesProviderNull = new AttributesProviderNull();
        $attributesProviderNull->setLogger($this->logger);
        $dbh = new Handler($user, $connectionInfoUtf8, $dsnProvider, $optionsProvider, $attributesProviderNull, $this->logger);
        $this->assertTrue($dbh instanceof Handler, 'Nevytvořil se objekt Handler z dsn poskytnutého dsn providerem a zadanými user, pass.');
    }

    public function testStatementFromHandler() {
        $user = new Account(self::USER, self::PASS);
        $connectionInfoUtf8 = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_UTF8, self::COLLATION_UTF8, self::DB_PORT);

        $dsnProvider = new DsnProviderMysql();
        $dsnProvider->setLogger($this->logger);
        $optionsProvider = new OptionsProviderMysql();
        $optionsProvider->setLogger($this->logger);

        $attributesProviderNull = new AttributesProviderNull();
        $attributesProviderNull->setLogger($this->logger);
        $dbh = new Handler($user, $connectionInfoUtf8, $dsnProvider, $optionsProvider, $attributesProviderNull, $this->logger);

        // použití objektu pro čtení z testovací databáze - handler bez nastavení option vytváří PDOStatement
        $stmt = $dbh->query('SELECT name, surname FROM person');
        $this->assertNotFalse($stmt, 'Nevznikl žádný objekt z příkazu Handler->query.');
        $this->assertTrue($stmt instanceof \PDOStatement, 'Nevytvořil se objekt typu PDOStatement z Handler->query.');
        $arr = $stmt->fetchAll();
        $this->assertNotSame(FALSE, $stmt, 'Není pole resultset z PDOStatement->fetchAll.');
        $c= count($arr);
        $this->assertEquals(3, count($arr), 'Je resultset z BaseHandler->query, ale nemá 3 řádky.');
        //řádky číslovány od 0 ->třetí řádek
        $this->assertEquals(self::TESTOVACI_STRING, $arr[2]['surname'], 'Surname ve 3 řádku resultsetu neodpovídá textovavacímu stringu vloženému v setUp.');
        }

    public function testHadlerWithWindowsCoding() {
        $user = new Account(self::USER, self::PASS);

        //assert s nastavením kódování Windows
        $connectionInfoWin = new ConnectionInfo(DbTypeEnum::MySQL, self::DB_HOST, self::DB_NAME, self::CHARSET_WINDOWS, self::COLLATION_WINDOWS, self::DB_PORT);
        // options provider už nestačí null - nastavuje charset a collate
        $dsnProvider = new DsnProviderMysql();
        $dsnProvider->setLogger($this->logger);
        // (attributes provider pro MySQL stačí null, ale např. v MSSQL se charset nastavuje až pomocí atributů podle AttributeProvider)
        $attributesProviderNull = new AttributesProviderNull();
        $attributesProviderNull->setLogger($this->logger);
        $optionsProviderMysql = new OptionsProviderMysql();
        $optionsProviderMysql->setLogger($this->logger);
        $dbh = new Handler($user, $connectionInfoWin, $dsnProvider, $optionsProviderMysql, $attributesProviderNull, $this->logger);
        $arrWin = $dbh->query('SELECT name, surname FROM person')->fetchAll();
        //řádky číslovány od 0 ->třetí řádek
        $testStringCP1250 = iconv("UTF-8", "Windows-1250", self::TESTOVACI_STRING);
        $this->assertEquals($testStringCP1250, $arrWin[2]['surname'],
                'Pří čtení záznamu v db zapsaného v utf8 a přečteného s kódováním cp1250 (windows)'
                . ' neodpovídá přečtené Surname ve 3 řádku resultsetu testovacímu stringu převedenému do cp1250.');

    }
}