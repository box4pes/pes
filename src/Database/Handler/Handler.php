<?php
/**
 * Základní handler objekt pro SQL databáze. Využívá hotovou abstrakci PHP PDO a jde o adapter a wrapper pro PDO.
 * Pro vytvoření instance využívá dsn provider, který musí generovat dsn pro připojení k databázi, options provider, který pokytuje options pro volání PDO
 * konstruktoru (před vytvořením PDO) a attribute setter, který může nastavit atrinuty vytvořeného objektu (po vytvoření PDO).
 * Objekt implementuje všechny metody PDO (jako wrapper) a přidává metody vlastní (jako adapter).
 *
 * @author pes2704
 */
namespace Pes\Database\Handler;

use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\ConnectionInfoInterface;
use Pes\Database\Handler\DsnProvider\DsnProviderInterface;
use Pes\Database\Handler\OptionsProvider\OptionsProviderInterface;
use Pes\Database\Handler\AttributesProvider\AttributesProviderInterface;
use Pes\Database\Statement\StatementInterface;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

class Handler extends \PDO implements HandlerInterface {

    /**
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Uschovaná hodnota pro identifikaci handleru při logování a debugování
     * @var string
     */
    protected $dbName;

    /**
     * Uschovaná hodnota pro identifikaci handleru při logování a derbugování
     * @var string
     */
    protected $dbHost;

    /**
     * Čítač instancí pro logování
     * @var int
     */
    protected static $handlerCounter=0;

    /**
     *
     * @var LoggerInterface
     */
    private static $safeExceptionHandlerLogger;

//    use SecurityContextObjectTrait;  //zdá se, že PDO má final public function __sleep() ačkoli v dolimentaci není nic (jen v Nette APi dokumentaci ??)

    /**
     * Konstruktor, přijímá povinné instanční proměnné objekty ConnectionInfo, DsnProvider, OptionsProvider, AttributesProvider a Logger.
     * Pokud některý z těchto objektů není potřeba i tak je nutné jej dodat a pro tento účel lze použít Null varianty těchto objektů.
     *
     * <b>Bezpečnostní rizika:</b>
     * Objekt User obsahuje informace pro připojení - jméno a heslo. Zde je bezpečnostní riziko, protože lze takový objekt někde omylem zobrazit.
     * Handler je obvykle používán na mnoha místech aplikace a často "globálně" dostupný. Proto handler objekt User neukládá a jen použije pro
     * vytvoření rodičovského PDO objektu.
     *
     * Objekt ConnectionInfo také obsahuje citlivé informace a je použit v handleru a také při volání metod objektů DsnProvider, OptionsProvider, AttributesProvider
     * Pproto je žádoucí, aby ani tyto objekty ConnectionInfo ani citlivé informace z něj neukládaly.
     *
     * @param AccountInterface $account Objekt obsahuje parametry uživatele pro připojení k databázi
     * @param ConnectionInfoInterface $connectionInfo Objekt obsahuje všechny parametry připojení k databázi mimo uživatele a hesla
     * @param DsnProviderInterface $dsnProvider Provider vytváří dsn řetězec pro vytvoření Handleru (PDO)
     * @param OptionsProviderInterface $optionsProvider Provider poskytuje pole options pro nastavení options při vytváření Handleru (PDO).
     * @param AttributesProviderInterface $attributesProvider Provider poskytuje pole atributů pro nastavení atributů Handleru (PDO) po jeho vytvoření
     * @param LoggerInterface $constructorExceptionsLogger Psr Logger pro logování výjimek při instancování handleru (tedy i PDO)
     */
    public function __construct(AccountInterface $account,
                                ConnectionInfoInterface $connectionInfo,
                                DsnProviderInterface $dsnProvider,
                                OptionsProviderInterface $optionsProvider,
                                AttributesProviderInterface $attributesProvider,
                                LoggerInterface $constructorExceptionsLogger
            ) {
        self::$handlerCounter++;
        self::$safeExceptionHandlerLogger = $constructorExceptionsLogger;  // pokud dojde k výjimce v konstruktoru, není objekt, není $this
        $this->dbName = $connectionInfo->getDbName();
        $this->dbHost = $connectionInfo->getDbHost();
        $this->logger = $constructorExceptionsLogger;

        // Z bezpečnostních důvodů connection info nemá getter pro pass a hodnota private vlastnosti pass se zde získává reflexí.
        // Tato hodnota se předává přímo do PDO, v objektu se neukládá.
        $rc = new \ReflectionClass($account);
        try {
            $userNameProperty = $rc->getProperty('name');
            $userPassProperty = $rc->getProperty('pass');
        } catch (\ReflectionException $re) {
            // Pravděpodobně se změnilo jméno vlastnosti name nebo pass ve třídě User
            throw new \UnexpectedValueException('Nepodařilo se získat skryté údaje z objektu User.');
        }
        $userNameProperty->setAccessible(TRUE);
        $userNameValue = $userNameProperty->getValue($account);
        $userNameProperty->setAccessible(FALSE);
        $userPassProperty->setAccessible(TRUE);
        $userPassValue = $userPassProperty->getValue($account);
        $userPassProperty->setAccessible(FALSE);
        // před voláním PDO nastaví vlastní exception handler
        $old = set_exception_handler(array(__CLASS__, 'safeExceptionHandler'));
        parent::__construct(
                $dsnProvider->getDsn($connectionInfo),
                $userNameValue,
                $userPassValue,
                $optionsProvider->getOptionsArray($connectionInfo));
        unset($userNameValue);
        unset($userPassValue);
        // po volání PDO vrátí zpět předchozí exception handler
        restore_exception_handler();
        $this->logger->debug("Vytvořen database handler {info}, name {name}, host {host}.", ['info'=>$this->getInstanceInfo(), 'name'=>$this->dbName, 'host'=>$this->dbHost]);
        if ($attributesProvider) {
            $this->setAttributes($attributesProvider->getAttributesArray());  // loguje se v metodě
        }
   }

