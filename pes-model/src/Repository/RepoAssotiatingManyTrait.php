<?php
namespace Pes\Model\Repository;

use Pes\Model\Repository\Association\AssociationOneToManyInterface;

use Pes\Model\Repository\RepoAssotiatingManyInterface;  // použito jen v komentáři

/**
 * Trait s implementací RepoAssotiatingManyInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
trait RepoAssotiatingManyTrait {

    public function registerOneToManyAssotiation(AssociationOneToManyInterface $assotiation) {
        $this->associations[] = $assotiation;
    }

}
