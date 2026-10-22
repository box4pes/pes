<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\DataManager;

use Pes\Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DataManagerInterface {

    public function getUnique(array $uniqueParams): ?RowDataInterface;

    public function getPrimaryKeyAttributes(): array;
    public function getAttributes(): array;

    /**
     * Vrací jméno databázové tabulky (nebo view).
     *
     * @return string
     */
    public function getTableName(): string;

    // metody implementované v DaoAbstract

    /**
     * Vrací asociativní pole dvojic klíč=>hodnota polí primárního klíče.
     *
     * @param array $primaryFieldsValue
     * @return array
     */
    public function getPrimaryKeyTouples(array $primaryFieldsValue): array;

    /**
     * Vrací řádek dat podle hodnot primárního klíče, pokud řádek neexistuje vrací null.
     * Zadané pole musí být asociativní pole dvojic jméno-hodnota a musí obsahovat jména a hodnoty atributů primárního klíče.
     * Metoda kontroluje jestli zadané pole obsahuje všechna jména atributů primárního klíče.
     *
     * @param array $id
     * @return RowDataInterface|null
     */
    public function get(array $id): ?RowDataInterface;
    public function find($whereClause="", $touplesToBind=[]): iterable;
    public function set($index, RowDataInterface $data): void;
    public function unset($index): void;
    public function flush(): void;
}
