<?php

use Pes\Logger\FileLogger;

######### ERROR REPORTING & PROFILING & PHP ERROR LOG ########################
if (PES_DEVELOPMENT) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', PES_BOOTSTRAP_ERROR_LOGS_PATH.'PHP_error_log '.str_replace(['/', '\\'], '_', $_SERVER['PHP_SELF']).'.log'); // Logging file
//    ini_set('log_errors_max_len', 1024); // Logging mesage size!
    error_reporting(E_ALL);
//    display_errors = On
//    display_startup_errors = On
//    error_reporting = -1
//    log_errors = On
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
//    display_errors = Off
//    display_startup_errors = Off
//    error_reporting = E_ALL
//    log_errors = On
}

//ini_set('xdebug.show_exception_trace', '1');
//ini_set('xdebug.collect_params', '4');
//ini_set('xdebug.profiler_enable', '1');

######### DEFINICE EXCEPTION A ERROR HANDLERŮ ##############
function flushOutputBuffer() {
    $obContent = '';
    while (ob_get_level()) {
        $ob = ob_get_clean();
        if ($ob!==\FALSE) {
            $obContent .= $ob;
        }
    }
    echo $obContent;
}

/**
 * Exception handler zachytává všechny výjimky a loguje je jako critical.
 * Následně:
 * - v development prostředí výjimku znovu vyhodí, pokud byla výjimka instance Throwable vyhodí ErrorException, jinak vyhodí původní výjimku
 * - mimo development prostředí vypíše omluvné hlášení
 *
 * Předpokládá české texty v chybových hlášeních, proto před vyhozením výjimky vypíše html s hlavičkou Content-Language: cs, ta obvykle zajistí, že české texty jsou česky.
 */
function loggingExceptionHandler(\Throwable $e) {
    $development= PES_DEVELOPMENT ? TRUE : FALSE;

    $exceptionsLogger = FileLogger::getInstance(PES_BOOTSTRAP_ERROR_LOGS_PATH, 'ExceptionsLogger.log', FileLogger::APPEND_TO_LOG);
    $time = date("Y-m-d H:i:s");
    if (class_exists('\\Error') AND $e instanceof \Error) {
        $exceptionsLogger->critical(get_class($e)." [$time] {$e->getMessage()} on line {$e->getLine()} in file {$e->getFile()}");
    } else {
        $exceptionsLogger->critical(get_class($e)." exception [$time] {$e->getMessage()} on line {$e->getLine()} in file {$e->getFile()}");
    }

    if ($development) {

//        if (class_exists('\\Error') AND $e instanceof \Error) {
//            $eClass = get_class($e);
//            $eMessage = $e->getMessage();
//            $eCode = $e->getCode();
//            $eFile = $e->getFile();
//            $eLine = $e->getLine();
//            $ePrevious = $e->getPrevious();
//            $eTrace = $e->getTrace();
//            $eTracestring = $e->getTraceAsString();
//            flushOutputBufferAndThrowException (new ErrorException(get_class($e).' '.$e->getMessage(), $e->getCode(), 1, $e->getFile(), $e->getLine(), $e));
//        } else {
//            flushOutputBufferAndThrowException ($e, __FUNCTION__);
//        }

        flushOutputBuffer ();
        $info = (__FUNCTION__ . 'error handler nebo exception handler').' v Pes\Bootstrap - ';
        throw new Exception("Výjimka zpracována funkcí $info", 0, $e);

    } else {
//TODO: Umožnit použití custom stránky
        http_response_code(500);
        echo
       '<html>
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="Content-Language" content="cs">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body>';
        echo '<h4  style="border: 8px solid tomato; padding: 1em; background-color: lightyellow;">Tento web je mimo provoz. Velice se omlouváme.';
        echo '<p> V '.$time.' nastala nečekaná výjimka.</p>';
        echo '</body>
        </html>';
    }
}

/**
 * Pomocná funkce pro error handlery - překládá typ (číslo) chyby na kód chyby (např. typ 2 na řetězec E_WARNING).
 * Pro nerozpoznaný typ chyby vrací původní hodnotu.
 *
 * @param integer $errno Číslo chyby
 * @return string Jméno PHP konstanty s hodnotou odpovídající říslu chyby
 */
