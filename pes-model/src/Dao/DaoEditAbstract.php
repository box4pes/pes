<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Dao;

use Pes\Model\RowData\RowDataInterface;
use Pes\Model\RowData\RowData;

use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;

use Pes\Model\Dao\Exception\DaoUnexpectecCallOutOfTransactionException;
use Pes\Model\Dao\Exception\DaoKeyVerificationFailedException;
use LogicException;

/**
 * Description of DaoEditAbstract
 *
 * @author pes2704
 */
abstract class DaoEditAbstract extends DaoAbstract implements DaoEditInterface {

    protected $rowCount;

    /**
     *
     * @var RowDataInterface
     */
    private $lastInsertedRowData;

    /**
     * Přídá změněná data do databáze.
     *
     * Ukládá pouze data
     * @param RowDataInterface $rowData
     * @return bool
     */
    public function insert(RowDataInterface $rowData): bool {
        $ret = ($this instanceof DaoEditKeyDbVerifiedInterface) ? $this->execInsertWithKeyVerification($rowData) : $this->execInsert($rowData);
        $this->setLastInsertedIdForAutoincrementedValue($rowData);
        $this->lastInsertedRowData = clone $rowData;
        return $ret;
    }

    private function setLastInsertedIdForAutoincrementedValue(RowDataInterface $rowData) {
        if($this instanceof DaoEditAutoincrementKeyInterface) {
            $this->setAutoincrementedValue($rowData);  // metoda je v DaoAutoincrementTrait
        }
    }

    public function getLastInsertedPrimaryKey(): array {
        if( ! isset($this->lastInsertedRowData)) {
            throw new LogicException("Nelze získat hodnotu klíče, nebyl proveden žádný insert.");
        }
        $keyAttribute = $this->getPrimaryKeyAttributes();
        $key = [];
        foreach ($keyAttribute as $field) {
            $key[$field] = $this->lastInsertedRowData->offsetGet($field);
        }
        return $key;
    }

    /**
     *
     * @param RowDataInterface $rowData
     * @return bool
     */
    public function update(RowDataInterface $rowData): bool {
        return $this->execUpdate($rowData);
    }

    /**
     *
     * @param RowDataInterface $rowData
     * @return bool
     */
    public function delete(RowDataInterface $rowData): bool {
        return $this->execDelete($rowData);
    }

    ####################################################################

    /**
     * Očekává SQL string s příkazem INSERT. Provede ho s použitím parametrů a vrací výsledek metody PDOStatement->execute().
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací výsledek metody PDOStatement->execute().
     *
     * @param RowDataInterface $rowData
     * @return bool
     * @throws \Exception
     * @throws DaoKeyVerificationFailedException
     */
    protected function execInsertWithKeyVerification(RowDataInterface $rowData): bool {
        $tableName = $this->getTableName();
        $keyNames = $this->getPrimaryKeyAttributes();
        try {
            if (!$this->dbHandler->inTransaction()) {
                $this->dbHandler->beginTransaction();
            }
            $changed = $rowData->yieldChangedAsArrayObject();
            $found = $this->getWithinTransaction($tableName, $keyNames, $changed);
            if  (! $found)   {
                $this->execInsert($rowData);   // předpokládám, že changed je i sloupec s klíčem
            } else {
                foreach ($keyNames as $name) {
                    $k[] = $changed[$name];
                }
                $key = implode(', ', $k);
                throw new DaoKeyVerificationFailedException("Hodnota klíče type '$key' již v tabulce $tableName existuje.");
            }
            /*** commit the transaction ***/
            $success = $this->dbHandler->commit();
        } catch(\Exception $e) {
            $this->dbHandler->rollBack();
            throw $e;
        }
        return $success ?? false;
    }

