<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Dao;

use Pes\Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DaoInterface {
    // abstract dao
    
    /**
     * Vrací jméno databázového schematu (jméno databáze).
     */
    public function getSchemaName();
    
    // metody musí implemtovat jednotlivá Dao

    /**
     * Vrací pole jmen polí atributu primárního klíče tabulky.
     *
     * @return array
     */
    public function getPrimaryKeyAttributes(): array;

    /**
     * Vrací pole jmen všech atributů tabulky (včetně primárního klíče).
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Vrací jméno databázové tabulky (nebo view).
     *
     * @return string
     */
    public function getTableName(): string;

    // metody implementované v DaoAbstract

    /**
     * Vrací primární klíč - asociativní pole klíč ->hodnota polí primárního klíče
     *
     * Parametr je řádek dat -asociativní pole, které musí obsahovat alespoň položky odpovídající polím primárního klíče.
     * Další položky položky v poli nevadí, je tak možné použít celý předtím načtený řádek dat nebo jen pole s položkami primárního klíče
     *
     * @param array $row Řádek dat -asociativní pole, které musí obsahovat alespoň položky odpovídající polím primárního klíče
     * @return array
     */
    public function getPrimaryKey(array $row): array;

    /**
     * Vrací řádek dat podle hodnot primárního klíče nebo null.
     * Parametr musí být asociativní pole dvojic jméno-hodnota a musí obsahovat jména a hodnoty atributů primárního klíče.
     * Metoda kontroluje jestli parametr obsahuje všechna jména atributů primárního klíče.
     *
     * Pokud je zadán parametr konstruktoru ContextFactoryInterface, metoda vždy použije tento kontext a pokud
     * v databázi řádek se zadaným primárním klíčem sice existuje, ale nesplňuje podmínky kontextu, metoda vrací null.
     *
     * @param array $id
     * @return RowDataInterface|null
     */
    public function get(array $id): ?RowDataInterface;

    /**
     * Vrací řádek dat podle hodnot položek parametru nebo null.
     * Předpokládá, že parametr určuje unikátní řádek dat. V případě, že zadanému parametru odpovídá více řádků hlásí chybu nebo vyhazuje výjimku.
     * Parametr musí být asociativní pole dvojic jméno-hodnota a musí obsahovat unikátní kombinaci jmen a hodnot atributů tabulky.
     *
     * Pokud je zadán parametr konstruktoru ContextFactoryInterface, metoda vždy použije tento kontext a pokud
     * v databázi řádek se zadaným klíčem sice existuje, ale nesplňuje podmínky kontextu, metoda vrací null.
     *
     * @param array $uniqueKey
     * @return RowDataInterface|null
     */
    public function getUnique(array $uniqueKey): ?RowDataInterface;

    /**
     * Vrací pole nebo iterátor řádkových dat eventuálně prázdné pole nebo iterátor. Data načte podle klíče. Používá PDO placeholdery a bind.
     * Klíč je asociativní pole dvojic jméno=>hodnota. Ze jmen položek v klíči budou vytvořeny placeholdery a z celého klíče pak nahrazeny placeholdery v příkazu where.
     *
     * Pokud je zadán parametr konstruktoru ContextFactoryInterface, metoda vždy použije tento kontext a pokud
     * v databázi řádky se zadaným klíčem sice existují, ale nesplňují podmínky kontextu, metoda tyto řádky nevrací.
     *
     * @param array $nonUniqueKey Asociativní pole dvojic pole klíče=>hodnota
     * @return iterable
     */
    public function findNonUnique(array $nonUniqueKey): iterable;

    /**
     * Vrací pole nebo iterátor řádkových dat eventuálně prázdné pole nebo iterátor. Data načte podle zadaného příkazu where v SQL syntaxi vhodné pro PDO
     * s pojmenovanými placeholdery (paceholder je jméno uvozené dvojtečkou). Z druhého paramentu, pole dvojic jméno-hodnota, budou budou nahrazeny placeholdery
     * v zadaném příkazu where.
     *
     * POZOR! Tento příkaz použite klasuli where tak, jak je zadána, nic nepřidává a to ani podmínky kontextu. To bez ohledu na to zda je do konstruktoru DAO předán
     * objekt ContextFactoryInterface.
     *
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkatu where
     * @return iterable<RowDataInterface>
     */
    public function find($whereClause="", array $touplesToBind=[]): iterable;

    /**
     * Vrací pole nebo iterátor řádkových dat eventuálně prázdné pole nebo iterátor.
     * Pokud není zadán parametr konstruktoru ContextFactoryInterface, metoda načte všechna data v tabulce.
     *
     * Pokud je zadán parametr konstruktoru ContextFactoryInterface, metoda vždy použije tento kontext a pokud
     * v databázi řádky se zadaným klíčem sice existují, ale nesplňují podmínky kontextu, metoda tyto řádky nevrací.
     *
     * @return iterable<RowDataInterface>
     */
    public function findAll(): iterable;
}
