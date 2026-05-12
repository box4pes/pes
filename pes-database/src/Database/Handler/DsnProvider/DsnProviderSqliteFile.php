<?php
namespace Pes\Database\Handler\DsnProvider;

use Pes\Database\Handler\ConnectionInfoInterface;

/**
 * DsnProviderSqliteFile
 * Vrací dsn řetězec pro db handler sqlite databáze ukládané do souboru.
 * 
 * Pro vytvoření dsn používá objekt ConnectionInfoInterface a z něj:
 * <ul>
 * <li>jméno databáze (dbName).</li></ul>
 * Jméno databáze je použito jako plná absolutní cesta k souboru s databází (včetně přípony). Složka zadaná v jménu databáze musí být zapisovatelná pro PHP skript.
 * 
 * Pokud objekt ConnectionInfoInterface nemá hodnotu dbName vznikne dočasná Sqlite databáze, která bude smazána po konci spojení.
 * 
 * Je připraven pouze na dsn pro připojení k databázi prostřednictvím příslušného PDO db handeru.
 * Název handleru je uveden v konstantě třídy.
 *
 * @author pes2704
 */
final class DsnProviderSqliteFile extends DsnProviderAbstract {

    const PDO_DRIVER_NAME = 'sqlite';
    
    /**
     * Sestaví dsn ve formátu MySQL.
     *
     * Používá vždy hodnotu dbHost, hodnoty dbPort, dbName jen pokud jsou v connectionInfo obsaženy.
     *
     * @return string
     */
    public function getDsn(ConnectionInfoInterface $connectionInfo) {
        $dsn = self::PDO_DRIVER_NAME.':'.($connectionInfo->getDbName() ? ($connectionInfo->getDbName()) : '');
        if($this->logger) {
              $this->logger->debug(__CLASS__.' Vytvořeno dsn: {dsn}', ['dsn'=>$dsn]);
        }
        return $dsn;
    }
}
