<?php

namespace Pes\Container;

use Pes\Container\Exception;

use Psr\Container\ContainerInterface;

/**
 * Description of Container
 *
 * @author pes2704
 */
class Container implements ContainerSettingsAwareInterface {

    const INTERFACE_NAME_POSTFIX = 'Interface';
    const INTERFACE_NAME_POSTFIX_LENGTH = -9;  //subst_compare a substr zprava

    /**
     * Kontejner, na který bude použit pokud tento kontejner neobsahuje požadovanou hodnotu.
     *
     * @var ContainerInterface
     */
    protected $delegateContainer;

    protected $useAutogeneratedInterfaceAliases;

    public $containerInfo;

    /**
     * Obsahuje již vytvořené instance objektů vytvořených voláním get($service).
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Aliasy ke jménu.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Pole generátorů - closure generovaných metodami set() a factory()
     * @var type
     */
    protected $generators = [];

    /**
     * Signalizuje uzamčený kontejner.
     *
     * @var boolean
     */
    private $locked;

    /**
     * Konstruktor.
     *
     * Jako parametr přijímá delegate kontejner, tedy kontejner, na který bude delegován požadavek na službu, pokud tento kontejner
     * službu se zadaným jménem neobsahuje. Dále přijímá bool parametr s dedaultní hodnotou TRUE, který určuje zda bude automaticky
     * použit alias pro jméno služby, které je názvem interface a exituje k ní obdobně pojmenovaná tčída.
     *
     * @param ContainerInterface $delegateContainer Kontejner, který bude vnořen jako delegát.
     * @param type $useAutogeneratedInterfaceAliases
     */
    public function __construct(ContainerInterface $delegateContainer = null, $useAutogeneratedInterfaceAliases=TRUE) {

        ### lock ###
        // container je nutné v okamžiku, kdy je použit jako delegate zamknout. Pokud by došlo k nastavení stejnojmenné služby v delegate i v delegujícím kontejneru
        // mohly by v různých částech aplikace vznikat voláním stejně pojmenované služby (service, nikoli factory) vzniknout dva různé objekty - což je nažádoucí a neočekávané chování
        if ($delegateContainer instanceof ContainerSettingsAwareInterface) {
            /** @var ContainerSettingsAwareInterface $delegateContainer */
            $delegateContainer->lock();
        }
        $this->delegateContainer = $delegateContainer;

        $this->useAutogeneratedInterfaceAliases = $useAutogeneratedInterfaceAliases;
    }

    /**
     * Nastaví kontejneru vlastnost jméno. Tato metoda slouží pouze pro ladění - umožňuje udžet si přehled, ve kterém konteneru se hledá služba
     * i v případě použití více zanořených delegete kontejnerů
     * @param string $containerInfo
     */
    public function addContainerInfo($containerInfo): ContainerSettingsAwareInterface {
        $this->containerInfo = $this->containerInfo.$containerInfo;
        return $this;
    }

    public function lock(): ContainerSettingsAwareInterface {
        $this->locked = true;
        return $this;
    }

