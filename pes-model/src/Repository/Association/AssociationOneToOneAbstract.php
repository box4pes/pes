<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Repository\Association;

use Pes\Model\Entity\PersistableEntityInterface;
use Pes\Model\RowData\RowDataInterface;
use Pes\Model\Repository\Association\Hydrator\AssociationHydratorInterface;

use Pes\Model\Repository\RepoAssotiatedOneInterface;
use Pes\Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;

/**
 * Description of AssotiatedRepo
 *
 * @author pes2704
 */
abstract class AssociationOneToOneAbstract extends AssociationAbstract implements AssociationOneToOneInterface {

    /**
     * @var RepoAssotiatedOneInterface
     */
    protected $childRepo;

    /**
     *
     * @param RepoAssotiatedOneInterface $childRepo
     */
    public function __construct(RepoAssotiatedOneInterface $childRepo) {
        $this->childRepo = $childRepo;
    }

    /**
     * Vyzvedne entitu z potomkovského repository getByReference() a pomocí child hydratoru hydratuje rodičovskou entitu vyzvednutou potomkonskou entitou.
     * Hodnoty referenčního klíče pro potomkovské repository->getByReference() bere z rodičovských dat.
     *
     * Poznámka: Pokud potomkovská entita neexistuje hydratuje hodnotou null.
     * 
     * @param PersistableEntityInterface $parentEntity
     * @param RowDataInterface $parentRowData
     * @return void
     */
    public function recreateChildEntity(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void {
        $childEntity = $this->childRepo->getByParentData($this->getReferenceName(), $parentRowData);
        $this->hydrateChild($parentEntity, $childEntity);
    }

    /**
     * Pokud asociovaná (child) entita není persistována, přidá ji do repository.
     *
     * @param PersistableEntityInterface $childEntity
     * @return void
     */
    public function addEntity(PersistableEntityInterface $parentEntity): void {
        $this->extractChild($parentEntity, $childEntity);
        // parametr entity může být null - metoda je volána např. repo->addChild($parentEntity->getCosi())
        if (isset($childEntity) AND !$childEntity->isLocked() AND !$childEntity->isPersisted()) {
            $this->childRepo->addChild($childEntity);
        }
    }

    /**
     * Pokud asociovaná (child) entita je persistována, odstraní ji z repository.
     *
     * @param PersistableEntityInterface $childEntity
     * @return void
     */
    public function removeEntity(PersistableEntityInterface $parentEntity): void {
        $this->extractChild($parentEntity, $childEntity);
        // parametr entity může být null - metoda je volána např. repo->addChild($parentEntity->getCosi())
        if (isset($childEntity) AND !$childEntity->isLocked() AND $childEntity->isPersisted()) {
            $this->childRepo->removeChild($childEntity);
        }
    }
}