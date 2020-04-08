<?php
/**
 * Zajišťuje autoloading tříd. Třída zajišťuje autoloading tříd odpovídajících deklaraci PSR-0.
 * <p>Předpokládá tuto strukturu složek:</p>
 * <pre>[Main]</pre>
 * <pre> |-[Package1]</pre>
 * <pre> |  |-[Subfolder1]</pre>
 * <pre> |     |-[Parent1]</pre>
 * <pre> |     |  |-Child1.php</pre>
 * <pre> |     |   -Child2.php</pre>
 * <pre> |      -Parent1.php</pre>
 * <pre>  -[Package2]</pre>
 * <pre>    |-Class1.php</pre>
 * <pre>     -Class2.php</pre>
 * <p>Main je nadřazená složka (například vendor) a v ní jsou složky jednotlivých package. Ve složkách package jsou 
 * podsložky v jedné či více úrovních a v nejnižší úrovni soubory. Nadřazená složka Main může a nemusí být použita. Pokud 
 * je použita, je třeba zadat relativní cestu do této složky jako druhý parametr konstruktoru (include path). 
 * Počínaje úrovní složek Package pracuje s názvy tříd odpovídajícími konvenci pojmenovávání tříd typu PEAR (PSR-0) nebo 
 * s třídami používajícími namespace (PSR-0).</p>
 * <p>Při užití pojmenovávání tříd typu PEAR musí názvy tříd mít syntaxi: Package_[Subfolder_]File
 * a odpovídající umístění třídy ve struktuře složek. Např. třída umístěná v package Package1, podsložce Subfolder1, pak 
 * podsložce Parent1 a v souboru Child1.php musí být pojmenována class Package1_Subfolder1_Parent1_Child1.</p>
 * <p>Při použití namespace musí namespace\subnamespace odpovídat struktuře složek. Třída z příkladu pak musí být v 
 * namespace Package1\Subfolder1\Parent1 a musí mít jméno Child1.</p>
 * <p>
 * Pro autoloading vnitřně používá spl_autoload. 
 * Objekt Autoloader_Autoloader používá nestatické metody a pro SPL autoload se registruje instance objektu (nikoli jen název třídy). 
 * Pro používání je tedy třeba vytvořit objekt příkazem new Autoloader_Autoloader().
 * </p>
 */
class Autoloader_AutoloaderOLD
{
    const SEPARATOR = "_";   //oddělovač v názvech tříd s pojmenování typu PEAR
    const NAMESPACE_SEPARATOR = '\\';

    //nefunguje DIRECTORY_SEPARATOR, PHP platform konstant - na Win notebooku mám linuxové PHP
    const DIR_SEPARATOR = '/';
    const DEFAULT_FILE_EXTENSION = '.php';
    
    const NO_NAMESPACE = 'NO_NAMESPACE';
    const PSR0 = 'PSR-0';
    const PSR4 = 'PSR-4';
    
    private $namespacePrefix;
    private $namespacePrefixLength;
    private $baseFolder;
    private $ps4infix;
    private $namespaceLength;
    private $mode;

    public function addInclude($param) {
        
    }
    
    /**
     * Konstruktor objektu Classes_Autoloader. 
     * 
     * Pokud není zadán parametr $namespace, autoload funguje pro libovolný namespace. Pokud je zadán, auloload funguje jen pro zadaný namespace.
     * 
     * @param string $namespacePrefix Společná (počáteční) část namespace. Pokud je zadána auloload funguje jen pro zadaný namespace.
     * @param string $baseFolder Cesta do kořenové složky, ve kterém jsou složky odpovídaící jednotlivým namespace.
     */
    public function addNamespace($namespacePrefix = NULL, $baseFolder = NULL)
    {
        spl_autoload_extensions(self::DEFAULT_FILE_EXTENSION);
        if ($namespacePrefix) {
            $this->namespacePrefix = $namespacePrefix;
            $this->namespacePrefixLength = strlen($this->namespacePrefix.self::NAMESPACE_SEPARATOR);
            if ($psr4namespaceInfix) {
                $this->ps4infix = $psr4namespaceInfix;
                $this->mode = self::PSR4;
            } else {
                $this->mode = self::PSR0;
            }
        } elseif (isset($psr4namespaceInfix)) {
            throw new UnexpectedValueException('Nebyla zadána počáteční část namespace a byla zadána PSR-4 vložená část namespace. Nelze použít PSR-4 vloženou část namespace bez nastavení počáteční části namespace.');
        } else {
            $this->mode = self::NO_NAMESPACE;
        }
        $this->baseFolder = $baseFolder;
    }

    /**
     * Vrací nastavené namespace.
     * @return string Namespace
     */
    public function getNamespace() {
        return $this->namespacePrefix;
    }
        
    /**
     * Nastaví namespace.
     * @param string $namespace Namespace
     */
    public function setNamespace($namespace) {
        $this->namespacePrefix = $namespace;
        return $this;
    }
    
