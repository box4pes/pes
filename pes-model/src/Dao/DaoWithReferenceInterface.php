<?php
namespace Pes\Model\Dao;

/**
 *
 * @author pes2704
 */
interface DaoWithReferenceInterface extends DaoInterface {

    /**
     * Musí vracet pole jehož položky odpovídají polím cizího klíče potomkovské tabulky. Každá položka tohoto pole musí obsahovat
     * dvojice klíč=>hodnota tak, že klíč položky je jméno pole cizího klíče potomka a hodnota je jméno pole (vlastního) klíče rodiče:
     *
     * ['pole cizího klíče potomka (jméno sloupce v potomkovi)'=>'pole vlastního klíče rodiče (jméno sloupce v rodiči)']
     *
     * Příklad:
     * Pro definici (CREATE)
     * CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`login_name_fk`) REFERENCES `login` (`login_name`)
     * musí pro jméno reference 'login' vracet pole
     * ['login_name_fk'=>'login_name']
     * @param type $referenceName
     * @return array
     */
    public function getReferenceAttributes($referenceName): array;

//    public function getReferenceKeyTouples($referenceName, array $referenceParams): array;

}
