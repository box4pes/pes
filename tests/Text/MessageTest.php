<?php
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Error\Warning;

use Pes\Text\Message;

use Pes\Logger\FileLogger;

/**
 * Test Pes\Text\Message.
 * Pes\Text\Message interně používá statickou třídu Pes\Text\Template.
 * Tento test používá Pes\Logger\FileLogger.
 *
 * @author pes2704
 */
class MessageTest extends TestCase {

    /**
     *
     * @var Pes\Logger\FileLogger
     */
    private static $logger;


    public static function setUpBeforeClass(): void {
        $baseLogsDir="../Tests_logs/";
        FileLogger::setBaseLogsDirectory($baseLogsDir);

        self::$logger = FileLogger::getInstance('LogsFromMessageTests', 'Messages.log');
        Message::setLogger(self::$logger);
        Message::addTranslations(
                [
                    'Testovací text!!' =>
                    [
                        'en' => 'Text for testing purposes..',
                        'cs' => 'Testovací text správně česky!!'
                    ],
                ]);
    }

    public function testWarningNeniLocale() {
        $this->expectException(Warning::class);
        $t = Message::t('Testovací text!!');
    }

    public function testNoticeNeniPreklad() {
        $this->expectException(Notice::class);
        Message::setAppLocale('de');
        $t = Message::t('Takový text neznám.');
    }

    public function testNoticeNeniPrekladProZadanyJazyk() {
        $this->expectException(Notice::class);
        Message::setAppLocale('de');
        $t = Message::t('Testovací text!!');
    }

    public function testTranslate() {
        Message::setAppLocale('cs-CZ');
        $this->assertEquals('Testovací text správně česky!!', Message::t('Testovací text!!'));
        Message::setAppLocale('en-US');
        $this->assertEquals('Text for testing purposes..', Message::t('Testovací text!!'));
        Message::setAppLocale('cs');
        $this->assertEquals('Testovací text správně česky!!', Message::t('Testovací text!!'));
        Message::setAppLocale('en');
        $this->assertEquals('Text for testing purposes..', Message::t('Testovací text!!'));
    }

    public function testAddTranslationRepeated() {
        Message::addTranslations(
                [
                    'Testovací text!!' =>
                    [
                        'de' => 'Text richtig auf Deutsch testen !!'
                    ],
                ]);
        Message::setAppLocale('de-DE');
        $this->assertEquals('Text richtig auf Deutsch testen !!', Message::t('Testovací text!!'));
        Message::setAppLocale('de');
        $this->assertEquals('Text richtig auf Deutsch testen !!', Message::t('Testovací text!!'));
    }

}
