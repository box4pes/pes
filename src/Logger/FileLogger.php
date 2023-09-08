<?php
namespace Pes\Logger;

use Psr\Log\AbstractLogger;
use Pes\Utils\Directory;
use Pes\Text\Template;

/**
 * Description of FileLogger
 * Třída loguje tak, že zapisuje do souboru. Pro každý soubor vytváří jednu istanci objektu Projektor_Model_Auto_Autocode_Logger, je to singleton
 * pro jeden logovací soubor.
 *
 * @author pes2704
 */
class FileLogger extends AbstractLogger {

    /**
     *
     * @var FileLogger array of
     */
    private static $instances = array();

    private static $baseLogsDirectory = '';

    private $loggerFullLogFileName;
    private $logFileHandle;

    const ODSAZENI = "    ";

    const REWRITE_LOG = 'replace log file';
    const APPEND_TO_LOG = 'append to existing log file';
    const FILE_PER_DAY = 'new log file for day, apend to log in one day range';

    /**
     * Privátní konstruktor. Objekt je vytvářen voláním factory metody getInstance().
     * @param Resource $logFileHandle
     */
    private function __construct($logFileHandle, $fullLogFileName){
        $this->logFileHandle = $logFileHandle;
        $this->loggerFullLogFileName = $fullLogFileName;  //proměnná jen pro přehlednost při debugování - i vrácená instance obsahuje název souboru
    }

    final public function __clone(){}

    final public function __wakeup(){}

    /**
     * Nastaví složku, do které budou ukládány logy ze všech instancí loggeru. Parametr může být zadán jako absolutní cesta i jako relativní cesta ke kořenovému skriptu.
     * První parametr metody getInstance() pak určuje podsložku této složky.
     *
     * @param string $baseLogsDirectory
     */
    public static function setBaseLogsDirectory($baseLogsDirectory) {
        self::$baseLogsDirectory = Directory::normalizePath($baseLogsDirectory);
    }

    /**
     * Factory metoda, metoda vrací instanci objektu.
     * Objekt je vytvářen jako singleton - vždy pro jeden logovací soubor. Parametr $mode určuje zda soubor je při zahájení logování přepsán novým obsahem - log
     * obsahuje zápisy jen z jednoho běhu skriptu nebo zda nový obsah je přidáván na konec souboru - log obsahuje všechny zápisy.
     *
     * @param string $logDirectoryPath Cesta k podsložce se souborem logu. Pokud byla nastavena bázová složka metodou setBaseLogsDirectory(), pak tento parametr určuje podsložku
     *      bázové složky a musí být zadán jako relativní cesta. Pokud nebyla nastavena bázová složka, musí být tento parametr zadán jako relativní cesta i jako relativní cesta
     *      ke kořenovému skriptu.
     * @param string $logFileName Název logovacího souboru (řetězec ve formátu jméno.přípona např. Mujlogsoubor.log).
     * @param type $mode
     *
     * @return FileLogger
     */
    public static function getInstance($logDirectoryPath, $logFileName, $mode = self::REWRITE_LOG) {
        if (!self::$baseLogsDirectory) {
            self::$baseLogsDirectory = getcwd();
        }
        $fullLogDirectoryPath = self::$baseLogsDirectory.Directory::normalizePath($logDirectoryPath);
        Directory::createDirectory($fullLogDirectoryPath);
        switch ($mode) {
            case self::REWRITE_LOG:
                $fopenMode = 'w+';
                $fullLogFileName = $fullLogDirectoryPath.$logFileName;
                break;
            case self::APPEND_TO_LOG:
                $fopenMode = 'a+';
                $fullLogFileName = $fullLogDirectoryPath.$logFileName;
                break;
            case self::FILE_PER_DAY:
                $fopenMode = 'a+';
                $fullLogFileName = $fullLogDirectoryPath.date('Ymd')." ".$logFileName;
                break;
            default:
                $mode = self::APPEND_TO_LOG;
                $fopenMode = 'a+';
                user_error('Zadán neznámý parametr $mode při vytváření loggeru. Použit mode APPEND_TO_LOG.', E_USER_WARNING);
                break;
        }
        if(!isset(self::$instances[$fullLogFileName])){
            $oldLogExists = is_readable($fullLogFileName);
            $handle = fopen($fullLogFileName, $fopenMode);
            if ($handle===FALSE) {
                throw new \InvalidArgumentException('Nelze vytvořit '.__CLASS__.' pro soubor: '.$fullLogFileName.', nepodařilo se soubor vytvořit.');
            }
            $loggerInstance = new self($handle, $fullLogFileName);
            switch ($mode) {
                case self::REWRITE_LOG:
                    $loggerInstance->debug("Logger start. Rewrite log file: {fullLogFileName}.", ['time'=>date('Y-m-d H:i:s'), 'fullLogFileName'=>$fullLogFileName]);
                    $loggerInstance->debug("Mode '{mode}'.", ['mode'=>$mode]);
                    break;
                case self::APPEND_TO_LOG:
                case self::FILE_PER_DAY:
                    if (!$oldLogExists) {
                    $loggerInstance->debug("Logger start. New log file created: {fullLogFileName}. ", ['time'=>date('Y-m-d H:i:s'), 'fullLogFileName'=>$fullLogFileName]);
                    $loggerInstance->debug("Mode '{mode}'.", ['mode'=>$mode]);
                    }
                    break;
                default:
                    $fopenMode = self::APPEND_TO_LOG;
                    user_error('Zadán neznámý parametr $mode při vytváření loggeru. Použit mode APPEND_TO_LOG.', E_USER_WARNING);
                    break;
            }
            self::$instances[$fullLogFileName] = $loggerInstance;
        }
        return self::$instances[$fullLogFileName];
    }

