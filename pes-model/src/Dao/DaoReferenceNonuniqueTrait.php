<?php

namespace Pes\Model\Dao;

use Pes\Model\Dao\DaoReferenceNonuniqueInterface;

use Pes\Model\Dao\Exception\DaoUnknownReferenceNameException;
use UnexpectedValueException;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
trait DaoReferenceNonuniqueTrait {

    public function findByReference($referenceName, array $key): array {
        /** @var DaoReferenceUniqueInterface $this */
        return $this->findNonUnique($this->checkReferenceKey($referenceName, $key));

//        $select = $this->sql->select($this->getAttributes());
//        $from = $this->sql->from($this->getTableName());
//
//        $fkAttributes = $this->getFkAttribute($referenceName);
//        $whereTouples = $this->getReferenceKeyBinds($referenceName, $referenceTouples);
//        if ($this instanceof DaoContextualInterface) {
//            $where = $this->sql->where($this->sql->and($this->getContextConditions(), $whereTouples));
//        } else {
//            $where = $this->sql->where($this->sql->and($whereTouples));
//        }
//
//        $touplesToBind = $this->getTouplesToBind($referenceTouples);
//        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    /**
     * Validuje referenční klíč.
     *
     * @param type $referenceName
     * @param array $key
     * @return array
     * @throws UnexpectedValueException
     */
    private function checkReferenceKey($referenceName, array $key): array {
        /** @var DaoReferenceNonuniqueInterface $this */
        $fkAttribute = $this->getReferenceAttributes($referenceName);
        if(!$fkAttribute) {
            throw new DaoUnknownReferenceNameException("V DAO není definován atribut reference se jménem $referenceName.");
        }
        foreach (array_keys($fkAttribute) as $field) {
            if( ! array_key_exists($field, $key)) {
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Reference obsahuje na pozici rodiče pole '$field' a pole rodičovských dat pro vytvoření klíče neobsahuje prvek s takovým jménem.");
            }
            if (!is_scalar($key[$field])) {
                $t = gettype($key[$field]);
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Zadaný atribut klíče obsahuje v položce '$field' neskalární hodnotu. Hodnoty v položce '$field' je typu '$t'.");
            }
        }

        return $key;
    }
}
