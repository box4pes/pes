<?php
namespace Pes\Entity\Persistable;
use Pes\Entity\EntityAbstract;
use Pes\Entity\Persistable\IdentityInterface;

/**
 * Description of Projektor2_Model_RowModelAbstract
 *
 * @author pes2704
 */
abstract class PersistableEntityAbstract extends EntityAbstract implements PersistableEntityInterface {
    
    /**
     *
     * @var IdentityInterface 
     */
    private $identity;
    
    /**
     * 
     * @param IdentityInterface $identity
     */
    public function __construct(IdentityInterface $identity=NULL) {
        $this->identity = $identity;
    }
    
    /**
     * Vrací objekt identity.
     * @return IdentityInterface
     * @throws \LogicException
     */
    public function getIdentity() {
        if ($this->identity) {
            return $this->identity;
        } else {
            throw new \LogicException('Objekt '.  get_called_class().' není dosud persistován, nemá identitu.');
        }
    }
    
    /**
     * Nasteevuje identitu entity
     * @param IdentityInterface $identity
     * @return \Pes\Entity\Persistable\PersistableEntityAbstract
     * @throws \LogicException
     */
    public function setIdentity(IdentityInterface $identity) {
        if ($this->hasIdentity()) {
            throw new \LogicException('Objekt '.  get_called_class().' již byl persistován, nelze nastavovat identitu znovu.');
        } else {
            $this->identity = $identity;
        }
        return $this;
    }
    
    public function hasIdentity() {
        return isset($this->identity);
    }
}