    protected function getWithinTransaction($tableName, array $keyNames, iterable $rowData) {
        if ($this->dbHandler->inTransaction()) {
            $select = $this->sql->select($this->getAttributes());
            $whereTouples = $this->sql->touples($keyNames);
            $from = $this->sql->from($tableName);
            $where = $this->sql->where($this->sql->and($whereTouples));
            $sql = $select.$from.$where." LOCK IN SHARE MODE";   //nelze použít LOCK TABLES - to commitne aktuální transakci!
            $stmt = $this->getPreparedStatement($sql);
            $this->bindParams($stmt , $rowData, $keyNames );
            $stmt->execute();
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);  // jinak vrací RowData
            $count = count($res);                
            return  $count ? true : false;
        } else {
            throw new DaoUnexpectecCallOutOfTransactionException('Metodu '.__METHOD__.' lze volat pouze v průběhu spuštěné databázové transakce.');
        }
    }

    /**
     * Očekává SQL string s příkazem INSERT. Provede ho s použitím parametrů a vrací výsledek metody PDOStatement->execute().
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací výsledek metody PDOStatement->execute().
     *
     *
     * @param RowDataInterface $rowData
     * @return bool
     */
    protected function execInsert(RowDataInterface $rowData): bool {
        if ($rowData->isChanged()) {
            $tableName = $this->getTableName();
            $changed = $rowData->fetchChanged();
            $changedNames = array_keys($changed);
            $cols = $this->sql->columns($changedNames);
            $values = $this->sql->values($changedNames);
            $sql = "INSERT INTO $tableName ($cols)  VALUES ($values)";
            $statement = $this->getPreparedStatement($sql);
            $this->bindParams($statement, $changed);
            $success = $statement->execute();
            $this->rowCount = $statement->rowCount();
        }
        return $success ?? false;
    }

    /**
     * Očekává SQL string s příkazem UPDATE. Provede ho s použitím parametrů a vrací výsledek metody PDOStatement->execute().
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není, vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací výsledek metody PDOStatement->execute().
     *
     * @param RowDataInterface $rowData
     * @return bool
     */
    protected function execUpdate(RowDataInterface $rowData): bool {
        if ($rowData->isChanged()) {
            $tableName = $this->getTableName();
            $whereTouples = $this->sql->touples($this->getPrimaryKeyAttributes(), 'key_');
            $oldData = $rowData->getArrayCopy();
            $changed = $rowData->fetchChanged();
            $changedNames = array_keys($changed);
            $setTouples = $this->sql->touples($changedNames);
            $sql = "UPDATE $tableName SET ".$this->sql->set($setTouples).$this->sql->where($this->sql->and($whereTouples));
            $statement = $this->getPreparedStatement($sql);
            $this->bindParams($statement, $changed);
            $this->bindParams($statement, $oldData, $this->getPrimaryKeyAttributes(), 'key_');
            $success = $statement->execute();
            $this->rowCount = $statement->rowCount();
        }
        return $success ?? false;
    }

    /**
     * Očekává SQL string s příkazem DELETE. Provede ho s použitím parametrů a vrací výsledek metody PDOStatement->execute().
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací výsledek metody PDOStatement->execute().
     *
     * @param RowDataInterface $rowData
     * @return bool
     */
    protected function execDelete(RowDataInterface $rowData): bool {
        $tableName = $this->getTableName();
        $keyNames = $this->getPrimaryKeyAttributes();
        $where = $this->sql->touples($keyNames);
        $sql = "DELETE FROM $tableName ".$this->sql->where($this->sql->and($where));
        $statement = $this->getPreparedStatement($sql);
        $this->bindParams($statement, $rowData, $keyNames);
        $success = $statement->execute();
        $this->rowCount = $statement->rowCount();
        return $success;
    }

    /**
     * Vrací počet řádek dotčených posledním příkazem delete, insert nebo update
     *
     * Správná funkce předpokládá nastavení atributu handleru PDO::MYSQL_ATTR_FOUND_ROWS = true
     *
     * @return int
     */
    public function getRowCount(): int {
        return $this->rowCount;
    }

}
