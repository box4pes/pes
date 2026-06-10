<?php
namespace Pes\Model\Entity;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Interface entity, která se vyskytuje ve více instancích odlišených od sebe identitou (klíčem).
 *
 * @author pes2704
 */
interface PersistableEntityInterface extends EntityInterface {

    public function setPersisted(): PersistableEntityInterface;

    /**
     *
     * @return \Pes\Model\Entity\PersistableEntityInterface
     */
    public function setUnpersisted(): PersistableEntityInterface;

    /**
     *
     * @return bool
     */
    public function isPersisted():bool;

    public function lock(): PersistableEntityInterface;

    public function unlock(): PersistableEntityInterface;

    public function isLocked(): bool;
    
    public function __clone();
}
