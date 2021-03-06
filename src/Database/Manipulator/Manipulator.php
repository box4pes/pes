<?php
namespace Pes\Database\Manipulator;

use Pes\Database\Handler\HandlerInterface;
use Pes\Database\Statement\StatementInterface;

use Psr\Log\LoggerInterface;

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
     * @throws \UnexpectedValueException Pokud neexistuje zdrojová tabulka
     * @throws \LogicException Již existuje cílová tabulka kopírování.
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
            throw new \UnexpectedValueException("Zdrojová tabulka kopírování '$oldTableName' neexistuje. Nevím co mám dělat a tak jsem se zhroutil.");
        }
        if ($this->tableExists($newTableName)) {
            throw new \LogicException("Zakázané nebezpečné chování - cílová tabulka kopírování '$newTableName'již existuje. Nelze přepsat existující tabulku.");
        }

        $dbhTransact = $this->handler;
        try {
            $dbhTransact->beginTransaction();
            $this->logger->info("CREATE TABLE $newTableName LIKE $oldTableName");
            $dbhTransact->exec("CREATE TABLE $newTableName LIKE $oldTableName");
            $this->logger->info("INSERT $newTableName SELECT * FROM $oldTableName");
            $dbhTransact->exec("INSERT $newTableName SELECT * FROM $oldTableName");
            $this->logger->info('Commit.');
            $succ = $dbhTransact->commit();
        } catch(\Exception $e) {
            $this->logger->error('Rollback: '.$e->getMessage());
            $dbhTransact->rollBack();
            throw new \RuntimeException($e);
        }
        return $succ ? TRUE : FALSE;
    }

    /**
     *
     * @param string $tableName
     * @return bool TRUE, pokud tabulka existuje a lze z ní číst, jinak FALSE.
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
                break;
        }

        $stmt = $dbh->prepare(
            "SELECT table_name
            FROM information_schema.TABLES
            WHERE (TABLE_SCHEMA = :db_name) AND (TABLE_NAME = :table_name)"
        );
        $stmt->bindParam(':db_name', $dbName, \PDO::PARAM_STR);
        $stmt->bindParam(':table_name', $tableName, \PDO::PARAM_STR);

        $stmt->execute();
        $q =  $stmt->fetch(\PDO::FETCH_ASSOC); //  array['table_name']=>$tableName)
        return is_array($q) ? TRUE : FALSE;
    }

    /**
     * Vykoná obsah zadaného souboru jako posloupnost SQL příkazů.
     * Předpokládá, že příkazy v souboru jsou odděleny středníkem ";".
     * Používá databázový handler generovaný database factory, která byla nastavena při instancování objektu.
     * Příkazy vykonává v rámci jedné transakce, kterou spouští.
     * Neporadí si, pokud ji zavoláte uprostřed již spuštěné transkace. V takovém případě by vykonání neznámé posloupnosti SQL příkazů mohlo vést
     * k nepředvídaným výsledkům. V tomto případě metoda vyhodí výjimku.
     *
     * @param string $sql Řetězec s posloupností SQL příkazů oddělený středníky.
     * @return bool TRUE, pokud transakce skončila úspěšně, jinak FALSE.
     * @throws \LogicException Při volání uprostřed již spuštěné transakce.
     * @throws \RuntimeException Pokud nelze přečíst zadaný soubor.
     * @throws \UnexpectedValueException Výjimka při vykonávání transakce.
     */
    public function exec($sql) {
        if (!$sql) {
            throw new \LogicException('Zadaný SQL řetězec je prázdný.');
        }
        $queries = \explode(';', \trim($sql));
        $dbhTransact = $this->handler;
        if ($dbhTransact->inTransaction()) {
            throw new \LogicException('Nelze volat tuto metodu uprostřed spuštěné databázové transakce.');
        }
        try {
            $dbhTransact->beginTransaction();
            foreach ($queries as $query) {
                if ($query) {
                    $this->logger->info($query);
                    $dbhTransact->exec($query);
                }
            }
            $this->logger->info('Commit.');
            $succ = $dbhTransact->commit();
        } catch(\Exception $e) {
            $this->logger->error('Rollback: '.$e->getMessage());
            $dbhTransact->rollBack();
            throw new \RuntimeException($e);
        }
        return $succ ? TRUE : FALSE;
    }

    public function query($sql): StatementInterface {
        if (!$sql) {
            throw new \LogicException('Zadaný SQL řetězec je prázdný.');
        }
        $queries = \explode(';', \trim($sql));
        if ($queries === false) {
            throw new \LogicException("Příkazy SQL musí být oddělené středníkem. Nenalezen žádný příkaz oddělený středníkem v zadaném stringu.");
        }
        $dbhTransact = $this->handler;
        if ($dbhTransact->inTransaction()) {
            throw new \LogicException('Nelze volat tuto metodu uprostřed spuštěné databázové transakce.');
        }
        try {
            $dbhTransact->beginTransaction();
            foreach ($queries as $query) {
                if (trim($query)) {
                    if (isset($stat)) {
                        throw new \LogicException("Touto metodou lze vykonat pouze jeden SQL příkaz. Nalezen druhý příkaz oddělený středníkem v zadaném stringu.");
                    }
                    $this->logger->info($query);
                    $stat = $dbhTransact->query($query);
                }
            }
            $this->logger->info('Commit.');
            $succ = $dbhTransact->commit();
        } catch(\Exception $e) {
            $this->logger->error('Rollback: '.$e->getMessage());
            $dbhTransact->rollBack();
            throw new \RuntimeException($e);
        }
        return $stat;
    }

    public function findAllRows($tablename) {
        $query = "SELECT *
                FROM $tablename ";
        $this->logger->info($query);
        $stmt = $this->handler->prepare($query);
        $stmt->execute();
        $this->logger->info($query);
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
        $this->logger->info($query);
        $stmt = $this->handler->prepare($query);
        foreach($criteriaArray as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }
}
