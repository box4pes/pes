<?php
use Pes\Logger\FileLogger;

######### DEVELOPMENT A PRODUCTION KONSTANTY #################

## INFO ##
##
## Pokud před voláním Bootstrap.php (resp. GlobalsSet.php) nastavíme hodnotu
## konstanty PES_FORCE_PRODUCTION nebo PES_FORCE_DEVELOPMENT
## použijí se tyto hodnoty místo hodnot načtených ze systémových proměnných.
## PES_FORCE_PRODUCTION má přednost (vyšší prioritu) než PES_FORCE_DEVELOPMENT, to znamená,
## že nastavení libovolné hodnoty (vyhodnocené jako TRUE) konstanty PES_FORCE_PRODUCTION způsobí přepnutí do production modu
## bez ohledu na PES_FORCE_DEVELOPMENT.
##
##########

// Nastavení PES_DEVELOPMENT na jakoukoli neprázdnou hodnotu vyhodnocenou jako TRUE MUSÍ pro skripty znamenat, že se jedná o běh ve vývojovém prostředí.
// Pak se například mohou zobrazovat chyby apod.
// Nastavení PES_PRODUCTION na jakoukoli hodnotu vyhodnocenou jako TRUE MUSÍ pro skripty znamenat, že se jedná o běh v produčním prostředí.
// Pak se například mohou chyby potlačovat, nezobrazovat uživateli a pouze logovat.
// Užití obou proměnných současně se vylučuje.
// Pokud není nastavena ani konstanta PES_DEVELOPMENT ani PES_PRODUCTION, skripty NESMÍ fungovat jako by se jednalo o vývojové prostředí.

// Zde je nastavena hodnota PES_DEVELOPMENT a PES_PRODUCTION
// - nejprve podle hodnot PES_FORCE_PRODUCTION nebo PES_FORCE_DEVELOPMENT, ty jsou určeny k vynucení chování v průběhu vývoje pro otestování funkčnosti
//   přepínání mezi development a production chováním,
// - podle proměnné prostředí 'development' a proměnné prostředí 'production' (systémové proměnné - nestačí uživatelská, case insensitive)

if (defined('PES_FORCE_PRODUCTION')) {
    if (defined('PES_FORCE_DEVELOPMENT')) {
        throw new UnexpectedValueException("SetGlobals: Jsou nastaveny nepřípustně současně kondtsntx PES_FORCE_DEVELOPMENT a PES_FORCE_PRODUCTION. Více viz GlobalsSet.");
    }
    define('PES_FORCE_DEVELOPMENT', FALSE);
    define('PES_PRODUCTION', 'forced into production mode');
    define('PES_DEVELOPMENT', FALSE);
} elseif (defined('PES_FORCE_DEVELOPMENT')) {
    define('PES_FORCE_PRODUCTION', FALSE);
    define('PES_DEVELOPMENT', 'forced into development mode');
    define('PES_PRODUCTION', FALSE);
} else {
    define('PES_FORCE_DEVELOPMENT', FALSE);
    define('PES_FORCE_PRODUCTION', FALSE);
    if (getenv('production')) {     // Windows - musí být nastavena systémová (nestačí uživatelská) proměnná prostředí production (case insensitive)
        if (getenv('development')) {
            throw new UnexpectedValueException("SetGlobals: Jsou nastaveny nepřípustně současně proměnné prostředí 'development' i 'production'. Více viz GlobasSet.");
        }
        define('PES_PRODUCTION', 'production mode by environment');
        define('PES_DEVELOPMENT', FALSE);
    } elseif (getenv('development')) {      // Windows - musí být nastavena systémová (nestačí uživatelská) proměnná prostředí production (case insensitive)
        define('PES_DEVELOPMENT', 'development mode by environment');
        define('PES_PRODUCTION', FALSE);
    } else {
        define('PES_DEVELOPMENT', FALSE);
        define('PES_PRODUCTION', FALSE);
    }
}
// Automatické nastavení globals 'production' nebo 'development' podle toho, zda skript běží na produkčním stroji. Jméno produkčního stroje musí být definováno konstantou PRODUCTION_MACHINE_HOST_NAME.
//
// !! TOTO NASTAVENÍ MÁ PŘEDNOST PŘED NASTAVENÍM PROMĚNNÝCH PROSTŘEDÍ I NASTAVENÍMI FORCE_PRODUCTION NEBO FORCE_DEVELOPMENT
//

if (defined('PES_PRODUCTION_MACHINE_HOST_NAME') AND  strpos(strtolower(gethostname()), strtolower(PES_PRODUCTION_MACHINE_HOST_NAME))===0) {
        define('PES_RUNNING_ON_PRODUCTION_HOST', PES_PRODUCTION_MACHINE_HOST_NAME);
        define('PES_PRODUCTION', 'production mode by hostname');
        define('PES_DEVELOPMENT', FALSE);
} else {
        define('PES_RUNNING_ON_PRODUCTION_HOST', \FALSE);
}

$bootstrapLoggerArray[] = "SetGlobals:  nastaveny hodnoty - force_development: ".PES_FORCE_DEVELOPMENT.", force_production: ".PES_FORCE_PRODUCTION.", "
    . "development: ".PES_DEVELOPMENT.", production: ".PES_PRODUCTION.", running_on_production_host: ".PES_RUNNING_ON_PRODUCTION_HOST." .";

