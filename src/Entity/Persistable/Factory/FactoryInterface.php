<?php
namespace Pes\Entity\Persistable\Factory;

use Pes\Entity\Persistable\IdentityInterface;

/**
 *
 * @author pes2704
 */
interface FactoryInterface {
    public function create(IdentityInterface $identity);
}
