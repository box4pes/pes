<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Repository;

use Pes\Model\Entity\PersistableEntityInterface;
use Pes\Model\RowData\RowDataInterface;

/**
 * Interface pro POTOMKOVSKÉ repository s asociací 1:1
 * Metoda msí vytvořit entitu v potomkovské repository bez čtení dat z úložiště. Potomkovskou entitu vytvoří z rodičovských dat.
 *
 * @author pes2704
 */
interface RepoAssociatedWithJoinOneInterface extends RepoInterface {
    public function recreateEntityByParentData(RowDataInterface $parentRowData): ?PersistableEntityInterface;

    public function addChild(PersistableEntityInterface $childEntity): void;

    public function removeChild(PersistableEntityInterface $childEntity): void;
}
