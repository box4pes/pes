<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Repository\Association;

use Pes\Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface AssociationInterface {

    /**
     * Musí vracet jméno reference z DAO
     */
    public function getReferenceName();
    public function extractChild(PersistableEntityInterface $parentEntity, &$childValue=null): void;
    public function hydrateChild(PersistableEntityInterface $parentEntity, &$childValue): void;
    public function flushChildRepo(): void;

}
