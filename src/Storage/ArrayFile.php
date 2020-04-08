<?php

namespace Pes\Storage;

use Pes\Validator\IsArrayKeyValidator;
use Pes\Utils\Directory;

/**
 * Třída pro ukládání informací do souboru.
 *
 * @author pes2704
 */
class ArrayFile extends StorageAbstract implements StorageInterface {
    
    /**
     * Default soubor
     */
    const DEFAULT_STORAGE_FILE = "Storage.file";
    /**
     * Default nově vytvářená složka
     */
    const DEFAULT_NEW_DIRECTORY = "Storage";
    
    private static $instances = array();

    private $fullStorageFileName;   
    
    /**
     * Pole pro uložení obsahu storage souboru
     * @var array 
     */
    protected $arrayContent = array();    

    /**
     * Privátní konstruktor. Objekt je vytvářen voláním factory metody getInstance().
     * 
     * @param type $fullStorageFileName
     * @throws \InvalidArgumentException
     */
    private function __construct($fullStorageFileName){
        parent::__construct();
        $this->fullStorageFileName = $fullStorageFileName;
        if (is_readable($this->fullStorageFileName)) {
            $handle = fopen($this->fullStorageFileName, 'r'); //readonly
            if ($handle == FALSE) {
                throw new \RuntimeException('Nelze otevřít existující soubor: '.$this->fullStorageFileNam);
            }
            $str = fread($handle, filesize($this->fullStorageFileName));
            $this->arrayContent = $this->valueUnserialize($str);
            if (!is_array($this->arrayContent)) {
                throw new \InvalidArgumentException('Nepodařilo se zřídit storage '. get_called_class().' ze souboru: '.$this->fullStorageFileName
                        .' Nepodařilo se obnovit obsah storage ze souboru.');
            }            
        } else {
            $this->arrayContent = [];
        }

    }

    final public function __clone(){}

    final public function __wakeup(){}

    /**
     * Factory metoda, metoda vrací instanci objektu třídy Framework_Storage_File. 
     * Objekt storage je vytvářen jako singleton vždy pro jeden soubor. Metoda vrací jeden unikátní 
     * objekt pro jednu kombinaci parametrů $storageDirectoryPath a $storageFileName.
     * 
     * @param string $storageDirectory Pokud parametr není zadán, třída loguje do složky, ve které je soubor s definicí třídy.
     * @param string $storageFileName Název logovacího souboru (řetězec ve formátu jméno.přípona např. Mujlogsoubor.log). Pokud parametr není zadán,
     *  třída loguje do souboru se jménem v konstantě třídy LOG_SOUBOR.
     * @return Framework_Storage_ArrayFile
     */
    public static function getInstance($storageDirectory=NULL, $storageFileName=NULL) {
        if (!$storageDirectory) {
            $storageDirectory = __DIR__."\\".self::DEFAULT_NEW_DIRECTORY."\\"; //složka Storage jako podsložka aktuálního adresáře
        }
        
        $storageDirectory = Directory::normalizePath($storageDirectory);
        Directory::createDirectory($storageDirectory);
        
        if (!$storageFileName) {
            $storageFileName = self::DEFAULT_STORAGE_FILE;
        }
        $fullStorageFileName = $storageDirectory.$storageFileName;
        if(!isset(self::$instances[$fullStorageFileName]) OR !self::$instances[$fullStorageFileName]){
            self::$instances[$fullStorageFileName] = new self($fullStorageFileName);
        }
        return self::$instances[$fullStorageFileName];
    }    

    /**
     * Metoda přečte a vrátí hodnotu uloženou pod daným klíčem (identifikátorem).
     * @param string $key Klíč (identifikátor) hodnoty 
     * @return mixed/null
     * @throws UnexpectedValueException
     */
    public function get($key) {
        if ($this->keyValidator->validate($key)) {
            return $this->arrayContent[$index] ?? NULL;
        }
    }

    /**
     * Metoda uloží zadanou hodnotu pod klíčem (identifikátorem).
     * @param string $key Klíč (identifikátor)
     * @param mixed $value Hodnota musí být skalární.
     * @return mixed/null
     * @throws UnexpectedValueException
     */
    /**
     * 
     * @param type $key
     * @param type $value
     * @return $this
     */
    public function set($key, $value) {
        if ($this->keyValidator->validate($key)) {
            $this->arrayContent[$key] = $value;
            return $this;
        } else {
            ;
        }
    }

    /**
     * Metoda odstraní (unset) hodnotu ze session.
     * @param type $key Klíč (identifikátor) hodnoty
     * @return mixed Výsledná hodnota v session. Pokud je metoda úspěšná vrací NULL.
     * @throws UnexpectedValueException
     */
    public function remove($key) {
        $index = $this->checkKeyValidity($key);
        unset($this->arrayContent[$index]);
    }

    /**
     * Destruktor. Zavře soubor.
     */
    public function __destruct() {
        $handle = fopen($this->fullStorageFileName, 'w'); //writeonly, smaže starý obsah
        fwrite($handle, $this->valueSerialize($this->arrayContent));
        if ($handle) {
            fclose($handle);
        }
    }  

}
