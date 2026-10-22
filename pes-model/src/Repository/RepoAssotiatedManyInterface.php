<?php
namespace Pes\Model\Repository;

use Pes\Model\Repository\RepoInterface;
use Pes\Model\RowData\RowDataInterface;
use Pes\Model\Entity\PersistableEntityInterface;

/**
 * Interface pro POTOMKOVSKÉ repository s asociací 1:N
 *
 * @author pes2704
 */
interface RepoAssotiatedManyInterface extends RepoInterface {
    /**
     * Metoda získá potomkovské entity z potomkovského repository pomocí reference. Hodnoty polí reference naplní z rodičovských dat.
     *
     * @param string $referenceName Jméno refernce z DAO
     * @param RowDataInterface $parentRowData Rodičovská data pro získání hodnot polí reference.
     * @return iterable
     */
    public function findByParentData(string $referenceName, RowDataInterface $parentRowData): iterable;

    public function addChild(PersistableEntityInterface $childEntity): void;

    public function removeChild(PersistableEntityInterface $childEntity): void;
}
