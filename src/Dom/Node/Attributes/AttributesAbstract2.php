<?php

namespace Pes\Dom\Node\Attributes;

/**
 * Description of AttributesAbstract
 *
 * @author pes2704
 */
abstract class AttributesAbstract2 implements AttributesInterface {

    private $stringRepresentation;

    private $declaredAttributes=[];

    private $definedAttributes=[];

    /**
     * Nastaví hodnoty atributů podle asociativního pole zadaného jako parametr.
     * V případě, že pole obsahuje prvek se jménem, které neodpovídá žádnému existujícímu atributu elementu metoda vyhodí uživatelskou chybu E_USER_NOTICE.
     */
    public function __construct(array $attributes=[]) {
        // deklarované jsou všechny public vlastnosti = všechny atributy, které objekt může mít
        $this->declaredAttributes = get_class_vars(static::class);

        $this->addAttributesArray($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesArray() {
        return iterator_to_array($this->getIterator(), TRUE);
    }

    /**
     * {@inheritdoc}
     * V případě, že pole obsahuje prvek se jménem, které neodpovídá žádnému existujícímu atributu elementu metoda vyhodí uživatelskou chybu E_USER_NOTICE.
     */
    public function addAttributesArray($attributesArray=[]): AttributesInterface {
        // bez kontroly
//        $this->stringRepresentation = '';
//        foreach ($attributesArray as $key => $value) {
//            $this->$key = $value;
//        }
        // s kontrolou
        if ($attributesArray) {
            $this->stringRepresentation = '';
            foreach ($attributesArray as $key => $value) {
                $this->setDefinedAttribute($key, $value);
            }
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     * @param string $key
     * @param string $value
     */
    public function setAttribute($key, $value): AttributesInterface {
        $this->stringRepresentation = '';
        $this->setDefinedAttribute($key, $value);
        return $this;
    }

    public function getAttribute($name) {
        return $this->$name;
    }

    public function hasAttribute($name) {
        return isset($this->$name);
    }

    /**
     * Nastaví hodnotu vlstnosti atributu
     * - pokud má třída objektu atributu deklarovánu takovou vlastnost nebo
     * - pokud jméno atributu začíná na "data-"
     *
     * @param type $name
     * @param type $value
     * @param type $definedAttributes
     */
    private function setDefinedAttribute($name, $value) {
        if (array_key_exists($name, $this->declaredAttributes) OR strpos($name, 'data-')===0) {
            $this->$name = trim($value);
            $this->definedAttributes[] = $name;
        } else {
            user_error('Nelze nastavit nedefinovaný atribut. Atribut "'.$name.'" není v atributech '.get_called_class().' definován.', E_USER_NOTICE);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getString() {
        if (!$this->stringRepresentation) {
            foreach ($this->definedAttributes as $key) {    // 400mikrosec na jeden getString() a dvou klíčích atributu
                $value = $this->$key;
                if(is_bool($value)) {
                    $attr[] = $key;
                } else {
                    $attr[] = $key.'="'.$value.'"';
                }
            }
            $this->stringRepresentation = isset($attr) ? implode(' ', $attr) : '';
        }
        return $this->stringRepresentation;
    }

    public function __toString() {
        return $this->getString();
    }

    /**
     * Metoda vrací iterátor obsahující atributy, které mají nastenou hodnotu
     * @return \ArrayIterator
     */
    public function getIterator() {
        $defined = array();
        //TODO: foreach jed přes všechy vlastnosti!!!!!
        foreach (get_object_vars($this) as $key=>$val) {
            if ($val) {
                $defined[$key] = $val;
            }
        }
        return new \ArrayIterator($defined);  // vrací properties, které mají hodnotu
    }

    public function count(): int {
        return $this->getIterator()->count();
    }
}

