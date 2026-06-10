<?php
namespace Pes\Model\Repository;

use Pes\Model\Entity\PersistableEntityInterface;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;

use Pes\Model\Repository\RepoAssotiatedOneInterface;  // použito jen v komentáři

use UnexpectedValueException;

/**
 * Trait s implementací RepoAssotiatedOneInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
trait RepoAssotiatedOneTrait {

    /** @var RepoAbstract $this */

    /**
     * Metoda získá potomkovskou entity z potomkovského repository pomocí reference. Hodnoty polí reference naplní z rodičovských dat.
     *
     * @param string $referenceName Jméno refernce z DAO
     * @param RowDataInterface $parentRowData Rodičovská data pro získání hodnot polí reference.
     * @return PersistableEntityInterface|null
     */
    public function getByParentData(string $referenceName, RowDataInterface $parentRowData): ?PersistableEntityInterface {
        $referenceKey = $this->createReferenceKeyFromParentData($referenceName, $parentRowData);
        $rowData = $this->dataManager->getByReference($referenceName, $referenceKey);
        return $this->recreateEntityByRowData($rowData);
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
