<?php
use PHPUnit\Framework\TestCase;

use Pes\Security\Password\Password;

/**
 * Description of testOrder
 *
 * @author pes2704
 */
class PasswordTest extends TestCase {

    public function testGetPasswordHash() {
        $passwordCrypt = new Password();
        /**
         * $passwordCrypt->getPasswordHash('nazdar') trvá s XDebugem 0,3 s!
         */
        // vrácený zahashovaný string obsahuje info o algoritmu - začíná "$"
        $this->assertTrue(strpos($passwordCrypt->getPasswordHash('nazdar'), '$')===0);
        // při opakovaném volání se stejným heslem generuje jiný hash
        $this->assertNotEquals($passwordCrypt->getPasswordHash('nazdar'), $passwordCrypt->getPasswordHash('nazdar'));
    }

    /**
     */
    public function testVerifyPassword() {
        $savedHash = '';
        $bcryptHash = '$2y$12$ZPHZgFhWbPF5UfaSMT2V1eysr53Qr5kfQFrjDoD7xHpz4CYPKRsc6';  // bcrypt hash slova 'nazdar'
        $MD5hash = 'fd097a2bc27a8101d48b4885278f57f7';  // MD5 hash slova 'nazdar'

        // password crypt bez fallbacku pro starou metodou hashované hashe a bez ukládání
        $passwordCrypt = new Password();
        $this->assertFalse($passwordCrypt->verifyPassword('nazdar', $MD5hash));
        $this->assertTrue($passwordCrypt->verifyPassword('a', $passwordCrypt->getPasswordHash('a')));
        $this->assertTrue($passwordCrypt->verifyPassword('nazdar', $bcryptHash));
    }

    public function testRehashOldHash() {
        $MD5hash = 'fd097a2bc27a8101d48b4885278f57f7';  // MD5 hash slova 'nazdar'
        // ověřovadlo na MD5 hashe - vrací TRUE, pokus hash je MD5 hash zadaného hesla
        $md5Verifier = function($password, $hash) { return md5($password)===$hash;};  //   return strlen($md5) == 32 && ctype_xdigit($md5) && md5($password)===$hash;
        // testovací ukládadlo - "ukládá" nový hash do proměnné $savedHash
        $rehashedHashSaver = function($newHash) use (&$savedHash) { $savedHash = $newHash; return TRUE; };

        // test přepočtení starých MD5 hashů a jejich přeuložení
        // password crypt s fallback funkcí pro MD5 metodou hashované hashe a ukládadlem pro přepočítaný hash
        $passwordHasherWithRecalculationAndSaver = new Password($rehashedHashSaver, $md5Verifier);
        // verifyPassword() má vracet TRUE pro MD5 hash, vypočítat nový hash a ten uložit do proměnné $savedHash
        $this->assertTrue($passwordHasherWithRecalculationAndSaver->verifyPassword('nazdar', $MD5hash));
        // rehahovaný hash má být "uložen"  v proměnné $savedHash
        $this->assertTrue(strpos($savedHash, '$')===0);  // nové hashe začínají $ - nic lepšího nemám

        // test chyb E_USER_NOTICE při chybějících parametrech
        // password crypt s fallback funkcí pro MD5 metodou hashované hashe a bez ukládadla pro přepočítaný hash
        // pokud je třeba hash přepočítat a není ukládací Closure pro uložení přepočteného hashe - verifyPassword() vyhazuje E_USER_NOTICE
        $oldErrorHandler = set_error_handler(array($this, 'errorHandler'));     // vlastní error handler - PHPUnit umí jen chyby PHP, neumí user error
        $passwordHasherWithRecalculationNoSaver = new Password(NULL, $md5Verifier);
        // verifyPassword() má vracet TRUE pro MD5 hash, vypočítat nový hash a ten uložit do proměnné $savedHash
        $this->assertTrue($passwordHasherWithRecalculationNoSaver->verifyPassword('nazdar', $MD5hash));
        set_error_handler($oldErrorHandler);
        $this->assertEquals($this->errorType, 'E_USER_NOTICE');
        $this->assertStringStartsWith('Nebyla zadána closure pro uložení ', $this->errorString);
    }

    #################################################

    /**
     * Rozpoznává E_USER_xxx . Nastaví několik proměných třídy, kterí se pak mohou v testu zpracovat. Nevrací chybu ke zpracování internímu PHP handleru.
     *
     * Všechny parametry jsou nepoviné, zdá se, že některé chyby (warning) vyvolají volání handleru bez parametrů.
     * Pak při použití povinných parametrů vznikne kaskáda warningů (a stack trace) typu Missing argument 1,2,3,4...
     *
     * @param type $errno
     * @param type $errstr
     * @param type $errfile
     * @param type $errline
     * @return boolean
     */
    function errorHandler($errno=NULL, $errstr=NULL, $errfile=NULL, $errline=NULL) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall through to the standard PHP error handler
            return FALSE;
        }
        switch ($errno) {
            case E_USER_ERROR:
                $this->errorType = 'E_USER_ERROR';
                break;
            case E_USER_WARNING:
                $this->errorType = 'E_USER_WARNING';
                break;
            case E_USER_NOTICE:
                $this->errorType = 'E_USER_NOTICE';
                break;
            default:
                /* Execute PHP internal error handler */
                return FALSE;
                break;
        }
        $this->errorString = $errstr;
        $this->errorFile = $errfile;
        $this->errorLine = $errline;
        /* Don't execute PHP internal error handler */
        return TRUE;

    }
}