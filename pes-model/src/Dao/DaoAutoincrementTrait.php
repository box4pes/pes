<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Dao;

use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;

use Pes\Model\RowData\RowDataInterface;
use Pes\Model\Dao\Exception\DaoLastInsertIdFailedAfterMultipleRowInsertException;
use Pes\Model\Dao\Exception\DaoAutoicrementedKeyAttributeFieldNameException;

use UnexpectedValueException;

/**
 * Trait pro implementaci metody rozhranní DaoAutoincrementKeyInterface
 *
 * @author pes2704
 */
trait DaoAutoincrementTrait {

    /**
     * Metoda nastaví objektu RowData hodnotu pole primárního klíče, kterou databáze vygenerovala
     * při posledním provedeném příkazu insert.
     * Metoda vrací platnou hodnotu jen po vložení právě jednoho řádku, jinak vyhazuje výjimku.
     * Poznámka: v transakci je třeba volat metodu před příkazem commit.
     * Poznámka: Metoda je funkční pro MySQL a MariaDB, pro jiné databáze záleží na driveru.
     *
     * @param RowDataInterface $rowdata
     * @throws DaoLastInsertIdFailedAfterMultipleRowInsertException
     */
    protected function setAutoincrementedValue(RowDataInterface $rowdata) {
        try {
            $value = $this->lastInsertIdValue();
        } catch (DaoLastInsertIdFailedAfterMultipleRowInsertException $daoExc) {
            throw $daoExc;
        }
        /** @var DaoEditAutoincrementKeyInterface $this */
        $name = $this->getAIKeyFieldName();
        $rowdata->forcedSet($name, $value);
    }

    private function getAIKeyFieldName() {
        $pk = $this->getAutoincrementFieldName();
        if (!in_array($pk, $this->getAttributes())) {  // AI sloupec nemusí být členem primárního klíče
            throw new UnexpectedValueException("Metoda getAutoincrementFieldName() vrací hodnotu '$pk', která není v poli atributů.");
        }
        return $pk;
    }

    /**
     * Vrací hodnotu autoincrement klíče vzniklého při posledním provedeném insertu.
     * Metoda vrací platnou hodnotu jen po vložení právě jednoho řádku, jinak vyhazuje výjimku.
     * Poznámka: v transakci je třeba volat metodu před příkazem commit.
     * Poznámka: Metoda je funkční pro MySQL a MariaDB, pro jiné databáze záleží na driveru.
     *
     * @return type
     * @throws DaoLastInsertIdFailedAfterMultipleRowInsertException
     */
    private function lastInsertIdValue() {
        /** @var DaoEditAutoincrementKeyInterface $this */
        if ($this->rowCount == 1) {
            return  $this->dbHandler->lastInsertId();
        } else {
            throw new DaoLastInsertIdFailedAfterMultipleRowInsertException("Metoda lastInsertIdValue() vrací platnou hodnotu jen při vložení právě jednoho řádku. Poslední insert vložil řádky: $this->rowCount.");
        }
    }
}