    /**
     * Nastaví definici služby s daným jménem. Služba je volaná metodou get() kontejneru a vrací hodnotu.
     * Služba definovaná metodou set() generuje hodnotu pouze jednou, při prvním volání metody kontejneru get(), další volání metody get() vrací
     * tutéž hodnotu. Pokud služba generuje objekt, každé volání get() vrací stejnou instanci objektu.
     * Služba musí být Closure nebo přímo zadaná hodnota. Generování hodnoty zadanou službou probíhá až v okamžiku volání metody get().
     * Pokud je služba typu \Closure, provede se se až v okamžiku volání metody get() kontejneru, jed tedy o lazy load generování hodnoty.
     *
     * <b>Předefinování služby:</b> Při opakovaném volání metody set() se stejným jménem služby dojde k vyhození výjimky. Nelze ani definovat metodou set()
     * službu, která již byla definována v delegátovi. Stejnou službu nelze ani dodatečně dodefinovat do delgáta, ovšem to proto, že kontejner, který je použit jako delegát,
     * je v takovém okamžiku uzamčen. Duplicitní volání služby se stejným jménem v různých delegujících a delegátech má za následek, že z různých kontejnerů jsou volány různé služby a vznikají
     * tak různé objekty, respektive objekty stejného typu, ale ve více instancích, přestože jsou definovány metodou set(). To je potenciálně nebezpečná situace a proto
     * je duplicitní nastevení služby metodou set() zakázáno.
     *
     * <b>Uzamčený kontejner:</b> Pokud byl kontejner uzamčen voláním metody lock(), nelze mu již nastavovat žádné služby, volání metody set() zamčeného kontejneru vyvolá výjimku. Pozor: kontejner
     * je vždy automaticky uzamčen, pokud byl použit jako delegate kontejner, tedy v okamžiku, kdy je předán jako parametr konstruktoru delegujícího kontejneru.
     *
     * @param string $serviceName
     * @param \Closure $service
     * @return \Pes\Container\ContainerSettingsAwareInterface
     * @throws Exception\LockedContainerException
     * @throws Exception\UnableToSetServiceException
     */
    public function set($serviceName, $service) : ContainerSettingsAwareInterface {
        if ($this->locked) {
            throw new Exception\LockedContainerException("Nelze nastavovat službu uzamčenému kontejneru. Kontener je uzamčen automaticky, když byl použit jako delegát.");
        }
        if (isset($this->delegateContainer) AND $this->delegateContainer->has($serviceName)) {
            $cName = $this->containerInfo ?? "";
            throw new Exception\UnableToSetServiceException("Nelze nastavit službu $serviceName kontejneru $cName. Služba $serviceName je obsažena v delegate kontejneru.");
        }
        if ($this->has($serviceName)) {
            $cName = $this->containerInfo ?? "";
            throw new Exception\UnableToSetServiceException("Nelze nastavit službu $serviceName kontejneru $cName. Služba $serviceName již byla v tomto kontejneru nakonfigurována.");
        }
        $this->setOverride($serviceName, $service);
        return $this;
    }

    /**
     * Nastaví službu tak, že služba přetíží případnou službu stejného jména v kterémkoli delegátovi konfigurovaného kontejneru (ve vnořených kontejnerech).
     * Služba definovaná metodou setOverride() - stejně jako u metody set() - generuje hodnotu pouze jednou, při prvním volání metody kontejneru get(), další volání metody get() vrací
     * tutéž hodnotu. Pokud služba generuje objekt, každé volání get() vrací stejnou instanci objektu.
     * Metoda setOverride() nastavuje služby s jménem, které bude použito v právě konfigurováném kontejneru a případně v dalších delegujících kontejnerech (obalujících),
     * přetíží tedy případnou služby stejného jména v kterémkoli delegátovi (ve vnořeném kontejneru).
     *
     * Služby nastavené metodou setOverride() je možno volat i z delegujících kontejnerů, tedy jako služby delegáta.
     *
     * @param type $serviceName
     * @param \Closure $service
     * @return \Pes\Container\ContainerSettingsAwareInterface
     */
    public function setOverride($serviceName, $service): \Pes\Container\ContainerSettingsAwareInterface {
        if ($service instanceof \Closure) {
            $this->generators[$serviceName] = function() use ($serviceName, $service) {
                        // ještě není instance?
                        if (!isset($this->instances[$serviceName])) {
                            // vytvoř instanci
                            $this->instances[$serviceName] = $service($this);
                        }
                        return $this->instances[$serviceName];
                    };
        } else {
            $this->generators[$serviceName] = function() use ($service) {
                        return $service;  // service je hodnota - nevytvářím instanci - mám hodnotu zde v definici anonymní funkce
                    };
        }
        return $this;
    }

    /**
     * Odstraní hodnotu (např, objekt) vytvořenou při předcházejecím volání metody get() se zadaným jménem služby.
     * Pokud již byla vytvořena instance objektu vraceného službou a tato instance byla odstraněna metodou reset(), pak při dalším volání služby get() vznikne
     * nová instance.
     *
     * Metoda je vhodná pro reset objektů, jejichž instancování závisí na kontextu nebo na stavu aplikace a tento kontext nebo stav se změnil a je třeba
     * výjimečně vytvořit novou instaci, ačkolijinak opkovaná volání get() vrecejí instanci stejnou. Typicky jse o objekty vytvořené s užitím bezpečnostního kontextu,
     *
     * @param type $serviceName
     * @return \Pes\Container\ContainerSettingsAwareInterface
     */
    public function reset($serviceName)  : ContainerSettingsAwareInterface{
        if (isset($this->instances[$serviceName] )) {
            unset($this->instances[$serviceName] );
        }
        return $this;
    }

