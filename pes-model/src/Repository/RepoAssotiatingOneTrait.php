<?php
namespace Pes\Model\Repository;

use Pes\Model\Repository\Association\AssociationOneToOneInterface;

use Pes\Model\Repository\RepoAssotiatingOneInterface;  // použito jen v komentáři

/**
 * Trait s implementací RepoAssotiatingOneInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
trait RepoAssotiatingOneTrait {

    public function registerOneToOneAssociation(AssociationOneToOneInterface $assotiation) {
        $this->associations[] = $assotiation;
    }
}
