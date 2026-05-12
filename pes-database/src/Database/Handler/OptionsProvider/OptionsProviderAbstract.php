<?php
namespace Pes\Database\Handler\OptionsProvider;
use Psr\Log\LoggerInterface;

/**
 * Provider poskytuje výchozí options pro MySQL.
 * <p>Nastavuje:</p>
 * <ul><li>PDO::MYSQL_ATTR_FOUND_ROWS = TRUE - příkaz $statement->rowCount() vrací počet nalezených a nikoli počet skutečně dotčenývh řádků</li>
 * <li>PDO::MYSQL_ATTR_INIT_COMMAND = SET NAMES charset COLLATE collation - Nastavuje charset a collation na hodnoty uložené v předaném parametru ConnectionInfo</li></ul>
 *
 * Pro vytvoření options používá objekt ConnectionInfoInterface a z něj:
 * <ul><li>znakovou sadu (charset)</li>
 * <li>řazení (collation)</li></ul>
 * <p>Podrobnosti:</p><p>
 * Příkaz PdoStatement::rowCount() defaultně vrací počet nalezených řádků a nikoli počet dotčených řádků.
 * Při UPDATE řádku stejnými hodnotami, které již jsou v tabulce zapsány MySQL vrací count affected rows 0.
 * Pokud chci v kódu testovat úspěšnost zápisu při příkazu UPDATE je lepší vracet počet nalezených řádků.</p><p>
 * Pro nastavení kódování a řazení pro připojení se nastaví jako inicializační příkaz (volaný vždy při vytoření handleru)
 * SET NAMES charset COLLATE collation s hodnotami charset a collation získanými z ConnectionInfo. Pozn. nastavení kódování
 * a collationv dsn funguje různě v různých verzích PHP a někdy vůbec.</p>
 *
 * @author pes2704
 */
abstract class OptionsProviderAbstract implements OptionsProviderInterface {

    protected $options = [];
    
    /** @var Psr\Log\LoggerInterface */
    protected $logger;

    
    public function __construct(array $options=[]) {
        $this->options = $options;
    }
    
    public function setLogger(LoggerInterface $logger): void {
        $this->logger = $logger;
    }
}
