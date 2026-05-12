<?php
namespace Pes\Database\MysqlReplace;

use Database\MyMiniHandler\Dsn;
use Database\MyMiniHandler\HandlerFactory;
use Database\MyMiniHandler\Handler;

/**
 * Description of HandlerFactoryStatic. Třída obsahuje sadu statických metod. Metody pro vytvoření databázového připojení - handleru, metodu pro získání  
 * vytvořeného handleru a pro jeho smazání. Vytvořený handler ukládá do interní statické proměnné a z ní pak opakovaně vrací stejný jednou vytvořený handler.
 *
 * @author pes2704
 */
class HandlerFactoryStatic {
    /**
     *
     * @var Handler 
     */
    static $handler;
    
    static $dbHost;
    static $user;
    static $password;
    
    /**
     * Vrací uložený Handler
     * @return Handler
     */
    public static function get() {
        if (!isset(self::$handler)) {
            throw new \LogicException("Nebylo provedeno připojení k databázi voláním metody set() (a preset()).");
        }
        return self::$handler;
    }
    
    /**
     * Nastaví parametry připojení k  databázi
     * @param string $dbHost
     * @param string $user
     * @param string $password
     */
    public static function preSetConnection(string $dbHost, string $user, string $password) {
        self::$dbHost = $dbHost;
        self::$user = $user;
        self::$password = $password;
    }
    
    /**
     * Pokud jsou nastaveny potřební parametry pro přopojení k databázi vrací TRUE, jinak FALSE. Metoda nijak nekontroluje, 
     * jestli hodnoty parametrů umožňují provést sketečně úspěšné připojení k databázi.
     * 
     * @return bool
     */
    public static function isPresetted() {
        return isset(self::$dbHost) AND isset(self::$user) AND isset(self::$password);
    }
    
    /**
     * Metoda vytvoří databázové připojení a PDO handler, ten interně uloží a vrací TRUE. Pokud handler nevznikne, vrací FALSE.
     * @param string $dbName
     * @return bool
     * @throws \LogicException
     */
    public static function setDb(string $dbName) {
        if (!self::isPresetted()) {
            throw new \LogicException("Nebyla zavolána metoda preset(). Metodu preset() je nutné volat před voláním funkce set().");
        }
        $dsn = new Dsn(self::$dbHost, $dbName);
        self::$handler = (new HandlerFactory($dsn, self::$user, self::$password))->get(); 
        return isset(self::$handler);
    }
    
    /**
     * Metoda vymaže interné uložený databázový handler PDO vytvořený metodou setDB() a parametry připojení nastavené metodou preSetConnection().
     */
    public static function reset() {
        self::$dbHost = NULL;
        self::$user = NULL;
        self::$password = NULL;
        self::$handler= NULL;
    }
}
