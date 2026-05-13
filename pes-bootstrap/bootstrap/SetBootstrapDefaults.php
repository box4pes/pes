<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

// Cesta ke složce, ve které budou ukládány jednotlivé logy třídou Pes\Logger\Filelogger.
// Cesta nastavená zde je použita pro nastavení base path loggeru v Bootstrap skriptu ještě před logováním průběhu bootstrapu a tak i logy Bootstrapu budou uloženy do této cesty.
if (!defined("PES_BOOTSTRAP_LOGS_BASE_PATH")) {
    define("PES_BOOTSTRAP_LOGS_BASE_PATH", getcwd()."/");
}
$bootstrapLoggerArray[] = 'SetBootstrapDefaults: define("PES_BOOTSTRAP_LOGS_BASE_PATH";'.PES_BOOTSTRAP_LOGS_BASE_PATH.')';

// Cesta ke složce, do které budou zapisovány soubory s logy vytvářené skripty v průběhu bootstrapu
// Hodnota je relativní cesta ke kořenovému skriptu aplikace.
if (!defined("PES_BOOTSTRAP_LOGS_PATH")) {
    define('PES_BOOTSTRAP_LOGS_PATH', 'Logs/Bootstrap/');
}
$bootstrapLoggerArray[] = 'SetBootstrapDefaults: define("PES_BOOTSTRAP_LOGS_PATH";'.PES_BOOTSTRAP_LOGS_PATH.')';

// Cesta ke složce, do které budou zapisovány soubory s chybovými logy vytvářené skripty v bootstrapu včetně error a exception handlerů
// Hodnota je relativní cesta ke kořenovému skriptu aplikace.
if (!defined("PES_BOOTSTRAP_ERROR_LOGS_PATH")) {
    define('PES_BOOTSTRAP_ERROR_LOGS_PATH', 'Logs/Errors/');
}
$bootstrapLoggerArray[] = 'SetBootstrapDefaults: define("PES_BOOTSTRAP_ERROR_LOGS_PATH";'.PES_BOOTSTRAP_ERROR_LOGS_PATH.')';

###
# Příklad dalších vhodných možností - pokud tyto položky potřebujete v aplikaci, definujte je v souboru BootstrapSet.php umístěném ve kořenovém adresáři aplikace
###

/*
 * Automaticky nastaví prostředí na produkční, pokud je skript spuštěn na stroji (host) se zadaným jménem
 */
###
#  TOTO NASTAVENÍ MÁ PŘEDNOST PŘED NASTAVENÍM DEVELOPMENT/PRODUCTION POMOCÍ PROMĚNNÝCH PROSTŘEDÍ I NASTAVENÍMI FORCE_PRODUCTION NEBO FORCE_DEVELOPMENT
###
if (!defined("PES_PRODUCTION_MACHINE_HOST_NAME")) {
    define('PES_PRODUCTION_MACHINE_HOST_NAME', 'Stroj_definovaný_jako_produkční_server_v_BootstratDefaults.php');
}
$bootstrapLoggerArray[] = 'SetBootstrapDefaults: define("PES_PRODUCTION_MACHINE_HOST_NAME";'.PES_PRODUCTION_MACHINE_HOST_NAME.')';

