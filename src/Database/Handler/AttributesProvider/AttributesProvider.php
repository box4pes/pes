<?php
namespace Pes\Database\Handler\AttributesProvider;

use Pes\Database\Handler\ConnectionInfo;
use Psr\Log\LoggerInterface;

// TOTO JE DEFINICE TŘÍDY STATEMENT OBJEKTU, KTERÝ JE VYTVÁŘEN METODOU HANDLERU ->prepare()
use Pes\Database\Statement\Statement;

/**
 * AttributesProviderDefault poskytuje základní nastavení, které očekávají ostatní části frameworku.
 *
 * @author pes2704
 */
class AttributesProvider extends AttributesProviderAbstract {

    /**
     * Připraví nastavení pro handler (potomek PDO) takto:
     * <ul>
     * <li>Při chybě vyhazuj výjimky</li>
     * <li>Metodami query() a prepare() vracej VLASTNÍ TYP objektu statement (Pes\Database\Statement\Statement).
     * Typ je dán deklarací use Statement v definici této třídy.</li>
     * <li>Nastav logger pro Statement. Třída očekává, že nastavený objekt Statement přijímá v konstruktoru logger a nastaví handler tak, aby při query() a prepare()
     * předával vytvářeným objektům logger, který handler dostal v konstruktoru.</li>
     * <li>Pokoušej se používat nativní poporu prepare statements poskytovanou driverem
     *   <ul>
     *   <li> nativní PDO prepare má výhodu v ochraně proti sql injection</li>
     *   <li> nativní PDO prepare má výkonostní výhodu</li>
     *   <ul></li>
     * </ul>
     * <p>
     * Doporučení:</p><p>
     * PDO se někdy chová nedokumentovaným způsobem. Např. PDO::MYSQL_ATTR_FOUND_ROWS nelze nastavovat pdo->setAttribute(\PDO::MYSQL_ATTR_FOUND_ROWS, true);
     * metoda vždy vrací FALSE a nic se nenastaví. Proto doporučuji nastavovat všechny atributy specifické pro daný driver jako options v příslušném objektu
     * XxxOptionsProvider. Například atributy s konstantami začínajícími prefixem MYSQL..., tedy atributy specifcké pro mysql driver v objektu MysqlOptionsProvider
     * jako options (nikoli po instancování PDO jako setAttributes). To funguje.
     */
    public function __construct() {

        // při chybě vyhazuj výjimky
        $this->attributes[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;

        // vracej VLASTNÍ TYP objektu statement zadaného typu - default typ zadaný konstantou
        $this->attributes[\PDO::ATTR_STATEMENT_CLASS] = array(Statement::class, array($this->logger));

        //pokoušej se používat nativní poporu preparu poskytovanou driverem
        // používej nativní PDO prepare - viz http://dev.mysql.com/doc/refman/5.6/en/sql-syntax-prepared-statements.html
        // má výhodu v ochraně proti sql injection viz - http://stackoverflow.com/questions/134099/are-pdo-prepared-statements-sufficient-to-prevent-sql-injection?rq=1 odpověď ircmaxell
        // má výkonostní výhodu
        $this->attributes[\PDO::ATTR_EMULATE_PREPARES] = FALSE;

        // ???? (PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL) - viz sERGE-01 http://php.net/manual/en/pdostatement.rowcount.php#113608
    }

}
