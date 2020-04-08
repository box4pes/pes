<?php
namespace Pes\Database\Handler\OptionsProvider;

use Pes\Database\Handler\ConnectionInfo;
use Psr\Log\LoggerAwareInterface;

/**
 * Vrací pole options pro konstruktor PDO
 * @author pes2704
 */
interface OptionsProviderInterface extends LoggerAwareInterface {
    public function getOptionsArray(ConnectionInfo $connectionInfo, $options=[]);
}