    /**
     * PRIVÁTNÍ Metoda se pokusí nastavit handleru atributy voláním PDO metody setAttrinutes().
     * Pokud se nepodaří některý atribut nastavit, metoda vyhazuje výjimku.
     * Pokud výjimka nastala díky chybě 'SQLSTATE[IM001]: Driver does not support this function: driver does not support that attribute',
     * pak metoda doplní zprávu ve výjimce o podrobný důvod.
     *
     * @param array $attributes
     * @throws \RuntimeException
     */
    private function setAttributes($attributes) {
        foreach ($attributes as $key => $value) {
            $succ = $this->setAttribute($key, $value);
            if (!$succ) {
                $dump = $this->dumpPDOParameters();
                if ($this->logger) {
                    $this->logger->alert($this->getInstanceInfo().' Selhalo nastavení hodnoty atributu handleru (PDO): {key} na hodnotu {value}', array('key'=>$key, 'value'=>print_r($dump, TRUE)));
                }
                throw new \RuntimeException($this->getInstanceInfo().' Selhalo nastavení atributu '.$key.'. '.$dump);
            }
        }
    }

    /**
     * Metoda ověřuje funkčnost nastavení všech existujících atributů PDO. Pokusí se z handleru načíst postupně všechny atributy,
     * které PDO může mít dle dokumentace a ukládá jejich aktuální hodnoty pro výpis. Pokud přečtení atributu selže, metoda z odchytnuté výjimky zjišťuje,
     * zda příčinou je, že použitý interpret php daný atribut nepodporuje. V takovém případě uloží zprávu a nepodporovaném atributu do výpisu.
     * Výpis pak vrací jako string.
     *
     * @return string Výpis
     */
    private function dumpPDOParameters() {
        //TODO: pro PDO::PARAM_ v options

        // všechny PDO ATTR atributy
        $attributes = array(
	 "ATTR_AUTOCOMMIT", "ATTR_CASE", "ATTR_CLIENT_VERSION", "ATTR_CONNECTION_STATUS",
         "ATTR_DRIVER_NAME", "ATTR_ERRMODE", "ATTR_ORACLE_NULLS", "ATTR_PERSISTENT",
	 "ATTR_PREFETCH", "ATTR_SERVER_INFO", "ATTR_SERVER_VERSION", "ATTR_TIMEOUT"
        );

        foreach ($attributes as $attribute) {
            try {
                $attr = $this->getAttribute(constant("\PDO::$attribute"));
                $dump[] = "PDO::$attribute: (atribut číslo ".constant("\PDO::$attribute").") má hodnotu ".$attr;
            } catch (PDOException $pdoex) {
                if (strpos($pdoex->getMessage, self::CATCHED_ERROR_MESSAGE) !== FALSE) {
                    $dump[] = "Použitý PHP interpret neakceptuje atribut PDO::$attribute";
                } else {
                    throwException($pdoex);
                }
            }
        }
        return var_export($dump, TRUE);
    }

