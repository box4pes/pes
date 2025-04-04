<?php

namespace Pes\Session;

use Pes\Session\SessionStatusHandlerInterface;
use Psr\Log\LoggerInterface;

use SessionHandlerInterface;
use LogicException;
use RuntimeException;

/**
 * SessionStatusHandler
 *
 */
class SessionStatusHandler implements SessionStatusHandlerInterface {

    // dafault jméno session cookie
    const SESSION_NAME = 'PES_SESSION';
    // oddělovač indexů fragmentů a promenných v klíči pole $_SESSION - tento znak nesmí být obsažen v indexech
    const FRAGMENT_SEPARATOR = '.';
    // index fragmentu pro uložaní interních proměnných handleru
    const HANDLER_VARS = '__session_handler_vars';
    // indexy interních proměnných handleru
    const CREATION_TIME = 'creation_time';
    const PREVIOUS_START_TIME = 'previous_start_time';
    const CURRENT_START_TIME = 'current_start_time';
    const FINGERPRINT = 'fingerprint';
    const FINGERPRINT_WARNING = 'fingerprint_warning';
    const IS_NEW = 'is_new';

    // maska pro kontrolu, nezměněné IP adresy klienta - lockToIp
    const IP_MASK = '255.255.0.0';

    private $name, $fingerprintBasedAutodestuction, $lockToUserAgent, $lockToIp, $sessionCookieParams, $sessionIdDurability, $manualStartStop;
    
