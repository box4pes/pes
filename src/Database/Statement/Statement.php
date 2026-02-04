<?php
/**
 * Základní statement objekt pro SQL databáze. Využívá hotovou abstrakci PHP PDOStatement a jde o adapter a současně wrapper
 * pro PDOStatement.
 *
 * @author Petr Svoboda
 */
namespace Pes\Database\Statement;

use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

use Psr\Log\LoggerAwareInterface;
use Pes\Database\Statement\Exception\ExecuteException;
use Pes\Database\Statement\Exception\BindParamException;
use Pes\Database\Statement\Exception\BindValueException;
use Pes\Database\Statement\Exception\InvalidArgumentException;

class Statement extends PDOStatement implements StatementInterface, LoggerAwareInterface {

    /**
     * Čítač instancí pro logování
     * @var int
     */
    private static $statementCounter=0;
    private $statementNumber;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected function __construct() {
        $this->statementNumber = ++self::$statementCounter;
        // konstruktor musí být deklarován i když je prázdný
        // bez toho nefunguje PDO::setAttribute(PDO::ATTR_STATEMENT_CLASS, ...
    }

    public function setLogger(LoggerInterface $logger): void {
        $this->logger = $logger;
    }

    public function getInstanceInfo() {
        return "Statement ($this->statementNumber)";
    }

    /**
     * setFetchMode
     *
     * {@inheritdoc}
     *
     * Metoda má v PHP čtyři možné signatury:
     * - public PDOStatement::setFetchMode ( int $mode ) : bool
     * - public PDOStatement::setFetchMode ( int $mode = PDO::FETCH_COLUMN , int $colno ) : bool
     * - public PDOStatement::setFetchMode ( int $mode = PDO::FETCH_CLASS , string $classname , array $ctorargs ) : bool
     * - public PDOStatement::setFetchMode ( int $mode = PDO::FETCH_INTO , object $object ) : bool
     *
     * @param int $mode <p>The fetch mode must be one of the <i>PDO::FETCH_&#42;</i> constants.</p>
     * @param mixed $args
     * @return true Vrací true nebo Exception
     * @throws InvalidArgumentException
     */
    public function setFetchMode(int $mode, mixed ...$args): true {
        $success = false;
        $argsOk = false;
        $count = count($args);
//        public setFetchMode(int $mode): bool
//        public setFetchMode(int $mode = PDO::FETCH_COLUMN, int $colno): bool
//        public setFetchMode(int $mode = PDO::FETCH_CLASS, string $class, ?array $constructorArgs = null): bool        
        if ($count === 0) {
            $success = parent::setFetchMode($mode);
            $argsOk = true;
        }
        if ($count === 1 && is_int($args[0]) ) {
            $success = parent::setFetchMode($mode, $args[0]);
            $argsOk = true;
        }
        if ($count === 1 && is_string($args[0]) ) {
            $success = parent::setFetchMode($mode, $args[0]);
            $argsOk = true;
        }
        if ($count === 2 && is_string($args[0]) && is_array($args[1]) ) {
            $success = parent::setFetchMode($mode, $args[0], $args[1]);
            $argsOk = true;
        }
        if ($argsOk == false) {
            $argsPrint = print_r($args, true);
            throw new InvalidArgumentException("Neplatná kombinace argumentů: mode='$mode' count=$count argumets=$argsPrint");
        }        
        $this->logger?->debug($this->getInstanceInfo().': setFetchMode({fetchMode})', array('fetchMode'=>$mode));
        if (!$success) {
            $this->logger?->warning(' Metoda '.__METHOD__.' selhala.');
        }
        return $success;
    }

    /**
     * {@inheritdoc}
     * @param type $fetch_style
     * @param type $cursor_orientation
     * @param type $cursor_offset
     * @return type
     */
//    public function fetch($fetch_style = null, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0): mixed {
    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed  {     
        $result = parent::fetch($mode, $cursorOrientation, $cursorOffset);
        if ($this->logger) {
            $message = $this->getInstanceInfo().': fetch({mode}, {cursor_orientation}, {cursor_offset})';
            $context = ['mode'=>$mode ?? 'null', 'cursor_orientation'=>$cursorOrientation, 'cursor_offset'=>$cursorOffset];
            if ($result===FALSE) {
                $message .= ' Metoda '.__METHOD__.' nevrátila žádná data.';
            } elseif(is_array($result)) {
                $message .= ' Result je array {count} prvků.';
                $context = array_merge($context, ['count'=>count($result)]);
            } elseif (is_object($result)) {
                $message .= ' Result je objekt {type}.';
                $context = array_merge($context, ['type'=>gettype($result)]);
            } else {
                $message .= ' Metoda '.__METHOD__.' vratila neznámý typ dat.';                
            }
            $this->logger->debug($message, $context);
        }
        return $result;
    }

//    public function fetchAll($fetch_style = NULL, $fetch_argument = NULL, $ctor_args = NULL): array {
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array {
        $argsOk = false;
        $count = count($args);
//        public fetchAll(int $mode = PDO::FETCH_DEFAULT): array
//        public fetchAll(int $mode = PDO::FETCH_COLUMN, int $column): array
//        public fetchAll(int $mode = PDO::FETCH_CLASS, string $class, ?array $constructorArgs): array
//        public fetchAll(int $mode = PDO::FETCH_FUNC, callable $callback): array 
        
        if ($count === 0) {
            $result = parent::fetchAll($mode);
            $argsOk = true;
        }        
        if ($count === 1 && is_int($args[0])) {
            $result = parent::fetchAll($mode, $args[0]);
            $argsOk = true;
        }
        if ($count === 1 && is_string($args[0])) {
            $result = parent::fetchAll($mode, $args[0]);
            $argsOk = true;
        }
        if ($count === 1 && is_callable($args[0])) {
            $result = parent::fetchAll($mode, $args[0]);
            $argsOk = true;
        }
        if ($count === 2 && is_string($args[0]) && is_array($args[1]) ) {
            $result = parent::fetchAll($args[0], $args[1]);
            $argsOk = true;
        }
        if ($argsOk == false) {
            $argsPrint = $args ? print_r($args, true) : '';
            throw new InvalidArgumentException("Neplatná kombinace argumentů: mode=$mode count=$count argumets=$argsPrint");
        }
        
        if ($this->logger) {
            $message = $this->getInstanceInfo().': fetchAll({mode}, {arguments})';
            $context = array('mode'=>$mode ?? 'null', 'arguments'=> implode(', ', $args));
            if ($result===FALSE) {
                $message .= 'Metoda '.__METHOD__.' selhala.';
            } else {
                $message .= 'Result má {count} prvků.';
                $context = array_merge($context, ['count'=>count($result)]);
            }
        }
        return $result;
    }

