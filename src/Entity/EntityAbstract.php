<?php
namespace Pes\Entity;

/**
 * In Domain Driven Design, an Entity is An object that is not defined by its attributes, but rather by a thread of continuity and its identity.
 * So for example, a user is an entity because you can change the user’s email address without changing the identity of the user. 
 * This is because the user has an id so even when you change the user’s attributes, it’s still the same user. So an Entity is basically 
 * just a unique object within the system that has identity. You can’t have two users with the same id and so users are Entities. An entity 
 * can be a user, an order, a product, a blog post, really anything with an identity.
 * 
 * Description of Projektor2_Model_RowModelAbstract
 *
 * @author pes2704
 */
abstract class EntityAbstract implements EntityInterface, \IteratorAggregate {
       
    /**
     * Setter, nastavuje jen hodnoty existujících public vlastností, zamezuje přidání další vlastnosti objektu. 
     * @param string $name
     * @param mixed $value
     * @throws LogicException
     */
    public function __set($name, $value=NULL) {
        if ($this->isPublicProperty($name)) {
            $this->$name = $value;
        } else {
            throw new LogicException('Nelze nastavovat neexistující nebo neveřejnou vlastnost '.$name.' objektu '.get_called_class().'.');
        }
    }
    
    /**
     * Metoda oznámí jestli je vlastnost se zadaným jménem public. Takovou vlastnost lze nastavovate metodou __set (nap $entity->vlastnost = $hodnota;)
     * a hodnotu a název takové vlastnosti vracejí metody getNames(), getValues(), getValuesAssoc a obsahuje ji iterátor vracený
     * metodou getIterator.
     * 
     * @param type $name
     * @return boolean
     */
    public function isPublicProperty($name) {
        return $this->getIterator()->offsetExists($name) ? TRUE : FALSE;
    }
    
    /**
     * Metoda vrací názvy public vlastností modelu v číselně indexovaném poli.
     * @return array
     */
    public function getNames() {
        return array_keys($this->getValuesAssoc());
    }

    /**
     * Metoda vrací hodnoty public vlastností modelu v číselně indexovaném poli.
     * @return array
     */    
    public function getValues() {
        return array_values($this->getValuesAssoc());
    }
    
    /**
     * Metoda vrací hodnoty a názvy public vlastností modelu jako asociativní pole.
     * @return array
     */    
    public function getValuesAssoc() {
        return call_user_func('get_object_vars', $this);  // vrací viditelné nestatické properties - v tomto případě tedy jen public properties objektu
    }
    
    /**
     * Metoda vrací iterátor obsahující vlastnosti objektu
     * @return \ArrayIterator
     */
    public function getIterator() {
       return new ArrayIterator($this->getValuesAssoc());
    }
}