    /**
     * @var SessionHandlerInterface
     */
    private $sessionSaveHandler;
    private $sessionHandlerVars;   // reference na $_SESSION[self::HANDLER_VARS]

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Konstruktor
     * <p>Pro správné nastavení parametrů session cookie vyžaduje PHP 7.3 a vyšší (pro správné nastavení samesite)</p>
     *
     * <p><b>Bezpečnostní vazby handleru</b></p>
     * <p>Session data mohou být vázána na klientskou aplikaci (prohlížeč) a případně na IP adresu klienta. Tato třída může používat dva typy vazby:
     * - Vazba na klientskou aplikaci je vazba na signaturu prohlížeče poskytovanou v hlavičce User-Agent.
     * - Vazba na IP adresu je vazba na horní dva byty IP adresy (maska 255.255.0.0), dolní dva byty se mohou měnit libovolně. IP adresa klienta v lokální síti se může měnit často,
     * např. za farmou proxy se může měnit pro každý request. Vazba na horní dva byty IP je kompromis, umožňuje klientům měnit IP adresu
     * v rámci 16ti bitového rozsahu adres.</p>
     * <p>Pokud dojde k porušení vazby (např změní se klient nebo IP adresa), data session jsou smazána a je nastartována nová session.</p>
     *
     * <p><b>Default nastavení:</b></p>
     * <p>Vazba na klientskou aplikaci (prohlížeč) = TRUE</p>
     * <p>Vazba na IP adresu = FALSE</p>
     * <p>Parametry cookie používané pro předávání identifikátoru session:
     *  - lifetime = 0 - tedy životnost do konce session
     *  - path = parametr session.cookie_path ze souboru php.ini - ini_get('session.cookie_path')
     *  - domain = parametr session.cookie_domain ze souboru php.ini - ini_get('session.cookie_domain')
     *  - secure = TRUE, pokud protokol je HTTPS, jinak je FALSE
     *  - httponly = TRUE
     *  - samesite = 'lax'</p>
     *
     * <p><b>Nastavení parametrů sesion</b></p>
     * <p>Třída vždy použije vlastní nastavení session.cookie_lifetime, session.use_cookies, session.use_only_cookies, nepoužívá nastavení zadané v souboru php.ini</p>
     * <p>Vlastní  nastavení:
     *  - session nikdy neexpirují, skutečná session lifetime je dána pouze hodnotou parametru konstruktoru $sessionCookieParams['lifetime'], default hodnota je O,
     *    cookie identifikátoru session tedy zanikne v okamžiku zavření prohlížeče, defaultně je tak nastaveno: ini_set('session.cookie_lifetime', 0);
     *  - session nebude viditelná pro skripty - obrana proti session ID hijacking: ini_set('session.use_cookies', 1);
     *  - session ID bude předáváno pouze v cookie, nebude nikdy součástí GET parametrů: ini_set('session.use_only_cookies', 1);
     *</p>
     * @param string $sessionName Jméno session
     * @param SessionHandlerInterface $sessionSaveHandler Handler pro čtení a ukládání dat session. Implementuje PHP SessionHandlerInterface
     * @param bool $fingerprintBasedAutodestuction Session budoe automaticky smazána při změně prohlížeče nebo IP adresy, pokud byla tvazba nastavena. Session bude nastartována jako nová, výchozí hodnota je TRUE.
     * @param bool $lockToUserAgent Platnost session je vázána na klientskou aplikaci (prohlížeč), pro kterou byla session nastartována, výchozí hodnota je true
     * @param bool $lockToIp Platnost session dat je vázána na IP adresu klienta, lze použít jen v prostředí se stabilními IP adresami, výchozí hodnota je false
     * @param array $sessionCookieParams Pole parametrů cookie používané pro předávání identifikátoru session. Lze zadat pouze ty parametry, které mění default hodnoty. Přípustné klíče pole jsou?
     *              'lifetime', 'path', 'domain', 'secure', 'httponly'. Default hodnoty jsou popsány výše.
     * @param integer $sessionIdDurability Průměrný počet použití session dat bez regenerování identifikátoru session
     * @param boolean $manualStartStop Je vyžadováno spuštění a zastavení session voláním metod sessionStart() a sessionFinish(), defaultně false.
     * @param boolean $closeOnShutdown Defaultní hodnota je true. Pokud je true, je funkce session_write_close() zaregistrována jako shutdown funkce (register_shutdown_function()). 
     *              Pokud je jako session_save_handler použit objekt (to v PES package je) je shutdown funkce je volána při destrukci objektu handleru při ukončování skriptu. 
     *              Zaregistrování  tak způsobí, že volání session_write_close() nastane v destruktoru správně až po uvolnění všech referencí na závislosti. Závislostí zde je 
     *              např. objekt logger, bez registrování se může stát, že logger je destruován před tím, než je volána session_write_close(), session_write_close() pokud je 
     *              použit session_save_handler vnitřně volá metody session save handleru write() a close() a pokud v těchto metodách je volání loggeru nebude již existovat 
     *              objekt loggeru.
     *              Pozor! Musí být zaručeno, že data session jsou v handleru připravena před zahájením shutdown sekvence PHP. Pokud jsou data ukládána do $_SESSION 
     *              v destruktorech dojde v komplexnějším skriptu snadno k tomu, že session_write_close() je v shutdown sekvenci
     *              zavolána dříve, než je volán destruktor a do úložiště jsou zapsána chybná (stará) data. Tedy chci-li používat logování nemůžu v destruktorech ještě zapisovat data do $_SESSION.
     * 
     * @throws \RuntimeException Nepodařilo se nastartovat session.
     */
    public function __construct(
            $sessionName = self::SESSION_NAME,
            SessionHandlerInterface $sessionSaveHandler = null,
            $fingerprintBasedAutodestuction = true,
            $lockToUserAgent = true,
            $lockToIp = false,
            array $sessionCookieParams = [],
            $sessionIdDurability = 5,
            $manualStartStop = false,
            $closeOnShutdown = true
        ) {

        $this->sessionSaveHandler = $sessionSaveHandler;
        $this->name = $sessionName;
        $this->fingerprintBasedAutodestuction = $fingerprintBasedAutodestuction;
        $this->lockToUserAgent = $lockToUserAgent;
        $this->lockToIp = $lockToIp;    // ip se může měnit při každém requestu - např pokud klient je za farmou proxy
        $this->sessionCookieParams = $sessionCookieParams + [
            'lifetime' => 0,
            'path'     => ini_get('session.cookie_path'),
            'domain'   => ini_get('session.cookie_domain'),
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'lax',
        ];
        // integer od 0 do 100
        $this->sessionIdDurability = $sessionIdDurability>0 ? ($sessionIdDurability>100 ? 100 : (int) $sessionIdDurability) : 0;
        $this->manualStartStop = $manualStartStop;

        // nastaveno - session nikdy neexpirují, skutečná session lifetime je dána pouze hodnotou  $this->sessionCookieParams['lifetime']
        ini_set('session.cookie_lifetime', '0');
        // session nebude viditelná pro skripty - obrana proti session ID hijacking
        ini_set('session.use_cookies', '1');
        // session ID bude předáváno pouze v cookie, nebude nikdy součástí GET parametrů
        ini_set('session.use_only_cookies', '1');

        session_name($this->name);

        session_set_cookie_params(
            $this->sessionCookieParams['lifetime'],
            $this->sessionCookieParams['path'],
            $this->sessionCookieParams['domain'],
            $this->sessionCookieParams['secure'],
            $this->sessionCookieParams['httponly']
        );

        if ($this->sessionSaveHandler) {
            if ($closeOnShutdown) {
//                Warning: When using objects as session save handlers, it is important to register the shutdown function with PHP to avoid unexpected side-effects from the way PHP internally destroys objects on shutdown and may prevent the write and close from being called. Typically you should register 'session_write_close' using the register_shutdown_function() function.                 
                session_set_save_handler($this->sessionSaveHandler, true);  //druhý parametr=TRUE -> registruje session_write_close() jako a register_shutdown_function() funkci.
            } else {
                session_set_save_handler($this->sessionSaveHandler, false);
            }
        }

        if (!$this->manualStartStop) {
            try {
                $this->sessionStart();
            } catch (Exception $exc) {
                throw new RuntimeException("Nepodařilo se nastartovat session.", 0, $exc);                
            }
        }
    }

######## METODY PRO ŘÍZENÍ SESSION A PRÁCI S INTERNÍMI DATY SESSION HANDLERU - VOLAJÍ PHP FUNKCE PRO ŘÍZENÍ SESSION #######################################

