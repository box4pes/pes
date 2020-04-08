<?php
namespace Pes\Database\Handler\DsnProvider;

use Pes\Database\Handler\ConnectionInfoInterface;

/**
 * DsnProviderMysql
 * Vrací dsn řetězec pro db handler.
 * Pro vytvoření dsn používá objekt ConnectionInfoInterface a z něj:
 * <ul><li>jméno hosta (host)</li>
 * <li>číslo portu (dbPort)</li>
 * <li>jméno databáze (dbName).</li></ul>
 *
 * Je připraven pouze na dsn pro připojení k databázi prostřednictvím příslušného PDO db handeru.
 * Název handleru je uveden v konstantě třídy.
 *
 * @author pes2704
 */
final class DsnProviderMysql extends DsnProviderAbstract {

    const PDO_DRIVER_NAME = 'mysql';

    /**
     * Sestaví dsn ve formátu MySQL.
     *
     * Používá vždy hodnotu dbHost, hodnoty dbPort, dbName jen pokud jsou v connectionInfo obsaženy.
     *
     * @return string
     */
    public function getDsn(ConnectionInfoInterface $connectionInfo) {
        $dsn = self::PDO_DRIVER_NAME.':host=' . $connectionInfo->getDbHost() .
                      ($connectionInfo->getDbPort() ? (';port=' . $connectionInfo->getDbPort()) : '') .
                      ($connectionInfo->getDbName() ? (';dbname=' . $connectionInfo->getDbName()) : '');
        if($this->logger) {
              $this->logger->debug(__CLASS__.' Vytvořeno dsn: {dsn}', ['dsn'=>$dsn]);
        }
        return $dsn;
    }
}
