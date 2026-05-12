<?php
namespace Pes\Database\Handler\DsnProvider;

/**
 * Description of DsnProvider
 * Vrací dsn řetězec pro db handler. Je připraven pouze na dsn pro připojení k databázi prostřednictvím specializovaného db handeru,
 * Název handleru je uveden v konstantě třídy.
 *
 * @author pes2704
 */
final class DsnProviderMssql extends DsnProviderAbstract {
    // https://msdn.microsoft.com/en-us/library/ff628167%28v=sql.105%29.aspx
    const PDO_DRIVER_NAME = 'sqlsrv';

    /**
     * Sestaví dsn ve formátu MS SQL.
     *
     * Používá vždy hodnotu dbHost, hodnoty dbPort, dbName jen pokud jsou v connectionInfo obsaženy.
     *
     * @return string
     */
    public function getDsn(ConnectionInfoInterface $connectionInfo) {
        $dsn = self::PDO_DRIVER_NAME . ':server=' . $connectionInfo->dbHost .
                      ($connectionInfo->getDbPort() ? (', ' . $connectionInfo->getDbPort()) : '') .
                      ($connectionInfo->getDbName() ? (';Database=' . $connectionInfo->getDbName()) : '');
        if($this->logger) {
              $this->logger->debug(__CLASS__.' Vytvořeno dsn: {dsn}', ['dsn'=>$dsn]);
        }
        return $dsn;
    }
}
