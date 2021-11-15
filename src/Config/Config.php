<?php

namespace Pes\Config;

/**
 * Třída vytváří objekt s načtenou konfigurací. Konfiguraci načítá ze souboru ve formátu xml.
 * Soubor s konfigurací obsahuje jednotlivé elementy - sekce konfigurace. Kořenová úrověň obsahuje pouze element <root>,
 * první úroveň pak jednotlivé elemty sekcí, např. <directories> nebo <db>. Pokud se některá sekce konfigurace má vyskytovat
 * více než jednou, budou všechny tyto sekce konfigurace obsaženy v elementu se stejným tagem a je nutné jednotlivé sekce
 * odlišit atributem. Např. <db database='Projektor'> a <db database='InformationSchema'>.
 */
class Config {
    /**
     *
     * @var \SimpleXMLElement
     */
    private $xml;

    private $sectionObjects;

    /**
     *
     * @var string
     */
    private $xmlFullFileName;

    public function __construct($xmlFullFileName) {
        $this->xmlFullFileName = $xmlFullFileName;
        $this->sectionObjects = new \stdClass();
        if (!is_readable($this->xmlFullFileName)) {
            throw new \LogicException('Není dostupný zadaný soubor s konfigurací '.$this->xmlFullFileName);
        }
        //$xmlObj = simplexml_load_string($xmlStr); použití této metody způsobuje chybu při použití xdebug - error socket
        $this->xml = new \SimpleXMLElement(file_get_contents($this->xmlFullFileName));
        if (!$this->xml) {
            throw new \RuntimeException("Nevznikl objekt SimpleXMLElement. Pravděpobně chybná syntaxe xml v souboru '{$this->xmlFullFileName}'.");
        }
    }

    /**
     * Metoda vrací objekt s konfiguračními informacemi obsaženými v jednom elementu xml konfiguračního souboru.
     * Elemnt vyhbírá podle jména elementu a případně podle nepovinných parametrů jméno a hodnoty atributu elementu.
     * Polde jméno a hodnoty atributu elementu lze element hledat při výskytu více lementů stejného jména v xml konfiguraci.
     *
     * @param string $elementName Název elementu (jm=no xml elementu)
     * @param string $atributeName Název atributu pažadovaného elementu
     * @param string $atributeValue Hodnota atributu požadovaného elementu
     * @return \stdClass
     * @throws \UnexpectedValueException Konfigurace obsahuje více elementů...
     * @throws \UnexpectedValueException Konfigurace eoobsahuje element...
     */
    public function getElement($elementName, $atributeName='', $atributeValue='') {
        $query ="//".$elementName;
        if ($atributeName) {
            $query .="[@$atributeName='$atributeValue']";
        }
        $array = $this->xml->xpath($query); // arrays of SimpleXMLObject nebo FALSE
        if ($array){
            if (count($array)==1) {
                return $this->prevedSimpleXMLObjectNaObjekt($array[0]);
            } else {
                throw new \UnexpectedValueException("Konfigurace obsahuje více elementů '$elementName' s atributem '$atributeName' a hodnotou '$atributeValue'.");
            }
        } else {
            throw new \UnexpectedValueException("Konfigurace eoobsahuje element '$elementName' s atributem '$atributeName' a hodnotou '$atributeValue'.");
        }
    }

    public function queryElement($xpathQuery) {
        $array = $this->xml->xpath($xpathQuery);
        return $this->prevedSimpleXMLObjectNaObjekt($array);
    }

    /**
     * Metoda vrací jednu sekci konfigurace. Sekce jsou pouze xml elementy v první úrovni pod keřenovým elementem.
     * Jméno sekce je jméno elementu. Sekce jednoho jména smí být v xml inicilizačním souboru pouze jedna.
     *
     * Metoda sekci vrací jako standartní php objekt (instanci stdClass) s jednotlivými vlastnostmi odpovídajícími položkám
     * v nejvyšší úrovni sekce. Pokud sekce má do hloubky více úrovní, je vlastnost objektu vždy znovu instance stdClass rekurzivně.
     * Pokud v úrovnije více elementá stejného jména, je vlastností pole objketů stdClass.
     *
     * @param string $section Název sekce (jméno xml elementu sekce)
     * @return \stdClass
     * @throws \UnexpectedValueException Sekce konfigurace je v souboru obsažena vícekrát než jednou.
     * @throws \UnexpectedValueException Sekce konfigurace není v souboru obsažena.
     */
    public function getSection($section) {
        if (! isset($this->sectionObjects->$section)) {
            if (property_exists($this->xml, $section)){
                if ($this->xml->$section->count()==1) {
                    return $this->prevedSimpleXMLObjectNaObjekt($this->xml->$section);
                } else {
                    throw new \UnexpectedValueException('Sekce konfigurace '.$section.' je v souboru obsažena vícekrát než jednou.');
                }
            } else {
                    throw new \UnexpectedValueException('Sekce konfigurace '.$section.' není v souboru obsažena.');
            }
            $this->sectionObjects->$section = $this->getSectionFromIni($section);
        }
        return $this->sectionObjects->$section;
    }

    /**
     * Rekurzivně převede SimpleXMLElement na jednoduchý objekt StdObj
     * @param type $simpleXMLObject
     * @return \stdClass
     */
    private function prevedSimpleXMLObjectNaObjekt($simpleXMLObject) {
        if (is_object($simpleXMLObject)) {
            $simpleXMLObject = get_object_vars($simpleXMLObject);
        }

        if (is_array($simpleXMLObject)) {
            foreach ($simpleXMLObject as $index => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = $this->prevedSimpleXMLObjectNaObjekt($value); // rekurze
                }
                if (!isset($objekt)) $objekt = new \stdClass ();
                $objekt->$index = $value;
            }
        }
        return $objekt ?? NULL;
    }
}
