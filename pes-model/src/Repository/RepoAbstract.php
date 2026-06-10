<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Repository;

use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Repository\RepoReadonlyInterface;

use Pes\Model\Dao\Exception\DaoKeyVerificationFailedException;
use UnexpectedValueException;


use Pes\Model\Hydrator\HydratorInterface;
use Pes\Model\Entity\PersistableEntityInterface;
use Pes\Model\RowData\RowDataInterface;
use Pes\Model\RowData\PdoRowData;
use Pes\Model\DataManager\DataManagerInterface;

use Pes\Model\Repository\Association\JoinOneToOneInterface;
use Pes\Model\Repository\Association\JoinOneToManyInterface;
use Pes\Model\Repository\Association\AssociationOneToOneInterface;
use Pes\Model\Repository\Association\AssociationOneToManyInterface;


use Pes\Model\Repository\Exception\BadReturnedTypeException;
use Pes\Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;
use Pes\Model\Repository\Exception\UnableRecreateEntityException;
use Pes\Model\Repository\Exception\BadImplemntastionOfChildRepository;
use Pes\Model\Repository\Exception\UnableAddEntityException;
use Pes\Model\Repository\Exception\OperationWithLockedEntityException;
use Pes\Model\Repository\Exception\UnableAddAssotiationsException;
use Pes\Model\Repository\Exception\UnableToDeleteAssotiatedChildEntity;

use Pes\Model\Dao\DaoWithReferenceInterface;

/**
 * Description of RepoAbstract
 *
 * @author
 */
abstract class RepoAbstract {

    public static $counter;
    protected $count;
    protected $oid;

    private $collection = [];
    private $new = [];
    private $removed = [];
    private $data = [];

    private $flushed = false;

    /**
     *
     * @var []
     */
    protected $associations = [];

    /**
     *
     * @var HydratorInterface[]
     */
    private $hydrators = [];

    /**
     * @var  DataManagerInterface
     */
    protected $dataManager;

    #### hydrator

    protected function registerHydrator(HydratorInterface $hydrator) {
        $this->hydrators[] = $hydrator;
    }

