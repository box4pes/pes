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

use PDO;
use PDOStatement;
use PDOException;

use ReflectionException;
use UnexpectedValueException;
use RuntimeException;

class Handler extends HandlerPDOAdapter implements HandlerInterface {


######## metody HandlerInterface ######################################################

    /**
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

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    #[\Override]
    public function getDatabaseHandlerErrorInfo(): string {
        return var_export($this->connection->errorInfo(), TRUE);
    }

}
