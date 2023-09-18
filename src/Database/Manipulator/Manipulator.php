<?php
namespace Pes\Database\Manipulator;

use Pes\Database\Handler\HandlerInterface;
use Pes\Database\Statement\StatementInterface;

use Psr\Log\LoggerInterface;

use PDOException;
use LogicException;
use UnexpectedValueException;
use Pes\Database\Manipulator\Exception\ErrorRollbackException;
use Pes\Database\Manipulator\Exception\ForcedRollbackException;

/**
 * Description of Convertor
 *
 * @author pes2704
 */
class Manipulator {

    /**
     * @var HandlerInterface
     */
    private $handler;
    private $logger;

    public function __construct(HandlerInterface $handler, LoggerInterface $logger) {
        $this->handler = $handler;
        $this->logger = $logger;
    }

    /**
     *
     * @return HandlerInterface
     */
    public function getHandler(): HandlerInterface {
        return $this->handler;
    }

    /**
     * Vytvoří kopii databázové tabulky. Pokud neexistuje zdrojová tabulka nebo existuje cílová tabulka již před kopírováním vyhodí výjimku.
     * Do nové tabulky kopíruje strukturu, data, indexy a triggery.
     *
     * @param string $oldTableName
     * @param string $newTableName
     * @return bool TRUE, pokud kopírování skončilo úspěšně, jinak FALSE.
     * @throws UnexpectedValueException Pokud neexistuje zdrojová tabulka
     * @throws LogicException Již existuje cílová tabulka kopírování.
     * @throws ErrorRollbackException
     */
    public function copyTable(string $oldTableName, string $newTableName) {

        // https://stackoverflow.com/questions/3280006/duplicating-a-mysql-table-indexes-and-data
        //To copy with indexes and triggers do these 2 queries:
        //CREATE TABLE newtable LIKE oldtable;
        //INSERT newtable SELECT * FROM oldtable;
        //
        //To copy just structure and data use this one:
        //CREATE TABLE tbl_new AS SELECT * FROM tbl_old;

        if (!$this->tableExists($oldTableName)) {
            throw new UnexpectedValueException("Zdrojová tabulka kopírování '$oldTableName' neexistuje. Nevím co mám dělat a tak jsem se zhroutil.");
        }
        if ($this->tableExists($newTableName)) {
            throw new LogicException("Zakázané nebezpečné chování - cílová tabulka kopírování '$newTableName'již existuje. Nelze přepsat existující tabulku.");
        }

        $dbhTransact = $this->handler;
        try {
            $dbhTransact->beginTransaction();
//            $this->logger->info("CREATE TABLE $newTableName LIKE $oldTableName");
            $dbhTransact->exec("CREATE TABLE $newTableName LIKE $oldTableName");
//            $this->logger->info("INSERT $newTableName SELECT * FROM $oldTableName");
            $dbhTransact->exec("INSERT $newTableName SELECT * FROM $oldTableName");
//            $this->logger->info('Commit.');
            $succ = $dbhTransact->commit();
        } catch(PDOException $e) {
//            $this->logger->error('Rollback: '.$e->getMessage());
            $dbhTransact->rollBack();
            throw new ErrorRollbackException($e->getMessage(), 0, $e);
        }
        return $succ ? TRUE : FALSE;
    }

    /**
     *
     * @param string $tableName
     * @return bool TRUE, pokud tabulka existuje a lze z ní číst, jinak FALSE.
     * @throws UnexpectedValueException
     */
    public function tableExists($tableName) {
        $dbh = $this->handler;
        $nameChunks = explode(".", $tableName);
        switch (count($nameChunks)) {
            case 1:
                $dbName = $dbh->getSchemaName();  // musím udělat proměnnou - předává se do bindParam referencí
                break;
            case 2:
                $dbName = $nameChunks[0];
                $tableName = $nameChunks[1];
                break;
            default:
                throw new UnexpectedValueException("Zadané jméno tabulky musí být ve tvaru jednoho slova nebo dvou slov spojených tečkou. Jméno $tableName neumím zpracovat.");
        }

        $stmt = $dbh->prepare(
            "SELECT table_name
            FROM information_schema.TABLES
            WHERE (TABLE_SCHEMA = :db_name) AND (TABLE_NAME = :table_name)"
        );
        $stmt->bindParam(':db_name', $dbName, \PDO::PARAM_STR);
        $stmt->bindParam(':table_name', $tableName, \PDO::PARAM_STR);

        $stmt->execute();
        $q =  $stmt->fetch(\PDO::FETCH_ASSOC); 
        return is_array($q) ? TRUE : FALSE;
    }

