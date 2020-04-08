<?php
namespace Pes\Entity\Persistable;

use Pes\Entity\Persistable\Factory\FactoryInterface;
use Pes\Entity\Persistable\PersistableEntityInterface;
use Pes\Entity\Persistable\IdentityInterface;
/**
 * Reference obashuje vždy id referencované entity a buď obsahuje referencovanou entitu nebo se jedná o proxy objekt a obahuje metodu
 * pro vytvoření entity s použitím známého id entity.
 * @author pes2704
 */
interface AssotiationInterface {
    
    public function getEntity();
    public function setEntity(PersistableEntityInterface $entity);
    public function setFactory(IdentityInterface $identity, FactoryInterface $factory);
}
