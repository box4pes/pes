<?php
namespace Pes\Database\Handler\OptionsProvider;

use Pes\Database\Handler\ConnectionInfo;

/**
 * Objekt typu OptionsProviderInterface je povinným parametrem pro volání konstruktoru Handleru.
 * Pro případ, kdy skutečně nechci nastavivat žádné options je možno použít tento options provider.
 *
 * @author pes2704
 */
final class OptionsProviderNull extends OptionsProviderAbstract {
    public function getOptionsArray(ConnectionInfo $connectionInfo, $options=[]) {
        if($this->logger) {
            $this->logger->debug(__CLASS__.': Nevytvořeny žádné options. Parametry jsou ignorovány.');
        }
        return array();
    }
}