    protected function hydrate(PersistableEntityInterface $entity, RowDataInterface $rowData) {
        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrate($entity, $rowData);
        }
    }

    protected function extract(PersistableEntityInterface $entity, RowDataInterface $rowData) {
        /** @var HydratorInterface $hydrator */
        foreach ($this->hydrators as $hydrator) {
            $hydrator->extract($entity, $rowData);
        }
    }

    #### call

    private function callCreateEntity() {
        $entity = $this->createEntity();
        if (! $entity instanceof PersistableEntityInterface) {
            throw new BadReturnedTypeException("Protected method ".get_called_class()."->createEntity() must return instance of ".PersistableEntityInterface::class.", ". get_class($entity)." returned.");
        }
        return $entity;
    }

    protected function callIndexFromKeyParams(array $params) {  // číselné pole - vzniklo z variadic $params
        $index = $this->indexFromKeyParams($params);
        if (!is_int($index) AND !is_string($index)) {
            throw new BadReturnedTypeException("Protected method ".get_called_class()."->indexFromRow() must return integer or string, ". gettype($index)." returned.");
        }
        return $index;
    }

    /**
     * Protected pro volání z RepoAssocitedXXXTrait
     *
     * @param type $row
     * @return type
     * @throws BadReturnedTypeException
     */
    protected function callIndexFromRow($row) {
        $index = $this->indexFromRow($row);
        if (!is_int($index) AND !is_string($index)) {
            throw new BadReturnedTypeException("Protected method ".get_called_class()."->indexFromRow() must return integer or string, ". gettype($index)." returned.");
        }
        return $index;
    }

    protected function callIndexFromEntity($entity) {
        $index = $this->indexFromEntity($entity);
        if (!is_int($index) AND !is_string($index)) {
            throw new BadReturnedTypeException("Protected method ".get_called_class()."->indexFromEntity() must return integer or string, ". gettype($index)." returned.");
        }
        return $index;
    }

    //TODO: SV a) metoda indexFromEntity($entity) -> do entit metodu getIndex() a v repo abstract volej $entity->getIndex().b) metoda indexFromRow($row) -> v repo abstract použij DAO->getPrimaryKeyAttributes() a row 
    //  - má to vadu, musí být stejné pořadí skládání v entity->getIndex a DAO->getPrimaryKeyAttributes() - asi by se dalo v entity abstract použít method hydrator a generovat skládání z dat DAO->getPrimaryKeyAttributes() (ze jen sloupců)
    // tak, že sepřevedou jména sloupců -> camelCase -> přidá se get a volá se metoda entity
    
    /**
     * Defaultní implementace indexFromKeyParams().
     *
     * Defaultní implementace je možná, protože příjímá jen typ array a spoléhá na to, že pořadí parametrů metody repo->get() je stejné jako
     * pořadí skládání indexu v metodách indexFromRow($row) a indexFromEntity($entity)
     *
     * @param array $params
     * @return type
     */
    protected function indexFromKeyParams(array $params) {  // číselné pole - vzniklo z variadic $params
        return implode($params);
    }

    #### get & find

    /**
     *
     * @param variadic $id
     * @return PersistableEntityInterface|null
     */
    protected function getEntity(...$id): ?PersistableEntityInterface {
        $index = $this->callIndexFromKeyParams($id);
        if (!isset($this->collection[$index]) AND !isset($this->removed[$index])) {
            $key = $this->createKeyFromPrimaryKeyAttributes($id);
            $this->recreateData($index, $key);
            $this->recreateEntity($index);
        }
        return $this->collection[$index] ?? NULL;
    }

    /**
     * Pro konkrétní metodu konkrétního repository
     * 
     * @param type $touplesToBind Pole dvojic jméno-hodnota pro UNIQUE sloupec nebo sloupce
     * @return PersistableEntityInterface|null
     */
    protected function getEntityByUnique($touplesToBind=[]): ?PersistableEntityInterface {
        // vždy čte data - neví jestli jsou v $this->data
        $rowData = $this->dataManager->getUnique($touplesToBind);
        return $this->recreateEntityByRowData($rowData);
    }

    /**
     * Pro konkrétní metodu konkrétního repository
     *
     * @param string $referenceName
     * @param type $referenceParams
     * @return PersistableEntityInterface|null
     */
    protected function getEntityByReference(string $referenceName, ...$referenceParams): ?PersistableEntityInterface {
        // vždy čte data - neví jestli jsou v $this->data
        $referenceKey = $this->createReferenceKey($referenceName, $referenceParams);
        $rowData = $this->dataManager->getByReference($referenceName, $referenceKey);
        return $this->recreateEntityByRowData($rowData);
    }

    /**
     * 
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkazu where
     * 
     * @return array
     */
    protected function findEntities($whereClause="", $touplesToBind=[]): array {
        return $this->recreateEntitiesByRowDataArray($this->dataManager->find($whereClause, $touplesToBind));
    }

    /**
     * Pro konkrétní metodu konkrétního repository
     *
     * @param string $referenceName Jméno reference definované v DAO (obvykle jméno rodičovské tabulky)
     * @param ...$referenceParams
     * @return iterable
     */
    protected function findEntityByReference(string $referenceName, ...$referenceParams): iterable {
        $referenceKey = $this->createReferenceKey($referenceName, $referenceParams);
        $rowDataArray = $this->dataManager->findByReference($referenceName, $referenceKey);
        return $this->recreateEntitiesByRowDataArray($rowDataArray);
    }

    private function createReferenceKey($referenceName, array $referenceParams): array {
        /** @var DaoWithReferenceInterface $this->dataManager */
        $refAttribute = $this->dataManager->getReferenceAttributes($referenceName);
        $key = array_combine(array_keys($refAttribute), $referenceParams);
        if ($key===false) {
            $daoCls = $daoCls($this->dataManager);
            throw new UnexpectedValueException("Nelze vytvořit referenci pro volání Dao. Počet parametrů předaných metodě typu getByReference() neodpovídá počtu polí reference se jménem $referenceName v DAO $daoCls.");
        }
        return $key;
    }

    /**
     * Přidá entity na základě pole Rowdata objektů.
     *
     * Metoda je vhodná pro případy, kdy jsou načtena z úložiště současně data pro rodičovskou i potomkovskou entitu
     * - například pomocí SELECT FROM table1 JOIN table2
     *
     * @param array[RowDataInterface] $rowDataArray
     * @return array[PersistableEntityInterface]
     */
    protected function recreateEntitiesByRowDataArray($rowDataArray): array {
        $recteated = [];
        foreach ($rowDataArray as $rowData) {
            $nextEntity = $this->recreateEntityByRowData($rowData);  // může vracet null
            if (isset($nextEntity)) {
                $recteated[] = $nextEntity;
            }
        }
        return $recteated;
    }

    /**
     * Přidá entitu na základě RowData objektu.
     *
     * Metoda je vhodná pro případy, kdy jsou načtena z úložiště současně data pro rodičovskou i potomkovskou entitu
     * - například pomocí SELECT FROM table1 JOIN table2
     *
     * @param RowDataInterface $rowData
     * @return PersistableEntityInterface
     */
    protected function recreateEntityByRowData(?RowDataInterface $rowData=null) {
        if (!isset($rowData)) {
            return null;
        }
        $index = $this->callIndexFromRow($rowData);
        if (!isset($this->collection[$index]) AND !isset($this->removed[$index])) {
            $this->addData($index, $rowData);  // natvrdo dá rowData do $this->data
            $this->recreateEntity($index, $rowData);
        }
        return $this->collection[$index] ?? null;
    }

    private function createKeyFromPrimaryKeyAttributes(array $idParams): array {
        $key = array_combine($this->dataManager->getPrimaryKeyAttributes(), $idParams);
        if ($key===false) {
            $daoCls = get_called_class($this->dataManager);
            throw new UnexpectedValueException("Nelze vytvořit primární klíč pro volání Dao. Počet parametrů předaných metodě typu get() neodpovídá počtu polí primárního klíče $daoCls.");
        }
        return $key;
    }


