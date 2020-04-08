<?php
use PHPUnit\Framework\TestCase;


use Pes\Database\Handler\DsnProvider\DsnProviderMssql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderNull;
use Pes\Database\Handler\AttributesSetter;
use Pes\Database\Handler\AttributesProvider\AttributesProviderMssql;
use Pes\Database\Handler\IdentificatorFormatter\IdentificatorFormatterMssql;
use Pes\Database\Handler\Handler;

use Pes\Database\Statement\StatementInterface;

use Psr\Log\NullLogger;

/**
 * Description of HandlerTest
 *
 * @author pes2704
 */
class HandlerMSSQLTest extends TestCase {
    public function testIncomplete()
    {    // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
//    const DB_NAME = 'pes';
//    const DB_HOST = '(local)';//'localhost';
//    const DB_PORT = 1433;
//    const CHARSET_WINDOWS = 'cp1250';
//    const TESTOVACI_STRING = "Cyrilekoěščřžýáíéúů";
//    
//    const NICK = 'tester';
//    const USER = 'pes_tester';
//    const PASS = 'pes_tester';
//    
//    public function setUp() {
//        //fiture:
//        //vymaaže tabulku, zapíše tři řádky v UTF8
//        $dsn = 'sqlsrv:server=' . self::DB_HOST . ';Database=' . self::DB_NAME; 
//        $dbh = new PDO($dsn, self::USER, self::USER);
//        $dbh->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_UTF8);
//        if (!$dbh) {
//            throw new RuntimeException('Nevytvořil se db handler v setUp.');
//        }
//        $dbh->exec('DELETE FROM person');
//        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (1, "Adam","Adamov")');
//        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (2, "Božena","Boženová")');
//        $dbh->exec('INSERT INTO person (number, name, surname) VALUES (3, "Cyril","'.self::TESTOVACI_STRING.'")');
//    }
    
//This works to get UTF8 data from MSSQL:
//$db = new PDO('dblib:host=your_hostname;dbname=your_db;charset=UTF-8', $user, $pass);
    
//    public function testMSSqlDsnProvider() {
//        $dsnProvider = new DsnProviderMssql(self::DB_NAME, self::DB_HOST);
//        $this->assertTrue($dsnProvider instanceof DsnProviderMssql, 'Nevytvořil se objekt dsn provider.');
//        $this->assertEquals(self::DB_HOST, $dsnProvider->getDbHost(), 'Objekt dsn provider nevrací zadaný parametr.');
//        $this->assertEquals(self::DB_NAME, $dsnProvider->getDbName(), 'Objekt dsn provider nevrací zadaný parametr.');
//        $dsn = $dsnProvider->getDsn();
//        $dbh = new PDO($dsnProvider->getDsn(), self::USER, self::PASS);
//        $this->assertTrue($dbh instanceof \PDO, 'Nevytvořil se objekt PDO z dsn poskytnutého dsn providerem.');
//
//        $dsnProvider = new DsnProviderMssql(self::DB_NAME, self::DB_HOST, self::CHARSET_WINDOWS, self::DB_PORT);
//        $this->assertTrue($dsnProvider instanceof DsnProviderMysql);
//        $this->assertEquals(self::DB_HOST, $dsnProvider->getDbHost(), 'Objekt dsn provider nevrací zadaný parametr.');
//        $this->assertEquals(self::DB_NAME, $dsnProvider->getDbName(), 'Objekt dsn provider nevrací zadaný parametr.');
//        $this->assertEquals(self::CHARSET_WINDOWS, $dsnProvider->getCharset(), 'Objekt dsn provider nevrací zadaný parametr.');
//        $this->assertEquals(self::DB_PORT, $dsnProvider->getDbPort(), 'Objekt dsn provider nevrací zadaný parametr.');
//        $dbh = new PDO($dsnProvider->getDsn(), self::USER, self::PASS);
//        $dbh->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_UTF8);
//        $this->assertTrue($dbh instanceof \PDO, 'Nevytvořil se objekt PDO z dsn poskytnutého dsn providerem.');
//    }
    
    
//    public function testHandlerForMssql() {
//        //netestuji chybné user, pass - ty se předávají přímo do PDO konstruktoru, tak to snad nikdy nerozbiju
//        //Chybné dbName, dbHost a charset způsobí výjimky PDOException. 
//        //Chybný dbPort se neprojeví nijak. Podle testů se zdá, že je úplně jedno jaká hodnota port 
//        //je zadána, dotaz jde vždy na 3306 (a na internetu jsou obdobné dotazy se stejným závěrem).
//        
//        //asserty bez nastavení kódování -> implicitně utf8
//        // vytvoření objektu - bez otions, set attribute, identificator formatter
//        $dsnProvider = new DsnProviderMssql(self::DB_NAME, self::DB_HOST);
//        $optionsProvider = new OptionsProviderNull();
//        $logger = new NullLogger();
//        $dbh = new Handler(self::NICK, $dsnProvider, self::USER, self::PASS, $optionsProvider, $logger);        
//        $this->assertTrue($dbh instanceof Handler, 'Nevytvořil se objekt BaseHandler z dsn poskytnutého dsn providerem a zadanými user, pass.');
//        // metody
//        $this->assertEquals(self::USER, $dbh->getUser(), 'Handler nevrací zadaný parament user.');
//        $this->assertSame($dsnProvider, $dbh->getDsnProvider(), 'Handler nevrací zadaný parament dsn provider.');
//        $this->assertEquals("nazdar", $dbh->getFormattedIdentificator('nazdar'), 'Handler je bez formatteru a nevrací správně nezměněný identifikátor.'); 
//
//        // použití objektu pro čtení z testovací databáze - používá vytvoření PDOStatement
//        $stmt = $dbh->query('SELECT name, surname FROM person');
//        $this->assertNotFalse($stmt, 'Není statement z BaseHandler->query.');
//        $this->assertTrue($stmt instanceof \PDOStatement, 'Nevytvořil se objekt typu PDOStatement z Handler->query.');
//        $arr = $stmt->fetchAll();
//        $this->assertNotSame(FALSE, $stmt, 'Není pole resultset z PDOStatement->fetchAll.');
//        $c= count($arr);
//        $this->assertEquals(3, count($arr), 'Je resultset z BaseHandler->query, ale namá 3 řádky.');
//        //řádky číslovány od 0 ->třetí řádek
//        $this->assertEquals(self::TESTOVACI_STRING, $arr[2]['surname'], 'Surname ve 3 řádku resultsetu neodpovídá textovavacímu stringu vloženému v setUp.');
//
//        //assert s nastavením kódování Windows
//        $dsnProvider = new DsnProviderMssql(self::DB_NAME, self::DB_HOST, self::CHARSET_WINDOWS);
//        $optionsProvider = new OptionsProviderNull();
//        $logger = new NullLogger();
//        $dbh = new Handler(self::NICK, $dsnProvider, self::USER, self::PASS, $optionsProvider, $logger);
//        $arr = $dbh->query('SELECT name, surname FROM person')->fetchAll();
//        //řádky číslovány od 0 ->třetí řádek
//        $testStringCP1250 = iconv("UTF-8", "Windows-1250", self::TESTOVACI_STRING);  
//        $this->assertEquals($testStringCP1250, $arr[2]['surname'], 
//                'Pří čtení záznamu v db zapsaného v utf8 s nastavenním dsn provedera na kódování cp1250 (windows)'
//                . ' neodpovídá přečtené Surname ve 3 řádku resultsetu textovavacímu stringu vloženému v setUp převedenému také do cp1250.');
//
//    }
//    
//    public function testMssqlOptionProvider() {
//        $dsnProvider = new DsnProviderMssql(self::DB_NAME, self::DB_HOST);
//        $optionsProvider = new OptionsProviderNull();
//        $logger = new NullLogger();
//        // kontrolní UPDATE bez nastavení otions provideru
//        $dbh = new Handler(self::NICK, $dsnProvider, self::USER, self::PASS, $optionsProvider, $logger);        
//        $this->assertTrue($dbh instanceof Handler, 'Nevytvořil se objekt BaseHandler z dsn poskytnutého dsn providerem (a zadanými parametry).');
//        $stmt = $dbh->query('UPDATE person SET name="Cyril", surname="'.self::TESTOVACI_STRING.'" WHERE name="Cyril"');
//        $rCount = $stmt->rowCount();
//        $this->assertEquals(0, $rCount, 
//                'UPDATE řádku stejnými hodnotami jako již v řádku jsou, bez nastavení parametrů handleru objektem attributes setter vrací nenulovou hodnotu.'
//                . ' Buď se změnila funčnost handleru nebo není v db správně fixture s hodnotami setUp() metody.');
//        // teď test options provider
//        $optionsProvider = new MysqlOptionsProvider();
//        $dbh = new Handler(self::NICK, $dsnProvider, self::USER, self::PASS, $optionsProvider, $logger);
//        $dbh->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_UTF8);        
//        $stmt = $dbh->query('UPDATE person SET name="Cyril", surname="'.self::TESTOVACI_STRING.'" WHERE name="Cyril"');
//        $rCount = $stmt->rowCount();
//        $this->assertEquals(1, $rCount, 
//                'UPDATE řádku stejnými hodnotami jako již v řádku jsou, s nastavením parametrů handleru objektem attributes setter vrací hodnotu jinou než 1.'
//                . ' Buď se změnila funčnost handleru nebo není v db správně fixture s hodnotami setUp() metody.');        
//        
//        $this->assertTrue($stmt instanceof \PDOStatement, 'Nevytvořil se objekt typu PDOStatement z Handler->query.');
//        $arr = $stmt->fetchAll();        
//    }
//    
//    public function testBaseAttributesSetter() {
//        // testuji poue nastevení návratového objektu statement. Netestuji vyhazování výjimak místo chyb a netestuji jestli MySQL skuečně používá prepares statemnty.
//        $dsnProvider = new DsnProviderMssql(self::DB_NAME, self::DB_HOST);
//        $optionsProvider = new MysqlOptionsProvider();
//        //bez nastavení typu návratového objektu statement
//        $setter = new BaseAttributesSetter();
//        $dbh = new Handler(self::NICK, $dsnProvider, self::USER, self::PASS, $optionsProvider, $setter);
//        $stmt = $dbh->query('SELECT name, surname FROM person');
//        $this->assertNotFalse($stmt, 'Není statement z BaseHandler->query.'); 
//        $this->assertEquals('PDOStatement', get_class($stmt), 'Objekt statement vytvořený handlerem bez nastaveného typu objektu statement není stdClass. Je '.get_class($stmt).'.');
//        //s nastavením typu návratového objektu statement
//        $setter = new BaseAttributesSetter('Pes\Database\Statement\Statement');
//        $dbh = new Handler(self::NICK, $dsnProvider, self::USER, self::PASS, $optionsProvider, $setter);
//        $stmt = $dbh->query('SELECT name, surname FROM person');
//        $this->assertNotFalse($stmt, 'Není statement z BaseHandler->query.'); 
//        $this->assertEquals('Pes\Database\Statement\Statement', get_class($stmt), 'Objekt statement vytvořený handlerem není nastaveného typu. Je '.get_class($stmt).'.');
//    }
//    
//    public function testMssqlIdentificatorFormatter() {
//        $dsnProvider = new DsnProviderMssql(self::DB_NAME, self::DB_HOST);
//        $formatter = new IdentificatorFormatterMssql();
//
//        $dbh = new Handler(self::NICK, $dsnProvider, self::USER, self::PASS, NULL, NULL, $formatter);
//        $this->assertEquals("[nazdar]", $dbh->getFormattedIdentificator('nazdar'), 'Handler nevrací správně zformátovaný identifikátor.'); 
//        $this->assertEquals("[kvok]]kvak]", $dbh->getFormattedIdentificator("kvok]kvak"), 'Handler nevrací správně zformátovaný identifikátor.');        
//    }
}