    public function getPs4infix() {
        return $this->ps4infix;
    }

    public function setPs4infix($ps4infix) {
        $this->ps4infix = $ps4infix;
        return $this;
    }

    /**
     * Vrací cestu do kořenového adresáše, ve kterém jsou adresáře odpovídaící jednotlivým namespace.
     * @return string $includePath
     */
    public function getIncludePath()
    {
        return $this->baseFolder;
    }
    
    /**
     * Nastaví cestu do kořenového adresáše, ve kterém jsou adresáře odpovídaící jednotlivým namespace.
     * @param string $includePath
     */
    public function setIncludePath($includePath)
    {
        $includePath = trim($includePath);
        if ($includePath AND substr($includePath, -1)!=self::DIR_SEPARATOR) {
            $this->baseFolder = $includePath . self::DIR_SEPARATOR;
        } else {
            $this->baseFolder = $includePath;
        }
    }

    /**
     * Vrací nastavené typy (přípony) souborů, ze kterých autoloader provádí autoloading.
     * @return array Pole přípon souborů včetně úvodní tečky.
     */
    public function getFileExtensions()
    {
//        $extensionsAsCommaSepatetedString = spl_autoload_extensions(); //vrací zaregistrované extensions jako řetězec extensions oddělených čárkou
        return explode(',', spl_autoload_extensions());
    }
    
    /**
     * Nastaví rozpoznávané typy souborů (t.j. přípony souborů), ze kterých autoloader provádí autoloading.
     * @param array $fileExtensions Pole přípon souborů včetně úvodní tečky.
     */
    public function setFileExtensions(array $fileExtensions)
    {
//        spl_autoload_extensions(".inc, .php, .lib, .lib.php ");  //zeregistruje rozpoznávané typy souborů (extension), extension obsahuje i tečku        
        spl_autoload_extensions(implode(',', $fileExtensions));
    }

    /**
     * Registruje objekt a jeho metodu 'autoload' v SPL autoloaderu.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }
    
    /**
     * Pokusí se načíst soubor s názvem složeným zleva z 
     * <ul>
     * <li>Cesty do kořenového adresáše, ve kterém jsou adresáře odpovídaící jednotlivým namespace - zadané při 
     * volání konstruktoru nebo metodou setIncludePath()</li>
     * <li>Cesty do adresáře se souborem - cesta je vytvořena překladem namespace. Namespace je zadán při volání 
     * konstruktoru nebo metodou setNamespace(). Překlad je vždy prováděn tak, že obrácená lomítka obsažená v namespace 
     * jsou nahrazena oddělovači složek (normální lomítka v systému Windows). Autoloader tedy předpokládá, tvar
     * namespace odpovídá struktuře složek obsažených v kořenové složce pro namespace složky.</li>
     * <li>Názvu souboru</li>
     * <li>Některé z nastavených přípon souborů odpovídajících rozpoznávaným typům souborů. Typ je použit výchozí nastavený konstantou
     * třídy DEFAULT_FILE_EXTENSION nebo jsou typy nastaveny metodou setFileExtensions(). Metoda vyzkouší postupně všechny nastavené typy
     * souborů (přípony).</li>
     * </ul>
     * @param string $className Jméno třídy
     */
    public function autoload($className)
    {
        switch ($this->mode) {
            case self::NO_NAMESPACE:


                break;
            case self::PSR0:
                $className = ltrim($className, '\\');
                $filePath  = '';
                $namespace = '';
                if ($lastNsSeparatorPosition = strrpos($className, '\\')) {
                    $namespace = substr($className, 0, $lastNsSeparatorPosition);
                    $className = substr($className, $lastNsSeparatorPosition + 1);
                    $filePath  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
                }
                $filePath .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

                require $filePath;

                break;
            case self::PSR4:


                break;            
            default:
                throw new LogicException('Chyba v implementaci - nesouhlasí autoload mode.');
                break;
        }
        if (!isset($this->namespacePrefix) OR $this->namespacePrefix.self::NAMESPACE_SEPARATOR === substr($className, 0, $this->namespaceLength)) {
            $lastNsSeparatorPosition = strripos($className, self::NAMESPACE_SEPARATOR);
            if ($lastNsSeparatorPosition === FALSE) {
                $filePath = '';
            } else {
                $nsPrefix = 
                $namespace = substr($className, 0, $lastNsSeparatorPosition);
                $className = substr($className, $lastNsSeparatorPosition + 1);
                $filePath = str_replace(self::NAMESPACE_SEPARATOR, self::DIR_SEPARATOR, $namespace) . self::DIR_SEPARATOR;
            }
            $fileName = $this->baseFolder . $filePath . str_replace(array('_', "\0"), array(self::DIR_SEPARATOR, ''), $className);
            foreach ($this->getFileExtensions() as $fileExtension) {
                $fileFullName = $fileName.$fileExtension;
                if (is_readable($fileFullName)) {
                    require_once $fileFullName;
                    return TRUE;
                }
            }
            return FALSE;
        }
    }
}
