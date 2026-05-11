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

use PDO;
use PDOStatement;
use PDOException;

use ReflectionException;
use UnexpectedValueException;
use RuntimeException;

class HandlerPDOAdapter implements HandlerPDOAdapterInterface {

    /**
     * @var PDO
     */
    protected $connection;
    
    /**
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
     * Čítač instancí pro logování - inkremetuje se v konstruktoru
     * @var int
     */
    private static $handlerCounter=0;

    protected $handlerNumber;

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
                                ?LoggerInterface $constructorExceptionsLogger
            ) {
        $this->handlerNumber = ++self::$handlerCounter;
        $this->dbName = $connectionInfo->getDbName();
        $this->dbHost = $connectionInfo->getDbHost();
        $this->logger = $constructorExceptionsLogger;
        try {
            $this->connection = $this->connect($account, $connectionInfo, $dsnProvider, $optionsProvider);
        } catch (PDOException $pdoExc) {   // Nastavení obsluhy chyb PHP na vyhazování chyb místo výjimek nijak neovliní chování konstruktoru PDO - ten i nadále vyhazuje výjimky.
            $this->safeExceptionHandler($pdoExc);
        }        
        // po volání PDO vrátí zpět předchozí exception handler
        restore_exception_handler();
        $this->logger?->debug("Vytvořen database handler {info}, name {name}, host {host}.", ['info'=>$this->getInstanceInfo(), 'name'=>$this->dbName, 'host'=>$this->dbHost]);
        if ($attributesProvider) {
            $this->setAttributes($attributesProvider->getAttributesArray());  // loguje se v metodě
        }
   }
   
   /**
    * 
    * @param AccountInterface $account
    * @param ConnectionInfoInterface $connectionInfo
    * @param DsnProviderInterface $dsnProvider
    * @param OptionsProviderInterface $optionsProvider
    * @return PDO
    * @throws UnexpectedValueException
    * @throws PDOException  
    */
   private function connect(AccountInterface $account, ConnectionInfoInterface $connectionInfo, DsnProviderInterface $dsnProvider, OptionsProviderInterface $optionsProvider): PDO {
        // Z bezpečnostních důvodů connection info nemá getter pro pass a hodnota private vlastnosti pass se zde získává reflexí.
        // Tato hodnota se předává přímo do PDO, v objektu se neukládá.
        $rc = new \ReflectionClass($account);
        try {
            $userNameProperty = $rc->getProperty('name');
            $userPassProperty = $rc->getProperty('pass');
        } catch (ReflectionException $re) {
            // Pravděpodobně se změnilo jméno vlastnosti name nebo pass ve třídě User
            throw new UnexpectedValueException('Nepodařilo se získat skryté údaje z objektu ConnectionInfo.');
        }
        $userNameProperty->setAccessible(TRUE);
        $userNameValue = $userNameProperty->getValue($account);
        $userNameProperty->setAccessible(FALSE);
        $userPassProperty->setAccessible(TRUE);
        $userPassValue = $userPassProperty->getValue($account);
        $userPassProperty->setAccessible(FALSE);
        // A PDOException is thrown if the attempt to connect to the requested database fails, regardless of which PDO::ATTR_ERRMODE is currently set
        $connection = PDO::connect(
                    $dsnProvider->getDsn($connectionInfo),
                    $userNameValue,
                    $userPassValue,
                    $optionsProvider->getOptionsArray($connectionInfo)
                );
        return $connection;
   }
   
    /**
     * PRIVÁTNÍ Metoda se pokusí nastavit handleru atributy voláním PDO metody setAttrinutes().
     * Pokud se nepodaří některý atribut nastavit, metoda vyhazuje výjimku.
     * Pokud výjimka nastala díky chybě 'SQLSTATE[IM001]: Driver does not support this function: driver does not support that attribute',
     * pak metoda doplní zprávu ve výjimce o podrobný důvod.
     *
     * @param array $attributes
     * @throws RuntimeException
     */
    private function setAttributes($attributes) {
        foreach ($attributes as $key => $value) {
            $succ = $this->connection->setAttribute($key, $value);
            if (!$succ) {
                $dump = $this->dumpPDOParameters();
                $this->logger?->alert($this->getInstanceInfo().' Selhalo nastavení hodnoty atributu handleru (PDO): {key} na hodnotu {value}', array('key'=>$key, 'value'=>print_r($dump, TRUE)));
                throw new RuntimeException($this->getInstanceInfo().' Selhalo nastavení atributu '.$key.'. '.$dump);
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
                $attr = $this->connection->getAttribute(constant("\PDO::$attribute"));
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

    ##### safeExceptionHandler ###################################################
    
    /**
     * Bezpečnostní exception handler obsluhuje pouze výjimky vyhozené v konstruktoru handleru - tedy výjimky při instancování PDO.
     *
     * Nezachycená výjimka PDO vede obvykle k výpisu výjimky tak, že výpis vidí uživatel. Tento výpis obvykle obsahuje údaje o připojení.
     * Zobrazování takového výpisu je zřejmé bezpečnostní riziko.
     *
     * Proto tato třída přidává jako bezpečnostní opatření svůj vlastní exception_handler, který hlásí
     * jen základní hlášení bez podrobnách informací.
     *
     * @param type $exception
     */
    private function safeExceptionHandler(\Exception $exception) {
        $str2 = '';
        $i = 0;
        foreach ($exception->getTrace() as $trace) {
            // pro výpis argumentů používá metodu self::varPrint() - volá array_map na všechny argumenty s metodou self::varPrint() jako mapovací funkcí
            $str2 .= '#'.$i.' '.$trace['file'] ?? '(no file)'.', line '.$trace['line'] ?? '(no line)'.': '.$trace['class']??'(no class)'.$trace['type']??'(no type)'.$trace['function']??'(no function)'
                 .'('.\implode(',', array_map([$this, 'varPrint'], $trace['args']??[])).')'.\PHP_EOL;
            $i++;
            // pokud jsou parametry handleru injektovány z kontejneru, pak výpis proměnných nad úroveň #1 vypisuje obsah kontejneru - log je obrovský
            if ($i>3) {
                break;       #0 je PDO exception, #1 je Handler exception - to stačí
            }
        }
        $this->logger->critical('Chyba při instancování db handleru. '.$exception->getMessage().\PHP_EOL.\PHP_EOL.'Trace string:'.\PHP_EOL.$exception->getTraceAsString().\PHP_EOL.$str2);
        // vyhodí výjimku s bezpečnou informací
        throw new UnexpectedValueException(' Problém s připojením k databázi - chyba při instancování Handleru. Info v logu. Kontaktujte správce systému.');
    }

    /**
     * Metoda JE použita!
     * Volána jako funkce v metodě safeExceptionHandler()
     *
     * @param type $param
     * @return type
     */
    private function varPrint($param) {
        $pr = [];
        foreach ($param as $var) {
            $pr[] = static::renderValueAsInfo($var);
        }
        return print_r($pr, TRUE);
    }

    private static function renderValueAsInfo($var) {
        $vartype = gettype($var);
        switch ($vartype) {
            case "boolean":
                $rendered = $vartype." ".($var ? "TRUE" : "FALSE");
                break;
            case "integer":
            case "double":    // (for historical reasons "double" is returned in case of a float, and not simply
            case "float":
                $rendered = $vartype." ".$var;
                break;
            case "string":
                $rendered = $vartype." ". strlen($var)." bytes" . (strlen($var)>100 ? ": \"".substr($var, 0, 97)."... (shortened)\"" : $var);
                break;
            case "array":
                $rendered = $vartype." ".count($var)." elements";
                break;
            case "object":
                $rendered = $vartype." ". get_class($var);
                break;
            case "resource":
            case "resource (closed)":  // as of PHP 7.2.0
                $rendered = $vartype;
                break;
            case "NULL":
            case "unknown type":
                $rendered = $vartype;
                break;
        }
        return $rendered;
    }
    
######### metody HandlerPDOAdapterInterface (LoggerAwareInterface) ##################################    
    
    #[\Override]
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    #[\Override]
    public function getLogger(): ?LoggerInterface {
        return $this->logger;
    }
    
######### adaptér pro METODY PDO #######################################################################
    
    public function errorInfo(): array {
        return $this->connection->errorInfo();
    }

    public function beginTransaction(): bool {
        if ($this->logger) {
                $this->logger->debug($this->getInstanceInfo().' beginTransaction()');
        }
        $ret = $this->connection->beginTransaction();
        return $ret;
    }
    
    public function inTransaction(): bool {
        return $this->connection->inTransaction();
    }
    
    public function lastInsertId(?string $name = null): string|false {
        $ret = $this->connection->lastInsertId($name);
        if ($this->logger) {
            $this->logger->debug($this->getInstanceInfo().' lastInsertId({name})', ['name'=>$name ?? 'null']);
        }
        return $ret;
    }
    
    public function commit(): bool {
        if ($this->logger) {
                $this->logger->debug($this->getInstanceInfo().' commit()');
        }
        $ret = $this->connection->commit();
        return $ret;
    }

    public function exec(string $query): int|false {
        if ($this->logger) {
                $this->logger->debug($this->getInstanceInfo().' exec({query})',
                    ['query'=>$query]);        }
        $ret = $this->connection->exec($query);
        return $ret;
    }

    public function rollBack(): bool {
        if ($this->logger) {
                $this->logger->debug($this->getInstanceInfo().' rollBack()');
        }
        $ret = $this->connection->rollBack();
        return $ret;
    }


    /**
     * {@inheritDoc}
     * 
     * Pokud má handler nastaven logger (metodou setLogger()), je tento logger nastaven jako logger i vytvořenémmu objektu Statement. Statement objekt "zdědí" logger z Handleru.
     *
     * @param string $query SQL příkaz s případnými pojmenovanými nebo otazníkem značenými placeholdery (SQL template)
     * @param array $options
     * @return \PDOStatement|false
     * @throws Exception\PrepareException
     */
    public function prepare($query, array $options = []): \PDOStatement|false {
        // 		public function prepare(string $query, array $options = []): \PDOStatement|false
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
            /* @var $prepStatement PDOStatement */
            $prepStatement = $this->connection->prepare($query, $options);
        } catch (\PDOException $pdoException) {
            if ($this->logger) {
                $this->logger->error($this->getInstanceInfo().' selhal prepare({sqlStatement}), nebyl vytvořen statement objekt.',
                        ['sqlStatement'=>$query]);
                $message = " Metoda {method} selhala. Vyhozena výjimka \PDOException: {exc}.";
                $this->logger->error($message, ['method'=>__METHOD__, 'exc'=>$pdoException->getMessage()]);
            }
            $einfo = $this->connection->errorInfo();
//            throw new HandlerFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
            throw new Exception\PrepareException($einfo[2]." Metoda ".__METHOD__." selhala.", 0, $pdoException);
        } finally {
            if ($this->logger && isset($prepStatement)) {
                if ($prepStatement instanceof StatementInterface) {   // typ $prepStatement je dán nastavením atributů -> nemusí to být StatementInterface, ten je nastavován AttributeProviderDefault
                    $replace = ['sqlStatement'=>$query, 'driver_options'=>$options, 'statementInfo'=>$prepStatement->getInstanceInfo()];
                } else {
                    $replace = ['sqlStatement'=>$query, 'driver_options'=>$options, 'statementInfo'=> get_class($prepStatement)];
                }
                $this->logger->debug($this->getInstanceInfo().': prepare({sqlStatement}, {driver_options}). Vytvořen {statementInfo}.',
                    $replace);
                $this->inheritStatementLogger($prepStatement);
            }

        }
        return $prepStatement;
    }

    /**
     * {@inheritDoc}
     * Pokud má handler nastaven logger (metodou setLogger()), je tento logger nastaven jako logger i vytvořenémmu objektu Statement. Statement objekt "zdědí" logger z Handleru.
     *
     * @param string $query
     * @param int|null $fetchMode
     * @param mixed $fetchModeArgs
     * @return PDOStatement|false
     * @throws InvalidArgumentException
     */
    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false {
        
//        public query(string $query, ?int $fetchMode = null): PDOStatement|false
//        public query(string $query, ?int $fetchMode = PDO::FETCH_COLUMN, int $colno): PDOStatement|false
//        public query(string $query, ?int $fetchMode = PDO::FETCH_CLASS, string $classname, array $constructorArgs): PDOStatement|false
//        public query(string $query, ?int $fetchMode = PDO::FETCH_INTO, object $object): PDOStatement|false        
        
        $argsOk = false;
        
        if (count($fetchModeArgs) === 0) {
            $result = $this->connection->query($query, $fetchMode);
            $argsOk = true;
        }
        if (count($fetchModeArgs) === 1 && is_int($fetchModeArgs[0])) {
            $result = $this->connection->query($query, $fetchMode, $fetchModeArgs[0]);
            $argsOk = true;
        }
        if (count($fetchModeArgs) === 1 && is_object($fetchModeArgs[0])) {
            $result = $this->connection->query($query, $fetchMode, $fetchModeArgs[0]);
            $argsOk = true;
        }
        if (count($fetchModeArgs) === 2 && is_string($fetchModeArgs[0]) && ((null === $fetchModeArgs[1]) || is_array($fetchModeArgs[1])) ) {
            $result = $this->connection->query($query, $fetchMode, $fetchModeArgs[0], $fetchModeArgs[1]);
            $argsOk = true;
        }

        if (true !== $argsOk) {
            throw new QueryInvalidArgumentException('Neplatná kombinace argumentů metody query.');
        }     
        
        if ($this->logger) {
                $this->logger->debug($this->getInstanceInfo()." query ($query)");
        }        
        /* @var $statement PDOStatement */
        $statement =  $this->connection->query($query, $fetchMode);
        if ($statement) {
            $message = $this->getInstanceInfo().' query({sqlStatement}). Vytvořen {statementInfo}. {fetchMode}';
            $replace = ["fetchMode"=>"Nastaven default fetch mode $fetchMode."];
            if (isset($fetchMode)) {
                $replace +=[];
            }
            if ($statement instanceof StatementInterface) {   // typ $prepStatement je dán nastavením atributů -> nemusí to být StatementInterface, ten je nastavován AttributeProviderDefault
                $replace += ['sqlStatement'=>$query, 'statementInfo'=>$statement->getInstanceInfo()];
            } else {
                $replace += ['sqlStatement'=>$query, 'statementInfo'=> get_class($statement)];
            }
                $this->logger?->debug($message, $replace);
                $this->inheritStatementLogger($statement);
        } else {
            $this->logger?->warning($this->getInstanceInfo().' selhal query({sqlStatement}), nebyl vytcořen statement objekt.', ['sqlStatement'=>$query]);
        }
        return $statement;
    }
}
