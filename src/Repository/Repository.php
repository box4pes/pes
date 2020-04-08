<?php
namespace Framework\Model;

/**
 * Description of Framework_Model_Repository
 * @author pes2704
 */
abstract class Repository {

    private $storage;
    
    public function __construct() {
        $this->storage = new \ArrayObject($array);
    }
    
    /**
     * 
     * @param type $object
     * @return void
     * @throws InvalidArgumentException
     */
    public function add($object) {
        if (is_a($object, $this->getEntityClass())) {
            if ($object instanceof Framework_Model_Persistable_ItemInterface) {
                //TODO: tady je spousta poznámek:
//                - metoda add() přidává jen nový, nepersistovaný objekt - takový nemá hodnotu id;
//                - vypadá to, že array bude pro storage lepší nápad - metoda get($id) je pak jednoduchá - u SplStorage musím použít vyhledávání, 
//                  naopak metoda add() neví kam dát objekt bez id.
//                - metoda get($id) načte z databáze jeden řádek?? pokud bude volána ve foreach bude volat jednotlivé selecty pro všechny řádky
//                - možná dodělat pro repository rozhranní iterator a v metodě rewind 
            } else {
                throw new LogicException('Tato repository '.  get_called_class($this).'je nastavena pro přijímání objektů typu '. $this->getItemClassName().
                        '. Tento typ není persostable ('.'Framework_Model_Persistable_ItemInterface'.').');
            }
            $this->storage->attach($object);
        } else {
            throw new InvalidArgumentException('Tato repository '.  get_called_class($this).'je určena pro přijímání objektů typu '. $this->getItemClassName().'.');
        }
    }
    
    /**
     * @return string Jméno třídy objektů, které múže repository přijímat metodou add()
     */
    public abstract function getEntityClass();
    
    /**
     * 
     * @param object $entity
     * @return void
     */
    public function remove($entity) {
        $this->storage->detach($object);
    }
    
    public function count() {
        $this->storage->count();
    }
    
    public function get($id) {
        return $this->searchById($id);
    }
    
    public function find(\Criteria $criteria) {
        ;
    }
    
    /**
     * Najde objekt dle id. Musí se jednat o bjekt implementující metodu ->getIdColumnName().
     * @return type
     */
    private function searchById() {
        $currentObject = $this->storage->current();
        if (!($currentObject instanceof Framework_Model_Persistable_ItemInterface)) {
            throw LogicException('Objekt v repository není typu Framework_Model_Db_ItemInterface, nemá unikátní identifikátor.');
        }
        $idPropertyName = $currentObject->getIdColumnName();
        return $this->searchOneByProperty($idPropertyName, $currentObject->$idPropertyName);
    }
   
    /**
     * Najde první výskyt objektu se zadanou hodnotou vlastnosti
     * @param type $propertyName
     * @param type $propertyValue
     */
    private function searchOneByProperty($propertyName, $propertyValue) {
        foreach ($this->storage as $object) {
            if ($object->$propertyName == $propertyValue) {
                return $object;
            }
        }
    }
            
            
}
