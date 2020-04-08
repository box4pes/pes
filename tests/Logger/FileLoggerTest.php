<?php
use PHPUnit\Framework\TestCase;

use Pes\Logger\FileLogger;

/**
 * Description of FileLoggerTest
 *
 * @author pes2704
 */
class FileLoggerTest extends TestCase {
    public function setUp():void {

    }

    public function testGetInstanceAndLog() {
        $baseLogsDir="_logs/";
        $dirRew = 'LogsFromLoggerTests/Rewrite';
        $fileRew = 'RewriteTest.log';
        $dirRewDel = 'LogsFromLoggerTests/RewriteAndDelete';
        $fileRewDel = 'RewriteTest.log';
        $dirApp = 'LogsFromLoggerTests/Append';
        $fileApp = 'AppendTest.log';
        // smaže soubory z minulého testu - soubory nelze smazat po skončení testu, protože se ještě nevykonal destruktor
        // a soubory jsou otevřené. Pokus o jejich smazání přek skončením skriptu tak vede k chybě - Permission denied
        if (is_readable("$dirRew/$fileRew")) {
            $succ = unlink("$dirRew/$fileRew");
            $succ = rmdir($dirRew);
        }

        // base
        FileLogger::setBaseLogsDirectory($baseLogsDir);

        $loggerApp = FileLogger::getInstance($dirApp, $fileApp, FileLogger::APPEND_TO_LOG);
        $loggerRewDel = FileLogger::getInstance($dirRewDel, $fileRewDel, FileLogger::REWRITE_LOG);
        $loggerRew = FileLogger::getInstance($dirRew, $fileRew, FileLogger::REWRITE_LOG);
        $this->assertTrue($loggerApp instanceof FileLogger);
        $this->assertTrue($loggerRewDel instanceof FileLogger);
        $this->assertTrue($loggerRew instanceof FileLogger);
        $this->assertTrue(is_readable("$baseLogsDir$dirApp/$fileApp"));
        $this->assertTrue(is_readable("$baseLogsDir$dirRewDel/$fileRewDel"));
        $this->assertTrue(is_readable("$baseLogsDir$dirRew/$fileRew"));

        $loggerApp->alert('FileLoggerTest v '.time());
// netestuji všechny metody log - dědí se z Psr log
        $loggerRewDel->alert('Jeden řádek. Aspoň na chvilku.');
        $loggerRew->alert('Jeden řádek.');
        $loggerRew->critical('Dva řádky. Tohle je první.'.PHP_EOL.'A tohle druhý.');
        $loggerRew->debug('Interpolovaná zpráva z testu {method} v čase {čas}.',['method'=>__METHOD__, 'čas'=>time()]);
    }

}