    /**
     * Nastaví definici služby s daným jménem jako typ factory. Služba je volaná metodou get() kontejneru a vrací hodnotu.
     * Služba definovaná metodou factory() generuje hodnotu vždy znovu, při každém volání metody kontejneru get().
     * Pokud služba generuje objekt, každé volání get() vrací novou instanci objektu.
     * Služba musí být Closure nebo přímo zadaná hodnota. Generování hodnoty zadanou službou probíhá až v okamžiku volání metody get().
     * Pokud je služba typu \Closure, provede se se až v okamžiku volání metody get() kontejneru, jedná se o lazy load generování hodnoty.
     * Poznámka: uzamčení kontejneru neomezuje volání metody factory().
     *
     * @param string $factoryName
     * @param mixed $service Closure nebo hodnota
     * @return ContainerSettingsAwareInterface
     */
    public function factory($factoryName, $service) : ContainerSettingsAwareInterface {
        if ($service instanceof \Closure) {
            $this->generators[$factoryName] = function() use ($factoryName, $service) {
                        return $service($this);
                    };
        } else {
            $this->generators[$factoryName] = function() use ($service) {
                        return $service;  // service je hodnota
                    };
        }
        return $this;
    }

    /**
     * Nastaví alias ke skutečnému jménu služby. Volání služby jménem alias vede na volání služby se skutečným jménem.
     * Třída nepodporuje víceúrovňové alias (alias k aliasu, který je aliasem ke jménu atd.)
     * Alias je aliasem ke službě kontejneru, kde je definován nebo ke službě vnořeného delegate kontejneru.
     * Poznámka: uzamčení kontejneru neomezuje volání metody alias().
     *
     * @param string $alias
     * @param string $name
     * @return ContainerSettingsAwareInterface
     */
    public function alias($alias, $name) : ContainerSettingsAwareInterface {
        $this->aliases[$alias] = $name;
        return $this;
    }

###############################################