    /**
     * Metoda je automaticky volána z konstruktoru, pokud není nastavena volba manualStartStop.
     *
     * Nastaví parametry session na základě parametrů konstrukroru a nastartuje session. Je nutné ji zavolat, jinak nezačne 
     * ukládání dat pomocí session handleru.
     * 
     * Tuto metodu je třeba volat jen v případě, když parametr konstruktoru $manualStartStop nastaven na true, jinak dojde 
     * v konstruktoru k automatickému staru session (dafault hodnota). V případě automatického startu session vyvolá následné 
     * volání metody session_start() výjimku LogicException.
     * 
     * Vrací doplňující informaci o případném znovunastartování session. Session je restartována při překročení času session durability 
     * (parametr konstruktoru) nebo při změně fingeprintu session (parametr konstruktoru).
     * 
     * @return bool Vrací true v prípadě restartu session.
     * @throws LogicException Při pokusu nastartovat již nastartovanou session
     */
    final public function sessionStart() {
        if (session_status() == PHP_SESSION_NONE) {
            if (session_start()) {      // session_start() vyvolá: open($sessionSavePath, $sessionName) a read($sessionId)
                $this->prepareOrRegenerate();
                if (isset($this->logger)) {
                    $this->logger->debug("SessionStatusHandler: Start, byla načtena data session a nastartována session. Data session: {data}", ['data'=> print_r($this->getArrayReference(), \TRUE)]);
                }
            }
        } else {
            throw new LogicException("Session již byla nastartována dříve. Nelze znovu nastartovat session.");
        }
    }
    
    /**
     * Nastaví parametry session na základě parametrů konstrukroru, přečte uložená session data a znovu nastartuje session.
     * Pokud session běží již při volání metody, vyhodí výjimku.
     * 
     * @throws LogicException
     */
    final public function sessionReset() {
        if (session_status() == PHP_SESSION_NONE) {
            if (session_reset()) {      // session_start() vyvolá: open($sessionSavePath, $sessionName) a read($sessionId)
                $this->prepareOrRegenerate();
                if (isset($this->logger)) {
                    $this->logger->debug("SessionStatusHandler: Reset, byla načtena data session a znovu nastartována session. Data session: {data}", ['data'=> print_r($this->getArrayReference(), \TRUE)]);
                }
            }
        } else {
            throw new LogicException("Session je nastartována (není ukončena). Nelze obnovit session.");
        }
    }
    
