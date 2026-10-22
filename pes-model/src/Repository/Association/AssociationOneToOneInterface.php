<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Repository\Association;

use Pes\Model\Entity\PersistableEntityInterface;
use Pes\Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface AssociationOneToOneInterface extends AssociationInterface {
    /**
     *
     * @param string $referenceName Jméno reference z DAO
     * @param RowDataInterface $parentRowData Data pro získání hodnot do reference
     * @return PersistableEntityInterface|null
     */
    public function recreateChildEntity(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void;

    /**
     *
     * @param PersistableEntityInterface $parentEntity
     * @return void
     */
    public function addEntity(PersistableEntityInterface $parentEntity): void;

    /**
     *
     * @param PersistableEntityInterface $parentEntity
     * @return void
     */
    public function removeEntity(PersistableEntityInterface $parentEntity): void;

}
