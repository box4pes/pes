<?php
namespace Pes\Storage;

use Pes\Validator\ValidatorInterface;

/**
 * Description of StorageAbstract
 *
 * @author pes2704
 */
abstract class StorageAbstract implements StorageInterface, IteratorAggregate {
    
    /**
     * @var ValidatorInterface 
     */
    protected $keyValidator;

    /**
     * Přijímá validátor klíčů (např. IsArrayKeyValidator pro ukládání do pole)
     * @param ValidatorInterface $keyValidator
     */
    public function __construct(ValidatorInterface $keyValidator) {
        $this->keyValidator = $keyValidator;
    }
    
    protected function valueSerialize($value) {
        return \serialize($value);
    }
    
    protected function valueUnserialize($value) {
        //unserialize returns false in the event of an error and for boolean false.
        $unserstring =  \unserialize($value);
        if ($unserstring == false AND $value !== serialize(false)) {
            throw new UnexpectedValueException("Nepodařilo se deserializovat hodnotu. Hodnota nevznikla korektní serializací nebo je poškozena, zde je prvních 200 znaků hodnoty:"
                    .  substr(print_r($value, TRUE), 0, 200));       
        }        
        return $unserstring;
    }  
    
    public function getIterator() {
        return new ArrayIterator($this->arrayContent);
    }    
    
    
}
