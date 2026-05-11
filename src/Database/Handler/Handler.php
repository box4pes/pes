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

use PDO;
use PDOException;
use Throwable;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\ConnectionInfoInterface;
use Pes\Database\Handler\DsnProvider\DsnProviderInterface;
use Pes\Database\Handler\OptionsProvider\OptionsProviderInterface;
use Pes\Database\Handler\AttributesProvider\AttributesProviderInterface;
use Pes\Database\Statement\StatementInterface;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;


class Handler extends HandlerPDOAdapter implements HandlerInterface { 
    private const UNSUPPORTED_ATTRIBUTE_MESSAGE = 'driver does not support that attribute';

    /**
     * 
     * @var PDO
     */
    private $connection;
    
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

    private $handlerNumber;

######## metody HandlerInterface ######################################################
    
    /**
     * Metoda getInstanceInfo
     *
     * {@inheritdoc}
     *
     * @return string
     */
    #[\Override]
    public function getInstanceInfo() {
        return "Handler $this->dbName ($this->handlerNumber)";
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    #[\Override]
    public function getSchemaName(): string {
        return $this->dbName;
    }

    ###############  METODY PRO DEBUG  ######################

    #[\Override]
    public function getDatabaseHandlerErrorInfo(): string {
        return var_export($this->connection->errorInfo(), TRUE);
    }

}