    /**
     * Zápis jednoho záznamu do logu.
     *
     * Záznam začíná prefixem uzavřeným do hranatých závorek, následuje zpráva.
     *
     * Podřetězce zprávy mohou být nahrazeny hodnotami z asociativního pole context. Metoda použije zprávu jako šablonu a ve zprávě nahradí řetězce uzavřené
     * ve složených závorkách hodnotami pole $context s klíčem rovným nahrazovanému řetězci.
     *
     * Víceřádková zpráva je uložena do více řádek logu tak, že první řádka obsahuje prefix v hranatých závorkách a další řádky jsou zleva odsazeny.
     *
     * Příklad:
     * volání logger->log('error', 'Toto je hlášení o chybě '.PHP_EOL.'v souboru {file} autora {author}.', ['file=>'Ukázka.ext', 'author'=>'Kukačka'] v čase 12:51:33 12.6.2020
     * vytvoří záznam:
     * <pre>
     * error | 2020-06-12 12:51:33 | Toto je hlášení o chybě
     *     v souboru Ukázka.ext autora Kukačka.
     * </pre>
     *
     * @param string $level Prefix záznamu zdůrazněný uzavřením do hranatých závorek
     * @param string $message Zpráva pro zaznamenání do logu
     * @param array $context Pole náhrad.
     * @return null
     */
    public function log($level, $message, array $context = array()) {
        $time = date("Y-m-d H:i:s");
        $completedMessage = isset($context) ? Template::interpolate($message, $context) : $message;
        // https://stackoverflow.com/questions/42013372/remove-control-characters-from-string-in-php
//        \p{Cc} is the unicode character class for control characters. \P{Cc} is the opposite (all that is not a control character).
//        [^\P{Cc}\r\n] is all that isn't \P{Cc}, \r and \n.
//        The u modifier ensures that the string and the pattern are read as utf8 strings.
//        If you want to preserve an other control character, for example the TAB, add it to the negated character class: [^\P{Cc}\r\n\t]
        $completedMessage = preg_replace('~[^\P{Cc}\r\n]+~u', '', $completedMessage);  // odstranéí všechny control znaky (občas tam jsou a při čtení chybového hlášení způsobí dojekm, že nastala nějaká podivná chyba)
        $completedMessage = preg_replace("/\r\n|\n|\r/", PHP_EOL.self::ODSAZENI, $completedMessage);  // odsazení druhé a dalších řádek víceřádkového message
        $newString = "$level | $time | $completedMessage".PHP_EOL;
        if (is_resource($this->logFileHandle)) {
            fwrite($this->logFileHandle, $newString);
        } else {
            user_error("Není handler k souboru logu při pokusu o zápis: $message", E_USER_WARNING);
        }
    }

    public function getLogFilePath() {
        return $this->loggerFullLogFileName;
    }

    /**
     * Metoda vrací aktuální obsah logovacího souboru..
     * @return string
     */
    public function getLogText() {
        $position = ftell($this->logFileHandle);
        $r = rewind($this->logFileHandle);
        return $position ? fread($this->logFileHandle, $position) : '';
    }

    /**
     * Magická metoda. Umožňuje například předávat objekt loggeru jako proměnnou do kontextu View - pak dojde k volání této metody
     * obvykle až když dochází k převodu view a proměnných kontextu na string. To se v Pes view obvykle dějě až na konci běhu skriptu nebo při
     * vytváření bydy responsu a v té době již log obsahuje údaje zapsané v průběhu běhu skriptu.
     *
     * @return string
     */
    public function __toString() {
        return $this->getLogText();
    }

    /**
     * Destruktor. Zavře logovací soubor.
     */
    public function __destruct() {
//        if (is_resource($this->logFileHandle)) {
//            fclose($this->logFileHandle);
//        }
    }
}