    /**
     * Bezpečnostní exception handler obsluhuje pouze výjimky vyhozené v konstruktoru handleru - tedy výjimky při instancování PDO.
     *
     * Nezachycená výjimka PDO vede obvykle k výpisu výjimky tak, že výpis vidí uživatel. Tento výpis obvykle obsahuje údaje o připojení.
     * Zobrazování takového výpisu je zřejmé bezpečnostní riziko.
     *
     * Nastavení obsluhy chyb PHP na vyhazování chyb místo výjimek nijak neovliní chování konstruktoru PDO - ten i nadále vyhazuje výjimky.
     * Proto tato třída přidává jako bezpečnostní opatření svůj vlastní exception_handler, který zachycuje výjimky všech typů a hlásí
     * jen základní hlášení bez podrobnách informací. Tento exception_handler musí být volán v konstruktoru této třídy před instancováním PDO,
     * po instancování PDO je nahrazen zpět předtím nastaveným exception handlerem.
     *
     * @param type $exception
     */
    public static function safeExceptionHandler(\Exception $exception) {
        $str2 = '';
        $i = 0;
        foreach ($exception->getTrace() as $trace) {
            @$str2 .= '#'.$i.' '.$trace['file'].', line '.$trace['line'].': '.$trace['class'].$trace['type'].$trace['function']
                 .'('.\implode(',', array_map('self::varPrint', $trace['args'])).')'.\PHP_EOL;
            $i++;
        }

        self::$safeExceptionHandlerLogger->critical('Chyba při instancování db handleru. '.$exception->getMessage().\PHP_EOL.\PHP_EOL.'Trace string:'.\PHP_EOL.$exception->getTraceAsString().\PHP_EOL.$str2);

        // Output the exception details
        throw new \UnexpectedValueException(' Problém s připojením k databázi - chyba při instancování Handleru. Info v logu. Kontaktujte správce systému.');//. $exception->getMessage()); //????? getMessage
    }

######## metody HandlerInterface ######################################################

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Metoda getInstanceInfo
     *
     * {@inheritdoc}
     *
     * Vrací hodnotu počítadla instancí hadleru pro logování.
     *
     * @return integer
     */
    public function getInstanceInfo() {
        return "Handler $this->dbName (".self::$handlerCounter.")";
    }

    public function getSchemaName(): string {
        return $this->dbName;
    }
    /**
     * Metoda JE použita!
     * Volána jako funkce v metodě safeExceptionHandler()
     *
     * @param type $param
     * @return type
     */
    private static function varPrint($param) {
        return print_r($param, TRUE);
    }

######### PŘETÍŽENÉ METODY PDO ( metody PDO Interface) #######################################################################xx

