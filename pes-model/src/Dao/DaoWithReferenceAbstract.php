<?php
namespace Pes\Model\Dao;

use Pes\Model\Dao\DaoWithReferenceInterface;
use Pes\Model\Dao\Exception\DaoUnknownReferenceNameException;

/**
 * Description of DaoWithReferenceAbstract
 *
 * @author pes2704
 */
abstract class DaoWithReferenceAbstract extends DaoAbstract implements DaoWithReferenceInterface {
    /**
     *
     * @param string $referenceName jméno reference.
     * @param array $parentTouples Pole dvojic rodičovských dat
     * @return type
     */
    public function getByReference($referenceName, array $parentTouples) {
        $key = $this->getReferenceKeyTouples($referenceName, $parentTouples);
        return $this->getUnique($key);
    }

    /**
     * Vytvoří asocitaivní pole dvojic klíče z jmen polí potomka a hodnot rodičovských dat.
     *
     * @param type $parentTableName
     * @param array $parentTouples
     * @return array
     * @throws UnexpectedValueException
     */
    protected function getReferenceKeyTouples($parentTableName, array $parentTouples): array {
        /** @var DaoReferenceNonuniqueInterface $this */
        $fkAttribute = $this->getReferenceAttributes($parentTableName);
        if(!isset($fkAttributes)) {
            throw new DaoUnknownReferenceNameException("V DAO není definována reference se jménem $referenceName.");
        }
        $key = [];
        foreach ($fkAttribute as $childField=>$parentField) {
            if( ! array_key_exists($parentField, $parentTouples)) {
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Reference obsahuje na pozici rodiče pole '$parentField' a pole rodičovských dat pro vytvoření klíče neobsahuje prvek s takovým jménem.");
            }
            if (is_scalar($parentTouples[$parentField])) {
                $key[$childField] = $parentTouples[$parentField];
            } else {
                $t = gettype($parentTouples[$parentField]);
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Zadaný atribut klíče obsahuje v položce '$parentField' neskalární hodnotu. Hodnoty v položce '$parentField' je typu '$t'.");
            }
        }

        return $key;
    }
}
