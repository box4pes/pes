<?php

/*
 * Příklady nastavení autoloaderu
 */

throw new Exception("V souboru Bootsrap je třeba zadat skutečné nastavení autoladeru.");

// composer autoloader
######### AUTOLOADER #######################################
require "vendor/autoload.php";    // composer autoloader - při změně struktury nebo namespace je třeba volat: composer upgrade (případně upravit composer.json)

// Pes autoloader
########## AUTOLOAD ###################################
require "../Pes/src/Autoloader/Autoloader.php";

use Pes\Autoloader\Autoloader;

$pesAutoloader = new Autoloader();
$pesAutoloader->register();
$pesAutoloader->addNamespace('Pes', '../Pes/src/'); //autoload pro namespace Pes
$pesAutoloader->addNamespace('Helper', '../Helper/'); //autoload pro namespace Helper
$pesAutoloader->addNamespace('Psr\Log', '../vendor/psr/log/Psr/Log'); //autoload pro namespace Psr\Log

// kombinace Pes autoloader pro vlastní skripty a Composer autoloader pro skripty nainstalované pomocí composeru
########## AUTOLOAD ###################################
require "../../Pes/Pes/src/Autoloader/Autoloader.php";

use Pes\Autoloader\Autoloader;

$pesAutoloader = new Autoloader();
$pesAutoloader->register();
$pesAutoloader->addNamespace('Pes', '../Pes/src/');
$pesAutoloader->addNamespace('Helper', '../Helper/');
$pesAutoloader->addNamespace('Database', 'Database/');

require "vendor/autoload.php";    // composer autoloader - při změně struktury nebo namespace je třeba volat: composer upgrade (případně upravit composer.json)
