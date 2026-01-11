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
use Pes\Database\Statement\Exception\ExecuteException;
use Pes\Database\Statement\Exception\BindParamException;
use Pes\Database\Statement\Exception\BindValueException;

class Statement extends PDOStatement {  // implements StatementInterface {

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

    public function setLogger(LoggerInterface $logger) {
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
     * @param type $arg2
     * @param type $arg3
     * @return bool Success <p>Returns <b><code>TRUE</code></b> on success or <b><code>FALSE</code></b> on failure.</p>
     */
    public function setFetchMode(int $mode, mixed ...$args): bool {
        $success = parent::setFetchMode($mode, $args);
        if ($this->logger) {
            $message = $this->getInstanceInfo().': setFetchMode({fetchMode})';
            $substitutes = array('fetchMode'=>$mode);
            $this->logger->debug($message, $substitutes);
        }
        if (!$success AND $this->logger) {
            $this->logger->warning(' Metoda '.__METHOD__.' selhala.');
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
        $result = parent::fetch($mode, $cursor_orientation, $cursor_offset);
        if ($this->logger) {
            $message = $this->getInstanceInfo().': fetch({fetch_style}, {cursor_orientation}, {cursor_offset})';
            $context = ['fetch_style'=>$fetch_style ?? 'null', 'cursor_orientation'=>$cursor_orientation, 'cursor_offset'=>$cursor_offset];
            if ($result===FALSE) {
                $message .= ' Metoda '.__METHOD__.' nevrátila žádná data.';
            } else {
                $message .= ' Result má {count} prvků.';
                $context = array_merge($context, ['count'=>count($result)]);
            }
            $this->logger->debug($message, $context);
        }
        return $result;
    }

//    public function fetchAll($fetch_style = NULL, $fetch_argument = NULL, $ctor_args = NULL): array {
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array {        
            $result = parent::fetchAll($mode, $args);
            if ($this->logger) {
                $message = $this->getInstanceInfo().': fetchAll({fetch_style})';
                $context = array('fetch_style'=>$fetch_style ?? 'null', 'fetch_argument'=>$fetch_argument, 'ctor_args'=>$ctor_args);
            }
        if ($this->logger) {
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
            if ($this->logger) {
                $this->logger->error($this->getInstanceInfo().': Selhal execute({input_parameters}).',
                        ['input_parameters'=>$params ?? 'null']);
                $message = " Metoda {method} selhala. Vyhozena výjimka \PDOException: {exc}.";
                $this->logger->error($message, ['method'=>__METHOD__, 'exc'=>$pdoException->getMessage()]);
                $errorInfo = $this->errorInfo();
                $message = " Výpis errorInfo: ".print_r($errorInfo, TRUE);
                $this->logger->error($message);
            }
            throw new ExecuteException(" Metoda ".__METHOD__." selhala.", 0, $pdoException);
        } finally {
            if ($this->logger) {
                $this->logger->debug($this->getInstanceInfo().': execute({input_parameters}).',
                    ['input_parameters'=>$params]);
            }

        }
        if($this->logger AND !$success) {
                $message = ' Metoda '.__METHOD__.' selhala bez vyhození výjimky. Není nastaven mod PDO::ERRMODE_EXCEPTION nebo nastala systémová chyba.';
                $this->logger->warning($message);
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
            if ($this->logger) {
                $message = " Metoda {method} selhala. Vyhozena výjimka \PDOException: {exc}.";
                $this->logger->error($message, ['method'=>__METHOD__, 'exc'=>$pdoException->getMessage()]);
            }
            throw new BindParamException(" Metoda ".__METHOD__." selhala.", 0, $pdoException);
        } finally {
            if ($this->logger) {
                $this->logger->debug($this->getInstanceInfo().': bindParam({parameter}, {variable}, {data_type}, {length}, {driver_options})',
                    ['parameter'=>$param, 'variable'=>$var, 'data_type'=>$type, 'length'=>$maxLength=NULL, 'driver_options'=>$driverOptions]);
            }
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
            if ($this->logger) {
                $message = " Metoda {method} selhala. Vyhozena výjimka \PDOException: {exc}.";
                $this->logger->error($message, ['method'=>__METHOD__, 'exc'=>$pdoException->getMessage()]);
            }
            throw new BindValueException(" Metoda ".__METHOD__." selhala.", 0, $pdoException);
        } finally {
            if ($this->logger) {
                $this->logger->debug($this->getInstanceInfo().': bindValue({parameter}, {value}, {data_type})',
                    ['parameter'=>$param, 'value'=>$value, 'data_type'=>$type]);
            }
        }
        return $success;
    }
}
