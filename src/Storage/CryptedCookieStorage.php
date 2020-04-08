<?php

namespace Pes\Storage;

use Pes\Cryptor\CryptorInterface;

class CryptedCookieStorage extends StorageAbstract {
    private static $cookies=array();

    // Proměnné ukládané do cookie se jménem COOKIENAME metodou _pack() a načítané z cookie metodou _unpack()
    private $created;
    private $content;
    private $version;

    private $cryptor;

    //Nastavení formátu Cookie
    const MY_VERSION = '0.1';
    const EXPIRATION_TIME = '6000'; //doba vypršení cookie '6000'
    const REISSUE_TIME = '300'; //doba po které se obnoví-znovu vydá cookie '300'
    const GLUE = '|';

    // klíč pro default cryptor - šifruje s konstantním klíčem - uloženým v kódu třídy
    const KEY = '8Fsfr9Ksxxc0008jj81'.self::GLUE.self::MY_VERSION;

    /**
     * Jako nepovinný parametr přijímá šifrovací objekt typu CryptorInterface.
     * Pokud není zadán používá pro šifrování Pes\Cryptor\CryptorBlowfish() a jako klíč konstantu zapsanou v kódu třídy.
     *
     * @param CryptorInterface $cryptor
     */
    public function __construct(CryptorInterface $cryptor=NULL) {
        assert(FALSE, 'Neimplementováno');
        $this->cryptor = $cryptor ?? new Pes\Cryptor\CryptorBlowfish(self::KEY);
    }

    /**
     * Metoda přečte obsah crypt cookie. Pokud byla cookie se zadaným jménem nově vytvořena v aktuálním běhu skriptu, metoda přečte
     * obsah této nové cookie. Jinak metoda přečte obsah z pole $_COOKIE.
     * @param type $key Klíč - jméno cookie
     * @return mixed/boolean Vpřípadě úspěšného přečtení crypt cookue metoda vrací obsah cookie, jinak FALSE.
     */
    public function get($key) {
        $index = $this->vali($key);
        if (isset(self::$cookies[$key])) {
            $this->unpack(self::$cookies[$key]);
            return $this->content;
        } elseif (isset($_COOKIE[$key])) {
            $this->unpack($_COOKIE[$key]);
            return $this->content;
        }
        return FALSE;
    }

    /**
     * Metoda zapíše crypt cookie se zadaným jménem a hodnotou
     * Metoda vytvoří cookie voláním php funkce setcookie(). Metoda používá pouze parametry funkce setcookie() name, value a httponly.
     * parametr name - je nastaven na hodnotu konstanty třídy COOKIENAME,
     * parametr value - je nastaven na hodnotu vrácená metodou třídy _pack().
     * parametr httponly - je nastaven na TRUE pro drobné zvýšení bezpečnosti (viz dokumentace setcookie() )
     * Metoda nepoužívá ostatní parametry funkce setcookie(), nastavuje defaultní hodnoty (viz dokumentace setcookie() ). Vytvořená cookie
     * tedy má platnost do konce session (zavření prohlížeče), platí pro doménu ze které byla odeslána a posílá i nezabezpečeným připojením.
     * @param string $key Klíč - jméno cookie
     * @param type $value
     * @return bool TRUE když funkce setcookie() proběhla úspěšně a vytvořená cookie je připravena na výstupu, jinak FALSE
     */
    public function set($key, $value) {
        $index = $this->checkKeyValidity($key);
        $this->content = $value;
        $this->version = self::MY_VERSION;
        $this->created = time();
        $cookie = $this->pack();
        //function setcookie ($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false) {}
        $res = setcookie($index, $cookie, 0, NULL, NULL, FALSE, TRUE);
        if ($res) self::$cookies[$index] = $cookie;
        return $res;
    }

    /**
     * Metoda odstraní cookie. Pokud existuje odstraní (unset) předtím nově vytvořenou cookie v aktuálním běhui skriptu,
     * odstraní (unset) prvek z pole $_COOKIE, odešle do prohůlížeče cookie s časem expirace v minulosti a tím vynutí smazání cookie
     * v prohlížeči po příjetí response prohlížečem.
     * @param string $key Klíč - jméno cookie
     * @return bool TRUE když funkce setcookie() proběhla úspěšně a vytvořená cookie je připravena na výstupu, jinak FALSE
     */
    public function remove($key) {
        $index = $this->checkKeyValidity($key);
        if (isset(self::$cookies[$index])) unset(self::$cookies[$index]);
        if(isset($_COOKIE[$index])) {
            unset($_COOKIE[$index]);
            return setcookie($index, '', time() - 3600); // čas vypršení cookie v minulosti způsobí smazání cookie v prohlížeči při příštím requestu
        }
    }

    /**
     * Metoda sloučí jednotlivé hodnoty ukládané do cookie - version, created a content a zašifruje je.
     * @return string Zašifrovaná hodnota pro uložení do cookie.
     */
    private function pack() {
        $parts = array($this->version, $this->created, $this->valueSerialize($this->content));
        $cookie = implode(self::GLUE, $parts);
        return $this->cryptor->encrypt($cookie);
    }

    /**
     * Metoda rozšifruje cookie použitím metody $this->_decrypt(), rozloží obsah cookie na jednotlivé hodnoty
     * ukládané do cookie - version, created a content. Metoda kontroluje zda všechny hodnoty mají nějaký obsah (jsou vyhodnoceny jako  true),
     * zda verze cookie odpovídá a zda nevypršela platnost cookie. Pokud některá kontrola selže, metoda vyhodí příslušnou výjimku.
     * Pokud doba života cookie překročila dobu pro znovuvydání cookie (self::REISSUE_TIME), metoda cookie obnoví voláním metody $this->_reiisue()
     * @param type $cryptedCookie Cookie, prvek pole $_COOKIE
     * @return boolean
     * @throws UnexpectedValueException
     */
    private function unpack($cryptedCookie) {
        $buffer = $this->cryptor->decrypt($cryptedCookie);
        list($this->version, $this->created, $content) = explode(self::GLUE, $buffer);
        $this->content = $this->valueUnserialize($content);
        if(!$this->version || !$this->created || !$this->content) {
            throw new UnexpectedValueException("Poškozená cookie");
        }
        if($this->version != self::MY_VERSION) {
            throw new UnexpectedValueException("Version cookie neodpovídá");
        }
        if (time() - $this->created > self::EXPIRATION_TIME) {
            throw new UnexpectedValueException("Vypršel čas platnosti cookie");
        } elseif (time() - $this->created > self::REISSUE_TIME) {
            $this->reissue();
        }
        return TRUE;
    }

    private function encrypt($plaintext) {
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($this->td), MCRYPT_RAND);
        mcrypt_generic_init ($this->td, self::KEY, $iv);
        $crypttext = mcrypt_generic ($this->td, $plaintext);
        mcrypt_generic_deinit ($this->td);
        return $iv.$crypttext;
    }

    private function decrypt($crypttext) {
        $ivsize = mcrypt_enc_get_iv_size($this->td);
        $iv = substr($crypttext, 0, $ivsize);
        $crypttext = substr($crypttext, $ivsize);
        mcrypt_generic_init ($this->td, self::KEY, $iv);
        $plaintext = mdecrypt_generic ($this->td, $crypttext);
        mcrypt_generic_deinit ($this->td);
        return $plaintext;
    }

    private function reissue() {
        $this->created = time();
    }
}
