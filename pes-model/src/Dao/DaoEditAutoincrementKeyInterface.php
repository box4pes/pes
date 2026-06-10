<?php

namespace Pes\Model\Dao;

use Pes\Model\Dao\DaoEditInterface;

/**
 *
 * @author pes2704
 */
interface DaoEditAutoincrementKeyInterface extends DaoEditInterface {

    /**
     * Metoda musí vracet jméno sloupce, který je typu autoincrement
     */
    public function getAutoincrementFieldName();

    /**
     * Metoda musí vracet hodnotu databází generované hodnoty pole primárního klíče,
     * kterou databáze vygenerovala při posledním provedeném příkazu insert.
     */
//    public function lastInsertIdValue();

    /**
     * Metoda musí vracet asociativní pole s dvojicí jméno-hodnota pro jméno pole primárního klíče a hodnoty pole
     * primárního klíče, kterou databáze vygenerovala při posledním provedeném příkazu insert.
     *
     * @return array
     */
//    public function getLastInsertIdTouple(): array;

}