    /**
     * {@inheritDoc}
     * Pokud má handler nastaven logger (metodou setLogger()), je tento logger nastaven jako logger i vytvořenémmu objektu Statement. Statement objekt "zdědí" logger z Handleru.
     *
     * @param string $sqlStatement SQL příkaz s případnými pojmenovanými nebo otazníkem značenými paramatery (SQL template)
     * @param type $driver_options
     * @return StatementInterface
     */
    public function prepare($sqlStatement, $driver_options = array()) {
        //TODO: Svoboda
//        a - nutno zařídit, aby handler i statement byly vždy v režimu vyhazování výjimek
//        b - zabalit prepare i query do try-catch bloku, odchytit PDOException a logovat něco jako:
//            - ?? podle kódu chyb volit log warning nebo error
//        c - vyhazovat vlastní výjimku
//        pro handler:
//            $einfo = $this->dbHandler->errorInfo();
//            throw new HandlerFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
//        pro statement:
//            $einfo = $statement->errorInfo();
//            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevykonal se PDO statement z sql příkazu: $sql", $einfo[1]);


        try {
        /* @var $prepStatement StatementInterface */
        $prepStatement = parent::prepare($sqlStatement, $driver_options);
        } catch (\PDOException $pdoException) {
            if ($this->logger) {
                $this->logger->error($this->getInstanceInfo().' selhal prepare({sqlStatement}), nebyl vytvořen statement objekt.',
                        ['sqlStatement'=>$sqlStatement]);
                $message = " Metoda {method} selhala. Vyhozena výjimka \PDOException: {exc}.";
                $this->logger->error($message, ['method'=>__METHOD__, 'exc'=>$pdoException->getMessage()]);
            }
            $einfo = $this->errorInfo();
//            throw new HandlerFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
            throw new Exception\PrepareException($einfo[2]." Metoda ".__METHOD__." selhala.", 0, $pdoException);
        } finally {
            if ($this->logger) {
                if (isset($prepStatement)) {
                    if ($prepStatement instanceof StatementInterface) {   // typ $prepStatement je dán nastavením atributů -> nemusí to být StatementInterface, ten je nastavován AttributeProviderDefault
                        $replace = ['sqlStatement'=>$sqlStatement, 'driver_options'=>$driver_options, 'statementInfo'=>$prepStatement->getInstanceInfo()];
                    } else {
                        $replace = ['sqlStatement'=>$sqlStatement, 'driver_options'=>$driver_options, 'statementInfo'=> get_class($prepStatement)];
                    }
                    $this->logger->debug($this->getInstanceInfo().' prepare({sqlStatement}, {driver_options}). Vytvořen {statementInfo}.',
                        $replace);

                    // !!! pokud má handler nastaven logger, Statement objekt ho "zdědí"
                    if ($prepStatement instanceof LoggerAwareInterface) {
                        $prepStatement->setLogger($this->logger);
                    }
                }
            }

        }
        return $prepStatement;
    }

    /**
     * {@inheritDoc}
     * Pokud má handler nastaven logger (metodou setLogger()), je tento logger nastaven jako logger i vytvořenémmu objektu Statement. Statement objekt "zdědí" logger z Handleru.
     *
     * @param string $sqlStatement
     * @return type
     */
    public function query(string $sqlStatement='') {
        /* @var $statement StatementInterface */
        $statement =  parent::query($sqlStatement);
        if ($statement) {
            $message = $this->getInstanceInfo().' query({sqlStatement}). Vytvořen {statementInfo}.';
            if ($statement instanceof StatementInterface) {   // typ $prepStatement je dán nastavením atributů -> nemusí to být StatementInterface, ten je nastavován AttributeProviderDefault
                $replace = ['sqlStatement'=>$sqlStatement, 'statementInfo'=>$statement->getInstanceInfo()];
            } else {
                $replace = ['sqlStatement'=>$sqlStatement, 'statementInfo'=> get_class($statement)];
            }
            if ($this->logger) {
                $this->logger->debug($message, $replace);
                  // !!! pokud má handler nasteven logger, Statement objekt ho "zdědí"
                if ($statement instanceof LoggerAwareInterface) {
                    $statement->setLogger($this->logger);
                }
            }
        } else {
            if ($this->logger) {
                $this->logger->warning($this->getInstanceInfo().' selhal query({sqlStatement}), nebyl vytcořen statement objekt.',
                    ['sqlStatement'=>$sqlStatement]);
            }
        }
        return $statement;
    }

    ###############  METODY PRO DEBUG  ######################

    public function getDatabaseHandlerErrorInfo() {
        return var_export($this->errorInfo(), TRUE);
    }

}