#### entity - recreate, add, remove

    /**
     *
     * @param string $index
     * @return string|null
     * @throws UnableRecreateEntityException
     */
    private function recreateEntity($index) {
        if($this->hasData($index)) {
            $entity = $this->callCreateEntity();  // definována v konkrétní třídě - adept na entity managera
            try {
                $this->recreateAssociations($entity, $this->data[$index]);
            } catch (UnableToCreateAssotiatedChildEntity $unex) {
                $eCls = get_class($entity);
                throw new UnableRecreateEntityException("Chyba recreate entity v repository ". get_called_class()." Entitě $eCls s indexem $index nelze obnovit agregovanou (vnořenou) entitu.", 0, $unex);
            }
            $this->hydrate($entity, $this->data[$index]);
            $entity->setPersisted();
            $this->collection[$index] = $entity;
            $this->flushed = false;
        }
    }

    /**
     * Pomocí Asotiation objektů a potomkovských repository získá asociované entity.
     *
     * @param PersistableEntityInterface $parentEntity
     * @param RowDataInterface $parentRowData
     * @return void
     * @throws BadImplemntastionOfChildRepository
     */
    private function recreateAssociations(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void {
        foreach ($this->associations as $association) {
            if ($association instanceof AssociationOneToManyInterface) {
                /** @var AssociationOneToManyInterface $association */
                $association->recreateChildEntities($parentEntity, $parentRowData);
            } elseif($association instanceof AssociationOneToOneInterface) {
                /** @var AssociationOneToOneInterface $association */
                $association->recreateChildEntity($parentEntity, $parentRowData);
            } elseif($association instanceof JoinOneToOneInterface) {              //TODO: nnexistuje typ JoinOneToOneInterface asociace!!
                /** @var JoinOneToOneInterface $association */
                $association->recreateChildEntity($parentEntity, $parentRowData);
            } else {
                $cls = get_class($association);
                throw new BadImplemntastionOfChildRepository("Neznámý typ zaregistrované asociace '$cls'.");
            }
        }
    }

    /**
     *
     * @param PersistableEntityInterface $entity
     * @return void
     * @throws OperationWithLockedEntityException
     * @throws UnableAddEntityException
     */
    protected function addEntity(PersistableEntityInterface $entity): void {
        if ($entity->isLocked()) {
            throw new OperationWithLockedEntityException("Nelze přidávat přidanou nebo smazanou entitu.");
        }
        if ($entity->isPersisted()) {
            $this->collection[$this->callIndexFromEntity($entity)] = $entity;   //TODO: pravděpodobně nadbytečná operace - persisted entita už je c collection
        } else {
            if ($this->dataManager instanceof DaoEditAutoincrementKeyInterface) {
                // okamžité uložaní a zpetné načtení z databáze
                $rowData = $this->createRowData();
                $this->extract($entity, $rowData);
                $this->dataManager->insert($rowData);
                $entity->setPersisted();
                $this->hydrate($entity, $rowData);  //získá hodnotu klíče
                $index = $this->callIndexFromEntity($entity);  // z hodnoty klíče
                $this->collection[$index] = $entity;
                $this->addData($index, $rowData);  // natvrdo dá rowData do $this->data
            } elseif ($this->dataManager instanceof DaoEditKeyDbVerifiedInterface ) {
                // okamžité uložení skontrolou duplicity primárního klíče (výjimka)
                $rowData = $this->createRowData();
                $this->extract($entity, $rowData);
                try {
                    $this->dataManager->insert($rowData);
//                    $this->addNonpersistedAssociatedEntities($rowData);
                    $entity->setPersisted();
                    $index = $this->callIndexFromEntity($entity);
                    $this->collection[$index] = $entity;
                    $this->addData($index, $rowData);  // natvrdo dá rowData do $this->data
                } catch ( DaoKeyVerificationFailedException $verificationFailedExc) {
                    throw new UnableAddEntityException('Entita má nastavenu hodnotou klíče, která již v databázi existuje, nelze zapsat do databáze.', 0, $verificationFailedExc);
                }
            } else {
                $this->new[] = $entity;
                $entity->lock();
            }
        }
        $this->addNonpersistedAssociatedEntities($entity);
        $this->flushed = false;
    }

    /**
     *
     * @param type $parentEntity Agregátní entita.
     * @throws \LogicException
     */
    private function addNonpersistedAssociatedEntities(PersistableEntityInterface $parentEntity) {
        foreach ($this->associations as $association) {
            if ($association instanceof AssociationOneToManyInterface) {
                $association->addEntities($parentEntity);
            } elseif($association instanceof AssociationOneToOneInterface) {
                $association->addEntity($parentEntity);
            } else {
                throw new \LogicException("Neznámý typ asociace pro $interfaceName");
            }
        }
    }

    /**
     *
     * @param PersistableEntityInterface $entity
     * @return void
     * @throws OperationWithLockedEntityException
     */
    protected function removeEntity(PersistableEntityInterface $entity): void {
        if ($entity->isLocked()) {
            throw new OperationWithLockedEntityException("Nelze mazat přidanou nebo smazanou entitu.");
        }
        if ($entity->isPersisted()) {

            try {
                $this->removePersistedAssociatedEntities($entity);
            } catch (UnableToDeleteAssotiatedChildEntity $unex) {
                $eCls = get_class($entity);
                throw new UnableRecreateEntityException("Chyba remove entity v repository ". get_called_class()." Entitě $eCls s indexem $index nelze odebrat a smazat agregovanou (vnořenou) entitu.", 0, $unex);
            }


            $index = $this->callIndexFromEntity($entity);
            $this->removed[$index] = $entity;
            unset($this->collection[$index]);
            $entity->lock();
        } else {   // smazání nepersistované entity před uložením do db
            foreach ($this->new as $key => $new) {
                if ($new === $entity) {
                    unset($this->new[$key]);
                }
            }
        }
        $this->flushed = false;
    }

    /**
     *
     * @param string $entityInterfaceName
     * @param type $parentEntity Entita nebo null. Asociovaná entita (vrácená pomocí cizího klíče) nemusí existovat.
     */
    protected function removePersistedAssociatedEntities($parentEntity) {
//    protected function removeAssociated($row, PersistableEntityInterface $entity) {
        foreach ($this->associations as $association) {
            if ($association instanceof AssociationOneToManyInterface) {
                $association->removeEntities($parentEntity);
            } elseif($association instanceof AssociationOneToOneInterface) {
                $association->removeEntity($parentEntity);
            } else {
                throw new \LogicException("Neznámý typ asociace pro $index");
            }
        }
    }

    #### data

    private function createRowData() {
        return new PdoRowData();
    }

    /**
     * Vrací data se tadaným indexem. Pokud data se zadaným indexem nejsou nastavena, načte je pomocí data manahera (DAO).
     *
     * @param type $index
     * @param array $id
     * @return type
     */
    private function recreateData($index, array $id) {
        if (!$this->hasData($index)) {
            $rowData = $this->dataManager->get($id);
            if(isset($rowData)) {
                $this->addData($index, $rowData);
            }
        }
        return $this->data[$index] ?? null;
    }

    private function hasData($index) {
        return array_key_exists($index, $this->data);
    }

    private function addData($index, ?RowDataInterface $rowData = null) {
        $this->data[$index] = $rowData;
    }

    #### flush

    public function flush(): void {
        if ($this->flushed) {
            return;
        }


        if ( !($this instanceof RepoReadonlyInterface)) {
            /** @var \Pes\Model\Entity\PersistableEntityAbstract $entity */
            foreach ($this->collection as $index => $entity) {
                if (!$entity->isPersisted()) {
                    throw new \LogicException("V collection je nepersistovaná entita.");
                }
                $this->addNonpersistedAssociatedEntities($entity);
            }
            foreach ($this->removed as $index => $entity) {
                $this->removePersistedAssociatedEntities($entity);
            }
            $this->flushChildRepos();
//            if ( ! ($this->dataManager instanceof DaoEditKeyDbVerifiedInterface)) {   // DaoKeyDbVerifiedInterface musí ukládat (insert) vždy již při nastavování hodnoty primárního klíče
                foreach ($this->new as $entity) {
                    $rowData = $this->createRowData();
                    $this->extract($entity, $rowData);
                    $this->dataManager->insert($rowData);
//                    $this->addNonpersistedAssociatedEntities($rowData);
                    $entity->setPersisted();
                    $entity->unlock();
                }
                $this->new = []; // při dalším pokusu o find se bude volat recteateEntity, entita se zpětně načte z db (včetně případného autoincrement id a dalších generovaných sloupců)
//                $this->flushChildRepos();  //pokud je vnořená agregovaná entita - musí se provést její insert
//            }

            foreach ($this->collection as $index => $entity) {
                $rowData = $this->data[$index];
                $this->extract($entity, $rowData);
                if ($rowData->isChanged()) {
                    $this->dataManager->update($rowData);
                }
            }
            $this->collection = [];

            foreach ($this->removed as $index => $entity) {
                $this->extract($entity, $this->data[$index]);
                $this->dataManager->delete($this->data[$index]);
                $entity->setUnpersisted();
                $entity->unlock();
                unset($this->data[$index]);
            }
            $this->removed = [];

        } else {
            // $this je reaonly, ale child repo nemusí být
            $this->flushChildRepos();
            if ($this->new OR $this->removed) {
                $cls = get_called_class();
                throw new \LogicException("Repostory $cls je read only a byly do něj přidány nebo z něj smazány entity.");
            }
        }
        // po provedení flush jsou data prázdná -> pokud ve stejném běhu skriptu volám repo->get() to zajistí, že dojde ke čtení databáze
        $this->data = [];
        $this->flushed = true;
    }

    private function flushChildRepos() {
        foreach ($this->associations as $association) {
            $association->flushChildRepo();
        }
    }

    public function __destruct() {
        $this->flush();
    }


}
