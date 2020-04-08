<?php

use Pes\Logger\FileLogger;



#### Bootstrap ####
#
# Počáteční nastavení auloloderu, prostředí, error a exception handlerů
#
####
$bootstrapLoggerArray = [];
// Pro nastavení konstant pro ostatní části bootstrapu se načítá soubor BootstrapDefaults.php
// Pro uživatelské nastavení konstant a případně i globálních proměnných pro ostatní části bootstrapu umístěte
// do sůožky aplikace definované konstantou PES_BOOTSTRAP_SETINGS_PATH
// soubor BootstrapSet.php (jako vzor můžeze použít BootstrapSetDefault.php).
// Nastavení v tomto souboru mají přednost před nastaveními v BootstrapDefaults.php (Použije se jako první).
include "SetBootstrapSettingsPath.php";
$bootstrapLoggerArray[] = "Bootstrap: include \"SetBootstrapSettingsPath.php\" define(\"PES_BOOTSTRAP_SETINGS_PATH\";".PES_BOOTSTRAP_SETINGS_PATH.")";
$bootstrapVar = getcwd()."/".PES_BOOTSTRAP_SETINGS_PATH."SetBootstrap.php";
if (is_readable($bootstrapVar)) {
    $bootstrapLoggerArray[] = "Bootstrap: include $bootstrapVar";
    include $bootstrapVar;
}
$bootstrapLoggerArray[] = "Bootstrap: include SetBootstrapDefaults.php";
include "SetBootstrapDefaults.php";   // definuje nedefinované konstanty

#### autoloader #####
#
# Je třeba volat autoloader - v dalších částech jsou používány objekty
# Soubor RegisterAutoloader.php musí nýt umístěn v kořenovém adresáři aplikace (např. vedle kořenového souboru aplikace index.php).
# Pokud chybí, nouzově načte composer autoload ze složky Pes pro objekty v Bootstrap.
#
####

$bootstrapVar = getcwd()."/".PES_BOOTSTRAP_SETINGS_PATH."RegisterAutoloader.php";
if (is_readable($bootstrapVar)) {
    $bootstrapLoggerArray[] = "Bootstrap: include $bootstrapVar";
    include $bootstrapVar;
} else {// nouzově načte composer autoload ze složky Pes pro objekty v Bootstrap
    $bootstrapVar = substr(__DIR__, 0, strrpos(str_replace("\\", "/", __DIR__), "vendor/")+7)."autoload.php";
    if (is_readable ($bootstrapVar)) {
        $bootstrapLoggerArray[] = "Bootstrap: include $bootstrapVar";
        include $bootstrapVar;
    }
}

#### globální proměnné ####
#
####

include "SetGlobals.php";

#### logy ####
#
# Nastaví bázovou cestu, na které budou všechny cesty k logům při použití Pes FileLogger v aplikaci.
# Zapíše Bootstrap.log o průběhu bosavadních akcí Bootstrapu.
#
####
if (defined('PES_BOOTSTRAP_LOGS_BASE_PATH')) {
    FileLogger::setBaseLogsDirectory(PES_BOOTSTRAP_LOGS_BASE_PATH);
} else {
    FileLogger::setBaseLogsDirectory(getcwd());
}

if (defined('PES_DEVELOPMENT')) {
    $bootstrapLogger = FileLogger::getInstance(PES_BOOTSTRAP_LOGS_PATH, 'Bootstrap.log', FileLogger::REWRITE_LOG);
    foreach ($bootstrapLoggerArray as $bootstrapLoggerItem) {
        $bootstrapLogger->info($bootstrapLoggerItem);
    }
} else {
    $bootstrapLogger = NULL;
}
unset($bootstrapLoggerArray);
unset($bootstrapLoggerItem);
unset($bootstrapVar);

#### kontrola a nastavení prostředí ####
#
####

// doporučení k nastavené PHP (php.ini)
include "CheckPhpIni.php";
// časké timezone a locale
include "SetCzechZone.php";
// error reporting, error handlery, exception handlery
include "SetErrorHandling.php";
// Asserce (zend.assertions´) a expektace (assert.exception) nastavované podle stroje, na kterém kód běží - dáno globálními proměnnými
include "SetAssertionsExpectations.php";

unset($bootstrapLogger);