    private function prepareOrRegenerate() {
        $this->setSessionHandlerVariables();
        // autodestrukce session, pokud nesouhlasí fingerprint
//        $this->autodestructOnFingerprintChange();
        // refresh session podle $this->sessionIdDurability
//        $this->refreshSessionOnDurabilityExceeding();        
    }
    
    private function setSessionHandlerVariables() {
        $this->sessionHandlerVars = & $_SESSION[self::HANDLER_VARS];  // reference

        // pro novou session nastaví flag IS_NEW_SESSION a vyrobí fingerprint a čas vytvoření
        if (!isset($this->sessionHandlerVars)) {  // první request v session
            $this->setNewSessionHandlerVars();
        } elseif ($this->sessionHandlerVars[self::IS_NEW] ?? FALSE) {    // následný request po prvním, kdy byla nastavena session new
            $this->sessionHandlerVars[self::IS_NEW] = FALSE;
        }

        // časy obnovení session
        $this->sessionHandlerVars[self::PREVIOUS_START_TIME] = $this->sessionHandlerVars[self::CURRENT_START_TIME] ?? NULL;
        $this->sessionHandlerVars[self::CURRENT_START_TIME] = time();        
    }
    
    private function autodestructOnFingerprintChange() {
        if ($this->fingerprintBasedAutodestuction AND !$this->hasFingerprint()) {
            $this->forget();  // provede sesiion close
            if (session_start()) {
                $this->setNewSessionHandlerVars();
                $this->sessionHandlerVars[self::FINGERPRINT_WARNING] = "Session destroyed, bad fingerprint.";
                if (isset($this->logger)) {
                    $this->logger->debug("Session byla smazána, došlo ke změně fingerprintu. Nastartována nová session.");
                }
                return;
            } else {
                throw new RuntimeException("Session byla zničena z důvodu změny fingerprint. Session se následně nepodařilo znovu nastartovat.");            
            }
        }            
    }
    
    private function refreshSessionOnDurabilityExceeding() {
        if ($this->sessionHandlerVars[self::IS_NEW] == FALSE && mt_rand(1, $this->sessionIdDurability) === 1) {  // náhodné int číslo mezi min, max
            if ($this->regenerate()) {
                return;
            } else {
                throw new RuntimeException("Byla překročena doba trvanlivosti session (viz parametr konstruktoru session durabulity)"
                        . ", ale session se následně nepodařilo regenerovat.");                
            }
        }        
    }
    
    private function setNewSessionHandlerVars() {
        $this->sessionHandlerVars[self::IS_NEW] = TRUE;
        $this->sessionHandlerVars[self::CREATION_TIME] = time();
        $this->sessionHandlerVars[self::FINGERPRINT] = $this->getFingerprintHash();
    }

