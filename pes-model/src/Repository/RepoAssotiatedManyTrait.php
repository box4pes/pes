<?php
namespace Pes\Model\Repository;

use Pes\Model\Entity\PersistableEntityInterface;
use Pes\Model\RowData\RowDataInterface;

use UnexpectedValueException;

use Pes\Model\Repository\RepoAssotiatedManyInterface;  // použito jen v komentáři

/**
 * Trait s implementací RepoAssotiatedManyInterface interface pro POTOMKOVSKÉ repository s asociací 1:N
 *
 * @author pes2704
 */
trait RepoAssotiatedManyTrait {

    /**
     * Metoda získá potomkovskou entoty z potomkovského repository pomocí reference. Hodnoty polí reference naplní z rodičovských dat.
     *
     * @param string $referenceName Jméno refernce z DAO
     * @param RowDataInterface $parentRowData Rodičovská data pro získání hodnot polí reference.
     * @return iterable
     */
    public function findByParentData(string $referenceName, RowDataInterface $parentRowData): iterable {
        $referenceKey = $this->createReferenceKeyFromParentData($referenceName, $parentRowData);
        $rowDataArray = $this->dataManager->findByReference($referenceName, $referenceKey);
        $recreated = [];
        foreach ($rowDataArray as $rowData) {
            $recreated[] = $this->recreateEntityByRowData($rowData);
        }
        return $recreated;
    }

    /**
     * Vytvoří asocitaivní pole dvojic klíče z jmen polí potomka a hodnot rodičovských dat.
     *
     * @param string $referenceName
     * @param RowDataInterface $parentRowData
     * @return type
     * @throws UnexpectedValueException
     */
    private function createReferenceKeyFromParentData(string $referenceName, RowDataInterface $parentRowData) {
        //TODO: aplikuj NominateFilter nastavený podle atributů
        /** @var DaoWithReferenceInterface $this->dataManager */
        $refAttribute = $this->dataManager->getReferenceAttributes($referenceName);
        $childTouples = [];
        foreach ($refAttribute as $childField => $parentField) {
            if( ! $parentRowData->offsetExists($parentField)) {
                $daoCls = get_class($this->dataManager);
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota z rodičovských dat. Atributy potomka $daoCls obsahují pole '$parentField' a pole rodičovských dat pro vytvoření klíče neobsahuje prvek s takovým jménem.");
            }
            $childTouples[$childField] = $parentRowData->offsetGet($parentField);
        }
        return $childTouples;
    }

    public function addChild(PersistableEntityInterface $childEntity): void {
        $this->addEntity($childEntity);
    }

    public function removeChild(PersistableEntityInterface $childEntity): void {
        $this->removeEntity($childEntity);
    }
}