function erroNumbeRrToType($errno)
{
    switch($errno)
    {
        case E_ERROR: // 1 //
            return 'E_ERROR';
        case E_WARNING: // 2 //
            return 'E_WARNING';
        case E_PARSE: // 4 //
            return 'E_PARSE';
        case E_NOTICE: // 8 //
            return 'E_NOTICE';
        case E_CORE_ERROR: // 16 //
            return 'E_CORE_ERROR';
        case E_CORE_WARNING: // 32 //
            return 'E_CORE_WARNING';
        case E_COMPILE_ERROR: // 64 //
            return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING: // 128 //
            return 'E_COMPILE_WARNING';
        case E_USER_ERROR: // 256 //
            return 'E_USER_ERROR';
        case E_USER_WARNING: // 512 //
            return 'E_USER_WARNING';
        case E_USER_NOTICE: // 1024 //
            return 'E_USER_NOTICE';
        case E_STRICT: // 2048 //
            return 'E_STRICT';
        case E_RECOVERABLE_ERROR: // 4096 //
            return 'E_RECOVERABLE_ERROR';
        case E_DEPRECATED: // 8192 //
            return 'E_DEPRECATED';
        case E_USER_DEPRECATED: // 16384 //
            return 'E_USER_DEPRECATED';
    }
    return $errno;
}

/**
 * Funkce zaregistrovaná jako error handler převede všechny chyby na výjimku typu ErrorException.
 * Funkci lze zaregistrovat jako error handler voláním set_error_handler("exception_error_handler");
 *
 * @param type $errno
 * @param type $errstr
 * @param type $errfile
 * @param type $errline
 * @return boolean
 * @throws ErrorException
 */
function exceptionErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {  // Volání error_reporting() bez patametru vrací aktuálně nastavenou hodnotu error_reporting.
        // Tento error kód $errno není rozpoznáván při nastevené útovni error repotingu, handelr brací FALSE => to způsobí,
        // že zpracování chyby je předáno PHP, to zavolá standartní error handler.
        return FALSE;
    }
    flushOutputBuffer( new ErrorException($errstr, 0, $errno, $errfile, $errline));
}

// varianta pro produkci:
// log_error_handler obsluhuje chyby, které nejsou potlačené podle úrovně nastavené v error_reporting
// Rozlišuje chyby s číslem E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE.
// Pro (chyby s jiným číslem než E_USER_...) vyhazuje výjimku ErrorException.
/**
 * Funkce zaregistrovaná jako error handler všechny chyby loguje, rozpoznává chyby typu E_USER a zachází s nimi podle závažnosti.
 * V development prostředí následně chybu předá zpět ke zpracování systému PHP, mimo development prostředí zpracování chyby končí.
 * Funkci lze zaregistrovat jako error handler voláním set_error_handler("logErrorHandler");
 *
 *
 *
 * @param type $errno
 * @param type $errstr
 * @param type $errfile
 * @param type $errline
 * @return boolean
 * @throws ErrorException
 */
function loggingErrorHandler($errno, $errstr, $errfile, $errline) {

    $development= PES_DEVELOPMENT ? TRUE : FALSE;

    if (!(error_reporting() & $errno)) {  // Volání error_reporting() bez patametru vrací aktuálně nastavenou hodnotu error_reporting.
        // Tento error kód $errno není rozpoznáván při nastevené útovni error repotingu, handelr brací FALSE => to způsobí,
        // že zpracování chyby je předáno PHP, to zavolá standartní error handler.
        return FALSE;
    }

    $errorLogger = FileLogger::getInstance(PES_BOOTSTRAP_ERROR_LOGS_PATH, 'ErrorsLogger.log', FileLogger::APPEND_TO_LOG);
    $time = date("Y-m-d H:i:s");

    switch ($errno) {
        case E_USER_ERROR:
            $errorLogger->error("E_USER_ERROR [$errno] [$time] $errstr on line $errline in file $errfile");
            $errorLogger->error("Aborting...<br />\n");
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);  //chyby převádím na výjimky
    //        break;
        case E_USER_WARNING:
            $errorLogger->warning("E_USER_WARNING [$errno] [$time] $errstr on line $errline in file $errfile");
            break;
        case E_USER_NOTICE:
            $errorLogger->notice("E_USER_NOTICE [$errno] [$time] $errstr on line $errline in file $errfile");
            break;
        default:
            if (function_exists('erroNumbeRrToType')) {
                $errType = erroNumbeRrToType($errno);
            } else {
                $errType = '??';
            }
            $errorLogger->debug("PHP error or unknown user error type: $errType [$errno] [$time] $errstr on line $errline in file $errfile");
            break;
    }
    if ($development) {
    /* Vracím FALSE = vyvolání dalšího zpracování chyby pomocí standartního interního error handleru PHP */
        return FALSE;
    }
    /* Vracím TRUE = potlačeno další zpracování chyby pomocí standartního interního error handleru PHP */
    return TRUE;
}


######### SPUŠŤĚNÍ EXCEPTION A ERROR HANDLERŮ ##################
set_exception_handler('loggingExceptionHandler');

set_error_handler("loggingErrorHandler");
//set_error_handler("exceptionErrorHandler");
