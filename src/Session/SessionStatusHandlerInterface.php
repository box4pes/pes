<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Session;

use Psr\Log\LoggerInterface;

/**
 *
 * @author pes2704
 */
interface SessionStatusHandlerInterface {
    /**
     * Nastaví loger.
     * @param LoggerInterface $logger
     * @return \Pes\Session\SessionStatusHandlerInterface
     */
    public function setLogger(LoggerInterface $logger): SessionStatusHandlerInterface;
    
    /**
     * Nastaví parametry session na základě parametrů konstrukroru a nastartuje session.
     * Tuto metodu e třeba volat jen v případě, když parametr konstruktoru $manualStartStop nastaven na true, jinak dojde v konstruktoru 
     * k automatickému staru session (dafault hodnota). V případě automatického startu session vyvolá následné volání metody 
     * session_start() výjimku LogicException.
     */
    public function sessionStart();
    
    /**
     * Nastaví parametry session na základě parametrů konstrukroru, přečte uložená session data a znovu nastartuje session.
     */
    public function sessionReset();
    
    /**
     * Zapíše data session do úložiště a ukončí fungování session handleru.
     */
    public function sessionFinish();

    /**
     * Vrací hodnotu se zadaným jménem.
     *
     * @param string $name
     * @return string || NULL
     */
    public function get($name);

    /**
     * Nastaví hodnotu zadanému jménu.
     *
     * @param string $name
     * @param string $value
     */
    public function set($name, $value);

    /**
     * Smaže fragment nebo jednu hodnotu se zadaným jménem.
     *
     * @param string $name Jméno hodnoty (např. user.status) nebo jméno fragmentu (npř. user)
     */
    public function delete($name);

    /**
     * Vrací referenci na pole $_SESSION, pole obsahuje data session ve víceúrovňové (stromové) struktuře.
     * @return array
     */
    public function getArrayReference();

    /**
     * Vrací referenci na prvek pole $_SESSION odpovídající zadanému fragmentu, pole obsahuje data session ve víceúrovňové (stromové) struktuře.
     * @param string $fragmentName
     * @return array
     */
    public function getFragmentArrayReference($fragmentName);

    /**
     * Smaže data ukládaná session handlerem a také cookie používané pro předávání identifikátoru session.
     * @return boolean
     */
    public function forget();

    /**
     * Regeneruje identifikátor session
     *
     * @return bool
     */
    public function refresh();

    /**
     * Vrací čas vytvoření (prvního startu) session. Čas je unix timestamp.
     *
     * @return int
     */
    public function getCreationTime();

    /**
     * Vrací čas předchozího startu session. Čas je unix timestamp.
     *
     * @return int
     */
    public function getLastStartTime();

    /**
     * Vrací čas startu session. Čas je unix timestamp.
     *
     * @return int
     */
    public function getCurrentStartTime();

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
    public function hasFingerprint();

    /**
     * Vrací TRUE, pokud session byla nastartována poprvé v průběhu trvání skutečného sezení klienta.
     *
     * @return bool
     */
    public function isNew();

}
