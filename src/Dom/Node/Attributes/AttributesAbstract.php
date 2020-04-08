<?php

namespace Pes\Dom\Node\Attributes;

/**
 * Description of AttributesAbstract
 *
 * @author pes2704
 */
abstract class AttributesAbstract implements AttributesInterface {

    private $stringRepresentation;

    /**
     * Nastaví hodnoty atributů podle asociativního pole zadaného jako parametr.
     * V případě, že pole obsahuje prvek se jménem, které neodpovídá žádnému existujícímu atributu elementu metoda vyhodí uživatelskou chybu E_USER_NOTICE.
     */
    public function __construct(array $attributes=[]) {
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
        $this->stringRepresentation = '';
        // bez kontroly
//        foreach ($attributesArray as $key => $value) {
//            $this->$key = $value;
//        }
        // s kontrolou
        if ($attributesArray) {
            $definedAttributes = get_object_vars($this);
            foreach ($attributesArray as $key => $value) {
                $this->setDefinedAttribute($key, $value, $definedAttributes);
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
        $definedAttributes = get_object_vars($this);
        $this->setDefinedAttribute($key, $value, $definedAttributes);
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
     * - pokud má objekt atributu definovánu takovou vlastnost nebo
     * - pokud jméno atributu začíná na "data-"
     *
     * @param type $name
     * @param type $value
     * @param type $definedAttributes
     */
    private function setDefinedAttribute($name, $value, $definedAttributes) {
        if (array_key_exists($name, $definedAttributes) OR strpos($name, 'data-')===0) {
            $this->$name = $value;
        } else {
            user_error('Nelze nastavit nedefinovaný atribut. Atribut "'.$name.'" není v atributech '.get_called_class().' definován.', E_USER_NOTICE);
        }
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getString() {
        if (!$this->stringRepresentation) {
            foreach ($this->getIterator() as $key => $value) {    // 400mikrosec na jeden getString() a dvou klíčích atributu
                if ($value) {
                    if(is_bool($value)) {
                        $attr[] = $key;
                    } elseif (is_array($value)) {
                        $attr[] = $key.'="'.implode(' ', $value).'"';
                    } else {
                        $attr[] = $key.'="'.trim((string) $value).'"';
                    }
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

