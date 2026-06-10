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
 *
 * @author pes2704
 */
interface RepoAssotiatedOneInterface extends RepoInterface {
    /**
     * Metoda získá potomkovskou entoty z potomkovského repository pomocí reference. Hodnoty polí reference naplní z rodičovských dat.
     *
     * @param string $referenceName Jméno refernce z DAO
     * @param RowDataInterface $parentRowData Rodičovská data pro získání hodnot polí reference.
     * @return PersistableEntityInterface|null
     */
    public function getByParentData(string $referenceName, RowDataInterface $parentRowData): ?PersistableEntityInterface;

    public function addChild(PersistableEntityInterface $childEntity): void;

    public function removeChild(PersistableEntityInterface $childEntity): void;

}
