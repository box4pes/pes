<?php
namespace Pes\Entity\Persistable;

use Pes\Entity\Persistable\Factory\FactoryInterface;
use Pes\Entity\Persistable\IdentityInterface;
/**
 * Description of Assotiation
 *
 * @author pes2704
 */
class Assotiation implements AssotiationInterface {
    
    private $assotiatedEntityIdentity;
    /**
     * @var FactoryInterface 
     */
    private $assotiatedEntityFactory;
    /**
     * @var PersistableEntityInterface 
     */
    private $assotiatedEntity;
    
    public function getEntity() {
        if (!$this->assotiatedEntity) {
            $this->assotiatedEntity = $this->create();
        }
        return $this->assotiatedEntity;
    }

    public function setEntity(PersistableEntityInterface $entity) {
        $this->assotiatedEntity = $entity;
    }
    
    public function setFactory(IdentityInterface $identity, FactoryInterface $factory) {
        $this->assotiatedEntityIdentity= $identity;
        $this->assotiatedEntityFactory = $factory;
    }

    private function create() {
        if($this->assotiatedEntityIdentity AND $$this->assotiatedEntityFactory) {
            return $this->assotiatedEntityFactory->create($this->assotiatedEntityIdentity);
        } else {
            throw new \LogicException('Objekt '.  get_called_class().' nemá nemá nastavenou asociovanou entitu ani id a factory asociované entity.');
        }
    }
    
}