    /**
     *
     * @param type $params
     * @return type
     * @throws ExecuteException
     */
    public function execute(?array $params = null): bool {
        try {
        $success = parent::execute($params);
        } catch (\PDOException $pdoException) {
            $this->logger?->error($this->getInstanceInfo().': Selhal execute({input_parameters}).', ['input_parameters'=>$params ?? 'null']);
            $this->logger?->error(" Metoda {method} selhala. Vyhozena výjimka \PDOException: {exc}.", ['method'=>__METHOD__, 'exc'=>$pdoException->getMessage()]);
            $this->logger?->error(" Výpis errorInfo: ".print_r($this->errorInfo(), TRUE));
            throw new ExecuteException(" Metoda ".__METHOD__." selhala.", 0, $pdoException);
        } finally {
            $this->logger?->debug($this->getInstanceInfo().': execute({input_parameters}).', ['input_parameters'=>$params]);
        }
        if(!$success) {
            $this->logger?->warning(' Metoda '.__METHOD__.' selhala bez vyhození výjimky. Není nastaven mod PDO::ERRMODE_EXCEPTION nebo nastala systémová chyba.');
        }
        return $success;
    }

    /**
     * Naváže proměnnou (jako referenci na proměnnou) na parametr sql.
     * Defaultní typ hodnoty proměnné je string, pokud parametr $data_type není zadán bude hodnota zadané proměnné přetypována na string.
     * V případě jiného typu než string je vhodné typ zadat.
     * Ponechání default typu může způsobit chybný výsledek pokud typ hodnoty neodpovídá typu sloupce v databázi.
     * Např. hodnota typu bool s hodnotou FALSE je přetypována na string '1'.
     * Pozn. Do sloupců typu date a datetime se ukládá hodnota typu string, u číselných hodnot nedochází k potížím.
     *
     * @param type $parameter
     * @param type $variable
     * @param type $data_type
     * @param type $length
     * @param type $driver_options
     * @return type
     * @throws BindParamException
     */
//    public function bindParam($parameter, &$variable, $data_type=\PDO::PARAM_STR, $length=NULL, $driver_options=NULL): bool {
    public function bindParam(string|int $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool {        
        try {
            $success = parent::bindParam($param, $var, $type, $maxLength, $driverOptions);
        } catch (\PDOException $pdoException) {
            $this->logger?->error(" Metoda {method} selhala. Vyhozena výjimka \PDOException: {exc}.", ['method'=>__METHOD__, 'exc'=>$pdoException->getMessage()]);
            throw new BindParamException(" Metoda ".__METHOD__." selhala.", 0, $pdoException);
        } finally {
            $this->logger?->debug($this->getInstanceInfo().': bindParam({parameter}, {variable}, {data_type}, {length}, {driver_options})',
                ['parameter'=>$param, 'variable'=>$var, 'data_type'=>$type, 'length'=>$maxLength=NULL, 'driver_options'=>$driverOptions]);
        }
        return $success;
    }

    /**
     * Naváže hodnotu na parametr sql. Defaultní typ hodnoty je string, pokud parametr $data_type není zadán bude zadaná hodnota přetypována na string.
     * V případě jiného typu než string je vhodné typ zadat.
     * Ponechání default typu může způsobit chybný výsledek pokud typ hodnoty neodpovídá typu sloupce v databázi. Např. hodnota typu bool s hodnotou FALSE je přetypována na string '1'.
     * Pozn. Do sloupců typu date a datetime se ukládá hodnota typu string, u číselných hodnot nedochází k potížím.
     *
     * @param string $parameter
     * @param scalar $value
     * @param int $data_type Hodnota platné konstanty \PDO::PARAM_*** , defaltně \PDO::PARAM_STR
     * @return bool
     * @throws BindValueException
     */
//    public function bindValue($parameter, $value, $data_type=\PDO::PARAM_STR): bool {
    public function bindValue(string|int $param, mixed $value, int $type = PDO::PARAM_STR): bool {
        try {
            $success = parent::bindValue ($param, $value, $type);
        } catch (\PDOException $pdoException) {
            $this->logger?->error(" Metoda {method} selhala. Vyhozena výjimka \PDOException: {exc}.", ['method'=>__METHOD__, 'exc'=>$pdoException->getMessage()]);
            throw new BindValueException(" Metoda ".__METHOD__." selhala.", 0, $pdoException);
        } finally {
            $this->logger?->debug($this->getInstanceInfo().': bindValue({parameter}, {value}, {data_type})',
                ['parameter'=>$param, 'value'=>$value, 'data_type'=>$type]);
        }
        return $success;
    }
}
