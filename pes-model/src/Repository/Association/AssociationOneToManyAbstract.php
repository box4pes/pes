<?php
namespace Pes\Model\Repository\Association;

use Pes\Model\Repository\RepoAssotiatedManyInterface;
use Pes\Model\Entity\PersistableEntityInterface;
use Pes\Model\RowData\RowDataInterface;

use Pes\Model\Repository\Association\Exception\BadTypeOfIterableParameterMember;
use Pes\Model\Repository\Exception\BadReturnedTypeException;

/**
 * Description of AssociationOneToManyFactory
 *
 * @author pes2704
 */
abstract class AssociationOneToManyAbstract extends AssociationAbstract implements AssociationOneToManyInterface {

    /**
     * @var RepoAssotiatedManyInterface
     */
    protected $childRepo;

    /**
     *
     * @param RepoAssotiatedManyInterface $childRepo Repo pro získání, ukládání a mazání asociovaných entit.
     */
    public function __construct( RepoAssotiatedManyInterface $childRepo) {
        $this->childRepo = $childRepo;
    }

    /**
     * Metoda získá pole potomkovských entit z potomkovského repository pomocí metody findByReference(). Hodnoty polí reference naplní z rodičovských dat.
     *
     *
     * @param PersistableEntityInterface[] $parentEntities
     * @param RowDataInterface[] $parentRowdataArray
     * @return void
     */
    public function recreateChildEntities(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void {
        $childEntities = $this->childRepo->findByParentData($this->getReferenceName(), $parentRowData);
        if (!is_iterable($childEntities)) {
            $rcls = get_class($this->childRepo);
            $acls = get_called_class();
            throw new BadReturnedTypeException("Potomkovské entity získané z potomkovského repo {$rcls}->findByParentData() v asociaci OneToMany $acls není iterovatelná.");
        }
        $this->hydrateChild($parentEntity, $childEntities);
    }

    /**
     *
     * @param iterable $parentEntity PersistableEntityInterface
     * @return void
     */
    public function addEntities(PersistableEntityInterface $parentEntity): void {
        $this->extractChild($parentEntity, $childValue);
        if(!is_iterable($childValue)) {
            $ecls = get_class($parentEntity);
            $acls = get_called_class();
            throw new BadReturnedTypeException("Hodnota získaná z rodičovské entity typu $ecls metodou {$acls}->extractChild() není iterovatelná.");
        }
        foreach ($childValue as $childEntity) {
            /** @var PersistableEntityInterface $childEntity */
            if (!$childEntity instanceof PersistableEntityInterface) {
                $cls = PersistableEntityInterface::class;
                throw new BadTypeOfIterableParameterMember("Prvky předaného iterable parametru metody musí být typu $cls");
            }
            $this->childRepo->addChild($childEntity);
        }
    }

    public function removeEntities(PersistableEntityInterface $parentEntity): void {
        $this->extractChild($parentEntity, $childValue);
        if(!is_iterable($childValue)) {
            $ecls = get_class($parentEntity);
            $acls = get_called_class();
            throw new BadReturnedTypeException("Hodnota získaná z rodičovské entity typu $ecls metodou {$acls}->extractChild() není iterovatelná.");
        }
        foreach ($childValue as $childEntity) {
            /** @var PersistableEntityInterface $childEntity */
            if (!$childEntity instanceof PersistableEntityInterface) {
                $cls = PersistableEntityInterface::class;
                throw new BadTypeOfIterableParameterMember("Prvky předaného iterable parametru metody musí být typu $cls");
            }
            $this->removeChild($childEntity);
        }
    }
}
