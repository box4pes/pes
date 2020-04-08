<?php

namespace Pes\Storage;

/**
 * Třída pro ukládání informací do session. Jedná se wrapper pro pole $SESSION, informace jsou ukládány do pole $_SESSION 
 * poskytovaného PHP. Třída tak využívá standartní mechanizmus PHP pro session. Instance třídy je singleton a je vytvářena jen jedna pro jednu
 * relaci (session).
 *
 * @author pes2704
 */
class SessionStorage extends StorageAbstract {

    const DEFAULT_SESSION_NAME = 'SESSION_STORAGE';
    
    /**
     * Statická proměnná. Pokud je nastavena nelze již instancovat objekt Framework_Storage_Session.
     * @var type 
     */
    private static $session;

    /**
     * Factory metoda třídy. Instance třídy je singleton a je vytvářena jen jedna pro jednu
     * relaci (session).
     * @return \self
     */
    public static function getInstance() {
        if (self::$session) {
            return self::$session;
        } else {
            return new self;
        }
    }
    
    /**
     * Konstruktor SessionStorage je privátní. Objekt SessionStorage je singleton, je vytvářen factory metodou getInstance()
     * @param type $sessionName
     */
    private function __construct() {
            self::$session = $this;
    }

    /**
     * Metoda přečte a vrátí hodnotu uloženou pod daným jménem (identifikátorem).
     * @param string $name Jméno (identifikátor) hodnoty 
     * @return mixed/null
     */
    public function get($name) {
        if (session_status()==PHP_SESSION_NONE) {
            session_start (); 
        }
        $index = $this->checkKeyValidity($name);
        if (isset($_SESSION[$index])) {
            $value = $this->valueUnserialize($_SESSION[$index]);
            return $value;
        } else {
            return FALSE;            
        }
    }

    /**
     * Metoda uloží zadanou hodnotu pod jménem (identifikátorem). Metoda vrací poslední uloženou hodnotu
     * @param string $name Jméno (identifikátor)
     * @param mixed $value Hodnota musí být serializovatelná.
     * @return mixed
     */
    public function set($name, $value) {
        if (session_status()==PHP_SESSION_NONE) {
            session_start (); 
        }
        $index = $this->checkKeyValidity($name);       
        $_SESSION[$index] = $this->valueSerialize($value);
        return $_SESSION[$index];
    }

    /**
     * Metoda odstraní (unset) hodnotu ze session.
     * @param type $name Jméno (identifikátor) hodnoty
     * @return mixed Vrací NULL.
     */
    public function remove($name) {
        $index = $this->checkKeyValidity($name);  
        if (isset($_SESSION[$index])) {
            unset($_SESSION[$index]);            
        }
        return NULL;
    }
    
    /**
     * Destruktor
     * Zavře session, nemaže proměnné session. Session je tak možné obnovit požději za běhu skriptu, 
     * například v jiném destruktoru.
     */
    public function __destruct() {
        session_write_close(); 
    }
}

?>