    /**
     * Vykoná obsah zadaného řetězce jako posloupnost SQL příkazů v jedné transakci. Jednotlivé SQL příkazy vykoná pomocé metody PDO->exec(), 
     * která jako návratovou hodnotu vrací bool. Metoda tak nevrací žádný výsledek jen informuje o úspěchu.
     * Lze zadat parametr rollback, který vynutí, že se po provedení příkazů v trasakci nikdy neprovede commit, vždy se volá rollback. 
     * Tím není ovlivněno chování v průběhu trasakce, pokud dojde v průběhu transakce k chybě, proběhne samozřejmě rollback zcela standartně.
     *
     * Předpokládá, že SQL příkazy v souboru jsou odděleny středníkem ";".
     * Příkazy vykonává v rámci jedné transakce, kterou spouští.
     * Při pokusu o volání metody uprostřed již spuštěné transakce by vykonání neznámé posloupnosti SQL příkazů mohlo vést
     * k nepředvídaným výsledkům. Proto v případě pokusu o volání metody uprostřed již spuštěné transakce metoda vyhodí výjimku.
     *
     * @param string $sql Řetězec s posloupností SQL příkazů oddělených středníky.
     * @param bool $rollback Pokud je true, pak se po provedení příkazů v trasakci nikdy neprovede commit, vždy se volá rollback.
     * @return bool TRUE, pokud transakce skončila úspěšně, jinak FALSE.
     * @throws LogicException Pokud nelze přečíst zadaný soubor. Při volání uprostřed již spuštěné transakce.
     * @throws ErrorRollbackException Výjimka při vykonávání transakce.
     * @throws ForcedRollbackException Rollbak byl vynucen parametrem rollback.
     */
    public function exec($sql, $rollback=false) {
        if (!$sql) {
            throw new LogicException('Zadaný soubor je prázdný.');
        }
        $queries = $this->mysql_explode($sql);
        $dbhTransact = $this->handler;
        if ($dbhTransact->inTransaction()) {
            throw new LogicException('Nelze volat tuto metodu exec() uprostřed spuštěné databázové transakce.');
        }
        try {
            $dbhTransact->beginTransaction();
            foreach ($queries as $query) {
                if (trim($query)) {
//                    $this->logger->info($query);
                    $dbhTransact->exec($query);
                }
            }
            if ($rollback) {
//            $this->logger->info('Forced rollback: '.$e->getMessage());
                $succ = $dbhTransact->rollBack();
                throw new ForcedRollbackException("Proveden přikázaný rollback transakce.");
                
            } else {
//                $this->logger->info('Commit.');
                $succ = $dbhTransact->commit();
            }
        } catch(PDOException $e) {
            $handlerLogger = $this->handler->getLogger();
            if (isset($handlerLogger)) {
                // přidá do logu handleru message z výjimky, pokud nastala při volání metody handleru
                $handlerLogger->error("Vyhozena PDOException: {$e->getMessage()}");
            }
//            $this->logger->error('Rollback: '.$e->getMessage());
            $dbhTransact->rollBack();
            throw new ErrorRollbackException($e->getMessage(), 0, $e);
        }
        return $succ ? TRUE : FALSE;
    }

    /**
     * Metoda očekává string obsahující jeden sql příkaz. Tento příkaz provede pomocí PDO->query() a vrací objekt StatementInterface.
     * Pokud provedení selže, vrací null (nikoli false).
     *
     *
     * @param string $sql
     * @return StatementInterface|null
     * @throws LogicException
     * @throws \RuntimeException
     */
    /**
     * 
     * @param type $sql
     * @return StatementInterface|null
     * @throws LogicException Zadaný SQL řetězec je prázdný. Nelze volat tuto metodu pro provedení více než jednoho příkazu SQL.
     * @throws ErrorRollbackException Výjimka při vykonávání transakce.
     */
    public function query($sql): ?StatementInterface {
        if (!$sql) {
            throw new LogicException('Zadaný SQL řetězec je prázdný.');
        }
        $queries = $this->mysql_explode($sql);
        if (count($queries)>1) {
            throw new LogicException('Nelze volat tuto metodu pro provedení více než jednoho příkazu SQL.');
        }
        try {
            if (trim($queries[0])) {
//                $this->logger->info($queries[0]);
                $stat = $this->handler->query($queries[0]);  // vrací statement nebo false
            }
        } catch(PDOException $e) {
//            $this->logger->info('Error: '.$e->getMessage());
            $this->logger->error('Error: '.$e->getMessage());
            throw new ErrorRollbackException($e->getMessage(), 0, $e);
        }
        return $stat ? $stat : null;
    }

    public function findAllRows($tablename) {
        $query = "SELECT *
                FROM $tablename ";
//        $this->logger->info($query);
        $stmt = $this->handler->prepare($query);
        $stmt->execute();
//        $this->logger->info($query);
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }

    public function find($tablename, $criteriaArray = []) {
        foreach($criteriaArray as $key => $value) {
            $criteria[] = ":$key = $key";
        }
        $query = "SELECT *
                FROM $tablename ";
        if ($criteria) {
            $query .= "WHERE ".implode(" AND ", $criteria);
        }
//        $this->logger->info($query);
        $stmt = $this->handler->prepare($query);
        foreach($criteriaArray as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }

    /**
     * MySQL umožňuje escapovat apostrof i takto: \'
     * Metoda toto escapování převede na standardní SQL escapování '' a pak volá sql_explode
     * @param type $sql
     * @return type
     */
    private function mysql_explode($sql) {
        $sql = str_replace("\'", "''", $sql);
        return $this->sql_explode($sql);
    }

    /**
     *
     * @param type $sql
     * @return type
     */
    private function sql_explode($sql) {
        $sql = \trim(\trim($sql), ";").";";
        $separator = ";";
        $leftBracket = "'";
        $rightBracket = "'";

        $ret = array();
        $left_parenthesis = 0;
        $right_parenthesis = 0;
        $opened_paretnhesis = false;
        $pos = 0;
        for($i=0;$i<strlen($sql);$i++)
        {
            $c = $sql[$i];
            $opened_paretnhesis = $left_parenthesis>$right_parenthesis;
            if($c == $separator && !$opened_paretnhesis) {
                $ret[] = substr($sql, $pos, $i-$pos);
                $pos = $i+1;
            } elseif($opened_paretnhesis && $c == $rightBracket) {
                $right_parenthesis++;
            } elseif($c == $leftBracket) {
                $left_parenthesis++;
            }
        }
//        if($pos > 0) $ret[] = substr($sql, $pos);

        return $ret;
    }
}