    /**
     * Nastaví loger.
     * @param LoggerInterface $logger
     * @return SessionStatusHandlerInterface
     */
    public function setLogger(LoggerInterface $logger): SessionStatusHandlerInterface {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Zapíše data session do úložiště a ukončí fungování session handleru.
     */
    public function sessionFinish() {
        if (session_status() == PHP_SESSION_ACTIVE) {
            if (isset($this->logger)) {
                $this->logger->debug("SessionStatusHandler: Finish, budou uložena data session a ukončena session. Data sesiion: {data}", ['data'=> print_r($this->getArrayReference(), \TRUE)]);
            }
            session_write_close();
        } else {
            throw new LogicException("Session již byla ukončena dříve. Nelze znovu ukončit session.");
        }
    }

    /**
     * Smaže data ukládaná session handlerem a také cookie používané pro předávání identifikátoru session.
     * @return boolean
     */
    public function forget() {
        if (session_status() == PHP_SESSION_NONE) {
            return false;
        }

        $_SESSION = [];
        setcookie(
            $this->name,
            '',
            time() - 42000,
            $this->sessionCookieParams['path'],
            $this->sessionCookieParams['domain'],
            $this->sessionCookieParams['secure'],
            $this->sessionCookieParams['httponly']
        );
        if (isset($this->logger)) {
            $this->logger->debug("SessionStatusHandler: Forget, byla smazána data session a zničena session.");
        }
        return session_destroy();       // session_destroy() vyvolá: destroy($session_id) a close()
    }

    /**
     * Regeneruje identifikátor session
     *
     * @return bool
     */
    public function regenerate() {
        // http://php.net/manual/en/function.session-regenerate-id.php
        // Warning

//Current session_regenerate_id does not handle unstable network well. e.g. Mobile and WiFi network. Therefore, you may experience
//lost session by calling session_regenerate_id.
//You should not destroy old session data immediately, but should use destroy time-stamp and control access to old session ID.
//Otherwise, concurrent access to page may result in inconsistent state, or you may have lost session, or it may cause client(browser)
//side race condition and may create many session ID needlessly. Immediate session data deletion disables session hijack attack detection
//and prevention also.

        // v PHP5 (5.4?) session_regenerate_id vyvolá: create_sid() |
        // v PHP7  session_regenerate_id vyvolá: destroy($session_id), close(), open($save_path, $session_name), create_sid(), read($nove_session_id)
        return session_regenerate_id(true);  // smaž starý soubor se session daty
    }

######## METODY PRO PRÁCI S UŽIVATELSKÝMI DATY SESSION - NEVOLAJÍ FUNKCE PRO ŘÍZENÍ SESSION #######################################

    /**
     * Vrací TRUE, pokud session byla nastartována poprvé v průběhu trvání skutečného sezení klienta.
     *
     * @return bool
     */
    public function isNew() {
        return $this->sessionHandlerVars[self::IS_NEW];
    }

    /**
     * Vrací čas vytvoření (prvního startu) session. Čas je unix timestamp.
     *
     * @return int
     */
    public function getCreationTime() {
        return $this->sessionHandlerVars[self::PREVIOUS_START_TIME];
    }

    /**
     * Vrací čas předchozího startu session. Čas je unix timestamp.
     *
     * @return int
     */
    public function getLastStartTime() {
        return $this->sessionHandlerVars[self::PREVIOUS_START_TIME];
    }

    /**
     * Vrací čas startu session. Čas je unix timestamp.
     *
     * @return int
     */
    public function getCurrentStartTime() {
        return $this->sessionHandlerVars[self::CURRENT_START_TIME];
    }

    /**
     * Metoda slouží k ověřování, že v průběhu trvání sezení klienta nedošlo ke změně aplikace nebo IP adresy.
     *
     * Na začátku trvání sezení, t.j. při prvním  instancování session handleru v průběhu sezení, session handler automaticky nastaví otisk a
     * při dalších voláních v průběhu trvání sezení klienta kontroluje shodu otisku.
     * Otisk je založen na jménu aplikace - klienta (HTTP_USER_AGENT) a případně horních 16 bbitech IP adresy klienta. Skutečný obsah otisky je dán
     * nastavením parametrů konstruktoru. V konstruktoru je také nastaveno, že session bude automaticky zrušena, pokud dojde ke změně otisku. Tuto volbu
     * lze parametrem konstruktoru změnit a pak kontrolovat otisk touto metodou.
     *
     * @return boolean Výsledek kontroly shody otisku, pokud je shodný vrací TRUE, jinak FALSE.
     */
    public function hasFingerprint() {
        return isset($this->sessionHandlerVars[self::FINGERPRINT]) ? hash_equals($this->sessionHandlerVars[self::FINGERPRINT], $this->getFingerprintHash()) : FALSE;
    }

    /**
     * Vrací hodnotu se zadaným jménem.
     *
     * @param string $name
     * @return string || NULL
     */
    public function get($name) {
        $parsed = $this->parse($name);
        $fragment = & $this->prepareFragment($parsed);
        return $fragment[array_shift($parsed)] ?? NULL;  // v $parsed je pole s jedním prvkem - indexem prvku ve fragmentu
    }

    /**
     * Nastaví hodnotu zadanému jménu.
     *
     * @param string $name
     * @param string $value
     */
    public function set($name, $value) {
        $parsed = $this->parse($name);
        $fragment = & $this->prepareFragment($parsed);
        $fragment[array_shift($parsed)] = $value;  //zde měním přímo $_SESSION, v $parsed je pole s jedním prvkem - indexem prvku ve fragmentu
    }

    /**
     * Smaže fragment nebo jednu hodnotu se zadaným jménem.
     *
     * @param string $name Jméno hodnoty (např. user.status) nebo jméno fragmentu (npř. user)
     */
    public function delete($name) {
        $parsed = $this->parse($name);
        $fragment = & $this->prepareFragment($parsed);
        unset($fragment[array_shift($parsed)]);  //zde měním přímo $_SESSION, v $parsed je pole s jedním prvkem - indexem prvku ve fragmentu (fragment může býte cele $:SESSION a prvek múže být fragment - pak maže celý fragment)
    }

    /**
     * Vrací kopii pole $_SESSION, pole obsahuje data session ve víceúrovňové (stromové) struktuře.
     * @return array
     */
    public function getArrayReference() {
        return $_SESSION;
    }

    /**
     * Vrací kopii prku pole $_SESSION odpovídající zadanému fragmentu, pole obsahuje data session ve víceúrovňové (stromové) struktuře.
     * @param string $name
     * @return array
     */
    public function getFragmentArrayReference($name) {
        $parsed = $this->parse($name.'.');
        $fragment = & $this->prepareFragment($parsed);
        return $fragment;
    }

##########  PRIVATE #########################

    private function parse($name) {
        return explode(self::FRAGMENT_SEPARATOR, $name);
    }

    /**
     * Metoda na základě zadaného rozkladu jména vrací pole získané jako předposlední úroveň pole $_SESSION, která odpovídá předposlední části jména (fragment)
     * a vrací index tohoto pole v parametru $parsed.
     * Fragment vrací jako návratovou hodnotu a vrací ho jako referenci na příslušnou část pole $_SESSION. To znamená, že změna hodnot prvků tohoto fragmentu
     * ve volajícím kódu pak mění přímo pole $_SESSION.
     * Index prvku ve fragmentu vrací v parametru $parsed, který po skončení metody obsahuje pole s jediným prvkem - indexem fragmentu. Proto je parametr $parsed
     * předáván referencí.
     *
     * @param array $parsed Jméno hodnoty rozložené metodu parse() na pole. Parameter $parse se v metodě také mění - je předáván referencí a představuje tak i druhou návratovou hodnotu.
     *
     * Příklad:
     * Pro jména "nazdar.hodiny" a "nazdar.budik" je pole $_SESSION dvourozměrné, má položku $_SESSION['nazdar'] což je pole,
     * které má dva prvky: ['hodiny'] a ['budik']. Metoda vrací pro jméno "nazdar.hodiny" referenci na prvek pole $_SESSION['nazdar'], což pole
     * s položkami ['hodiny'] a ['budik']. V parametru $parsed je pak pole s jediným prvkem obsahujícím string 'hodiny'.
     *
     * @return reference na session fragment, reference na pole získané jako předposlední úroveň pole $_SESIION, která odpovídá předposlední části jména.
     */
    private function & prepareFragment(& $parsed) {
        $session =& $_SESSION;  //reference - $session je lokální - na konci metody se reference zahodí

        while (count($parsed) > 1) {
            $next = array_shift($parsed);  // mění $parsed
            if ( ! isset($session[$next]) || ! is_array($session[$next])) {
                $session[$next] = [];      // prázné pole = neexistující hodnota úrovně session
            }
            $session =& $session[$next];  // $result = další úroveň pole session jako reference
        }
        return $session;
    }

    /**
     * Pokud je this->lock_to_user_agent nebo $this->lock_to_ip TRUE, vrací hash, jinak NULL
     * @return bool|NULL
     */
    private function getFingerprintHash() {
        if ($this->lockToUserAgent) {
            if ($this->lockToIp) {
                $hash = hash('sha1', $_SERVER['HTTP_USER_AGENT'] . (ip2long($_SERVER['REMOTE_ADDR']) & ip2long(self::IP_MASK)));
            } else {
                $hash = hash('sha1', $_SERVER['HTTP_USER_AGENT']);
            }
        } else {
            if ($this->lockToIp) {
                $hash = hash('sha1', (ip2long($_SERVER['REMOTE_ADDR']) & ip2long(self::IP_MASK)));
            } else {
                $hash = NULL;
            }
        }
        return $hash;        // http://php.net/manual/en/function.hash.php - sha1 není nejbezpečnější, ale patří k nejrychlejším a tady to stačí

    }
}
