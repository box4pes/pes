<?php

namespace Pes\Model\RowData;

/**
 * Description of PdoRowData
 * Objekt reprezentuje položku relace (řádek dat db tabulky). Objekt je určen pro uložená dat načtených pomocí PDOStatementu.
 *
 * Výchozí data objektu se nastavují pomocí magiské metody __set(). V konstruktoru objektu se však nastavuje chování objektu - potomka \ArrayObject tak,
 * že pro zápis dat (jako položek pole i jako vlastostí objektu) je výhradně používána metoda offsetSet(). To znamená, že magickou metodu __set() je možné volat
 * pouze před voláním konstruktoru a pak už ne.
 * PdoStatement je nutno nastavit tak, aby vracel objekt PdoRowData a zachovat defaultní nastavení PDOStatementu, kdy PDOStatement nejdříve nastavuje vlastnosti
 * vytvářeného objektu a teprve potom volá konstruktor objektu ( $statement->setFetchMode(\PDO::FETCH_CLASS, PdoRowData::class); ).
 * Pak proběhne nastavení výchozích dat objektu pomocí magické metody __set() a všechna další měněná a zapisovaná data jsou již ukládána pomocí metody offsetSet().
 * Data jsou objektem ukládána pouze pokud byla změněna proti výchozím datům. Objekt pak poskytuje metodu pro vrácení pouze změněných dat pro účel zápisu do databáze.
 *
 * @author pes2704
 */
class PdoRowData extends RowData {


    /**
     * V kostruktoru se mastaví způsob zapisu dat do PdoRowData objektu na ARRAY_AS_PROPS. Od té chvíle jsou data zapisována metodou offsetSet()
     * a to znamená, že jsou vždy změněná nebo nová data uložena do interního pole changed, nikdy do vlastní storage ArrayObjectu. Data do storage ArrayObjectu
     * se zapíší před voláním konstruktoru a tedy před nastavením způsobu zapisu dat do RowData objektu na ARRAY_AS_PROPS. To dokáže PDOStatement, který dafaultně nejprve nastavuje
     * vlastnosti objektu a teprve potom volá konstruktor.
     *
     */
    public function __construct() {
        $this->setFlags(\ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Magický setter je volán jen před nastavením setFlags(\ArrayObject::ARRAY_AS_PROPS). Nastavení setFlags(\ArrayObject::ARRAY_AS_PROPS) je provedeno
     * v konstruktoru. To znamená, že pokud je zavolán konstruktor, nevolá se už pak nikdy __set(), ale offsetSet(). Magická metoda __set() je volána
     * pouze objektem PDOStatement s nastavením: $statement->setFetchMode(\PDO::FETCH_CLASS, 'RowData'); volání $stmt->fetch(); pak nejdříve nastavuje properties RowDAta
     * objektu a v tu chvíli je volána __set(), pak teprve zavolá konstruktor. V konstruktoru RowData se nastaví $this->setFlags(\ArrayObject::ARRAY_AS_PROPS);
     * a od té chvíle pak každé další volání (jak RowData $data[$index], tak i $data->index) volá metodu offsetSet().
     *
     *
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value) {
        parent::forcedSet($name, $value);
    }
}
