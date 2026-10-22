<?php
namespace Pes\Database\Handler\DsnProvider;

use Pes\Database\Handler\ConnectionInfoInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * @author pes2704
 */
interface DsnProviderInterface extends LoggerAwareInterface {

    /**
     * @return string Řetězec vhodný jako parametr dsn pro vytvoření objektu PDO.
     */
    public function getDsn(ConnectionInfoInterface $connectionInfo);
}
