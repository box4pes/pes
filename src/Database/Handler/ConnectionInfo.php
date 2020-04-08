<?php

/*
 * Objektový typ pro údaje o připojení k db.
 */

namespace Pes\Database\Handler;

use Pes\Database\Handler\DbTypeEnum;

/**
 * Description of ConnectionInfo
 *
 * @author pes2704
 */
final class ConnectionInfo implements ConnectionInfoInterface, \Serializable {

    private $dbNick;
    private $dbType;
    private $dbName;
    private $dbHost;
    private $dbPort;
    private $charset;
    private $collation;

    use SecurityContextObjectTrait;

    /**
     * Všechny vlastnosti objektu jsou zadány jako instanční proměnnné do konstruktoru. Nelze je později měnit, třída neobsahuje settery.
     * Pro získání hodnot vlastností třída používá gettery, výjimkou je vlastnost pass, která z bezpečnostních důvodů getter nemá.
     * <p><b>charset a collation</b><br>
     * Některé parametry mají defaultní hodnoty, které předpokládají předávání dat v kódování utf8 a s řazením utf8_czech_ci.
     * Pro jiné kódování a řazení je třeba zadat příslušné hodnoty, zadání NULL způsobí použití default hodnot databáze nebo MySQL aplikace.
     * Pokud $charset nebo $collation jsou nastaveny na NULL, použije MySQL defaultní hodnoty pro konkrétní databázi nastavené při vytváření
     * databáze např.:</p>
     * <pre>
     * CREATE DATABASE mydb DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
     * </pre>
     * nebo defaultní hodnotu pro MySQl aplikaci nastavené v konfiguraci např.:
     * <pre>
     *  [mysqld]
     *  character-set-server=utf8
     *  collation-server=utf8_general_ci
     * </pre>
     * <p><b>port</b><br>
     * Nepovinný parametr, pokud není zadán nikde v handleru se nepoužije a MySQL driver sám použije default (well known) port 3306.
     * Pokud chceš používat jiný port, musí se vždy jako parametr dbHost použít IP adresa. Např. nesmí být host zadán jako "localhost", musí být 127.0.0.1</p>
     * <p><b>dbName</b><br>
     * Parametr $dbName je skutečné aktuální jméno databáze a je nepovinný. Pokud databáze dosud neexistuje je parametr prázdný.
     * Připojení vytvořené bez jména databáze je třeba např. pro vytvoření nové databáze  příkazem CREATE DATABASE.
     * Nelze však použít postup:
     * new ConnectionInfo bez dbName -> new Handler(ConnectionInfo) -> CREATE DATABASE dabaname ->USE dabaname -> ConnectionInfo->setDbName(dabaname).
     * Namísto toho je třeba spojení zahodit a vytvořit nové s použitím nového ConnectionInfo, již obsahujícího dbName. Toto nové připojení by obvykle
     * mělo mít také jiné parametry user a pass, protože práva pro vytváření databází budou asi jiná než práva ke konkrétní nové databázi.</p>
     *
     * @param string $dbType Typ databáze jako hodnota výčtového typu Pes\Type\DbTypeEnum
     * @param string $dbHost IP adresa nebo doménové jméno hostitelského stroje. Při použití parametru dbPort je nutné použít IP adresu.
     * @param string $dbName Nepovinný parametr skutečné aktuální jméno databáze. Pokud databáze dosud neexistuje je parametr prázdný.
     * @param string $charset Nepovinný parametr, zadání NULL způsobí použití default hodnoty databáze nebo MySQL aplikace
     * @param string $collation Nepovinný parametr, zadání NULL způsobí použití default hodnoty databáze nebo MySQL aplikace
     * @param integer $dbPort Nepovinný parametr, zadání NULL způsobí, že driver použije standartní port 3306.
     */
    public function __construct($dbType, $dbHost, $dbName=NULL, $charset = NULL, $collation = NULL, $dbPort=NULL) {
        $this->dbType = (new DbTypeEnum())($dbType);
        $this->dbHost = $dbHost;
        $this->charset = $charset;
        $this->collation = $collation;
        $this->dbName = $dbName;
        $this->dbPort = $dbPort;
    }

    public function getDbType() {
        return $this->dbType;
    }

    public function getDbName() {
        return $this->dbName;
    }

    public function getDbHost() {
        return $this->dbHost;
    }

    public function getDbPort() {
        return $this->dbPort;
    }

    public function getCharset() {
        return $this->charset;
    }

    public function getCollation() {
        return $this->collation;
    }
}
