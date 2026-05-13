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
// Cesta nastavená zde je použita pro nastavení base path loggeru v Bootsstrap skriptu ještě pře logování průběhu bootstrapu a tak i logy Bootstrapu budou uloženy do této cesty.
// define("PES_BOOTSTRAP_LOGS_BASE_PATH","../"); složka o úroveň nad kořenovým skriptem
// define("PES_BOOTSTRAP_LOGS_BASE_PATH", $_SERVER['DOCUMENT_ROOT'].'/MyAppLogs');  // absolutní cesta k podsložce document root
// define('PES_BOOTSTRAP_LOGS_PATH', 'Mojelogy/Bootstrap/');  // cesta ke složce, do které budou zapisovány soubory s logy vytvářené skripty v průběhu bootstrapu
// define('PES_BOOTSTRAP_ERROR_LOGS_PATH', 'Mojelogy/Errors/');  // Cesta ke složce, do které budou zapisovány soubory s chybovými logy vytvářené skripty v bootstrapu včetně error a exception handlerů

###
# Příklad dalších vhodných možností - pokud tyto položky potřebujete v aplikaci, definujte je v souboru BootstrapSet.php umístěném ve kořenovém adresáři aplikace
###

/*
 * Automaticky nastaví prostředí na produkční, pokud je skript spuštěn na stroji (host) se zadaným jménem
 * TOTO NASTAVENÍ MÁ PŘEDNOST PŘED NASTAVENÍM PROMĚNNÝCH PROSTŘEDÍ I NASTAVENÍMI FORCE_PRODUCTION NEBO FORCE_DEVELOPMENT
 */
// define('PES_PRODUCTION_MACHINE_HOST_NAME', 'Stroj_předefinovaný_jako_produkční_server_v_BootstratSet.php');

/*
 * Vynutí nastevení prostředí na produkční nebo vývojové bez ohledu na nastavení proměnných prostředí
 * Hodnota konstanty se vyhodnocuje jako bool, tedy jestli je TRUE nebo FALSE.
 */
//define('PES_FORCE_DEVELOPMENT', 'force_development');
//// nebo
//define('PES_FORCE_PRODUCTION', 'force_production');