    /**
     * Existuje definice služby?
     *
     * @param string $serviceName Jméno hledané služby
     * @return bool
     */
    public function has($serviceName) {
        if (isset($this->generators[$serviceName])) {     // pole $this->has obsahuje jen položky definované v této instanci kontejneru
            return TRUE;
        }
        $realName = $this->realName($serviceName);
        if (isset($this->generators[$realName])) {
            return TRUE;
        }
        if (isset($this->delegateContainer) AND $this->delegateContainer->has($serviceName)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Vrací výsledek volání služby zadaného jména.
     *
     * @param string $serviceName Jméno volané služby.
     * @return mixed Návratová hodnota vracená službou.
     * @throws NotFoundException Služba nenalezena
     */
    public function get($serviceName) {
        if (isset($this->generators[$serviceName])) {
            return $this->generators[$serviceName]();
        }
        $realName = $this->realName($serviceName);
        if (isset($this->generators[$realName])) {
            return $this->generators[$realName]();
        }
        if (isset($this->delegateContainer) AND $this->delegateContainer->has($serviceName)) {
            return $this->delegateContainer->get($serviceName);
        }
        if (isset($this->delegateContainer) AND $this->delegateContainer->has($serviceName)) {
            return $this->delegateContainer->get($serviceName);
        }
        throw new Exception\NotFoundException("Volání služby kontejneru get('$serviceName') selhalo. Požadovaná služba kontejneru se jménem: '$serviceName' neexistuje, nebyla nastavena.");
    }

    /**
     * Pokud je v instanci kontejneru zadano jméno jako alias, metoda vrací zadané skutečné jméno služby.
     * Pokud alias zadán není, ale je nastavena hodnota instanční proměnné $useAutogeneratedInterfaceAliases na TRUE (default),
     * jméno hledané služby odpovídá existujícímu interface a končí řetězcem zadaným konstantou INTERFACE_NAME_POSTFIX a existuje třída se jménem
     * odpovídajícím jménu interface po odtržení přípony dané konstatntou INTERFACE_NAME_POSTFIX, pak metoda vrací jméno takové třídy.
     *
     * Příklad:
     * self:INTERFACE_NAME_POSTFIX = 'Interface'
     * volání metody ->realName('KlokociInterface') vrací sktečné jméno 'Klokoci' (pokud existuje KlokociInterface i Klokoci)
     *
     *
     * @param string $serviceName
     * @return string
     */
    protected function realName($serviceName) {
        if (isset($this->aliases[$serviceName])) {
            $realName = $this->aliases[$serviceName];
        } elseif ($this->useAutogeneratedInterfaceAliases) {
            if (substr_compare( $serviceName, self::INTERFACE_NAME_POSTFIX, self::INTERFACE_NAME_POSTFIX_LENGTH) === 0) {
                if (interface_exists($serviceName)) {
                    $realName = substr($serviceName, 0, self::INTERFACE_NAME_POSTFIX_LENGTH);
                    if (class_exists($realName)) {
                        $this->aliases[$serviceName] = $realName;
                    } else {
                        throw new Exception\NotFoundException("Pokus použít automaticky generované jméno třídy $realName k interface $serviceName selhal. Definice třídy $realName nebyla nalezena.");
                    }
                } else {
                    throw new Exception\NotFoundException("Pokus použít automaticky generované jméno třídy k požadovanému jménu interface $serviceName selhal. Definice interface $serviceName nebyla nalezena.");
                }
            } else {
                $realName = $serviceName;
            }
        } else {
            $realName = $serviceName;
        }
        return $realName;
    }

    /**
     * Pokud:
     * - je zapnuto automatické generování aliasů k interface - paramater kontruktoru $useAutogeneratedInterfaceAliases byl TRUE (dafault je TRUE)
     * - a existuje interface se zadaným jménem služby
     * - a existuje automatický překlad jména služby (interface) na jméno třídy
     *
     * pak nastaví automaticky přeložené jméno třídy jako alias k zadanému jménu služby (interface).
     *
     * Pro automatický překlad jména interface se používá protected metoda translateInterfaceName(). Tuto metodu je možno přetížit a změnit tak
     * defaultní překlad.
     *
     * @param type $serviceName
     */
    private function createAutoInterfaceAlias($serviceName) {
        if ($this->useAutogeneratedInterfaceAliases AND interface_exists($serviceName)) {
            $realName = $this->translateInterfaceName($serviceName);
            if (isset($realName) AND $realName) {     // moje při neuspěchu vrací NULL, ale potomci vrací kdovíco
                $this->aliases[$serviceName] = $realName;
            }
        }
        return $realName;
    }

    /**
     * Metoda provádí automatický překlad jména interface na jméno třídy. Tuto metodu je možno přetížit a změnit tak
     * defaultní překlad.
     *
     * Tato metoda zjistí zda jméno interface končí řetezcem definovaným konstatou třídy INTERFACE_NAME_POSTFIX, pokud ano,
     * vytvoří jméno třídy odstraněním tohoto řetezce (přípony) ze jména interface.
     *
     * Alternativně je možno změnit automatický překlad přetížením třídy kontejneru třídou, které pouze předefinuje konstanty
     * INTERFACE_NAME_POSTFIX a INTERFACE_NAME_POSTFIX_LENGTH, pak je metoda překladu zachována a mění se jen očekávaná přípona v názvu interface.
     * Konstanta INTERFACE_NAME_POSTFIX_LENGTH udává délku přípony.
     *
     * Metoda musí vracet string v případě úspěchu nebo NULL v případě neúspěchu.
     *
     * @param type $serviceName
     * @return string || NULL
     */
    protected function translateInterfaceName($serviceName) {
        $len = -1*self::INTERFACE_NAME_POSTFIX_LENGTH;
        if (substr_compare( $serviceName, self::INTERFACE_NAME_POSTFIX, $len) === 0){
            return substr($serviceName, 0, $len);
        }
    }
}