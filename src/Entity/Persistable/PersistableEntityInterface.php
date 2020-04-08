<?php
/**
 *
 * @author pes2704
 */
namespace Pes\Entity\Persistable;

use Pes\Entity\EntityInterface;
use Pes\Entity\Persistable\IdentityInterface;

interface PersistableEntityInterface extends EntityInterface {
    /**
     * @return IdentityInterface
     */
    public function getIdentity();
    /**
     * @param IdentityInterface $pdentity
     */
    public function setIdentity(IdentityInterface $identity);
    /**
     * @return boolean
     */
    public function hasIdentity();
}