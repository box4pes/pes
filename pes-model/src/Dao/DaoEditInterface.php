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
interface DaoEditInterface extends DaoInterface {
    public function insert(RowDataInterface $rowData): bool;
    public function getLastInsertedPrimaryKey(): array;
    public function update(RowDataInterface $rowData): bool;
    public function delete(RowDataInterface $rowData): bool;
    public function getRowCount(): int;
}
