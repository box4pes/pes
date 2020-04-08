<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Security\Password;

/**
 * Description of Password
 *
 * @author pes2704
 */
class Password implements PasswordInterface {
    
    private $algorithm;
    private $cost;
    private $rehashSaver;
    private $fallbackHashVerifier;


    /**
     * Nastavuje metodu a cost používané pro výpočet hashe hesla. 
     * Používá vždy defaultní metodu používanou PHP pro password_hash() a cost = 12. Ke změně metody nebo cost může dojít 
     * při změně default metody v PHP nebo editací kódu konstruktoru. 
     * 
     * Prvním parametrem konstruktoru je Closure pro ukládání přepočtených hashů. Metoda verifyPassword() při úspěšném ověření hesla a hashe kontroluje, 
     * zda hash byl vytvořen s aktuálně nastavenou metodou a cost, pokud hash použitý při ovšřování hesla nebyl vytvořen s aktuálně nastavenou metodou a cost, metoda 
     * verifyPassword() vypočte nový hash s aktuálně nastavenou metodou a cost a pokud je zadána jako parametr konstruktoru Closure pro ukládání přepočtených hashů, 
     * pak metoda verifyPassword() automaticky aktualizuje uložený hash. Zadání Closure pro ukládání přepočtených hashů tak vede ke zcela automatickému přepočítávání hashů 
     * při změně algoritmu nebo cost a uložení nových hashů (například do databáze).
     * 
     * Pro ukládáná hashů je třeba počítat s maximální délkou 255 znaků. Současná default hodnota počínaje PHP 5.5 (platí i pro PHP7) je bcrypt a tento algoritmus vrací 70 znaků, 
     * ale hodnota se může kdykoli v budoucnu změnit, dle dokumnetace PHP by však neměla překročit 255 znaků. Oříznutí hashe při ukládání do databáze by znamenalo zničené heslo.
     * 
     * Druhým parametrem konstruktoru je Closure, která se použije jako fallback pro ověření shody hesla a hashe v metodě verifyPassword(), pokud vnitřní 
     * PHP funkce password_verify() password a hash neověří. 
     * Tento fallback tak může obsahovat ověření shody hesla a hashe vytvořeného některou ze zastaralých metod, nepodporovaných funkcí password_hash(). Příkladem mohou být 
     * hashe vytvořené dříve užívanými metodami MD5 nebo SHA-1. Pak je nutno předat Closure, která ověří shodu hesla a hashe pomocí příslušné zastaralé metody. 
     * Oveření pomocí tohoto fallbacku je náhradní metodou ověření a i v tomto úříúadě funguje mechanizmus ukládání přepočtených hashů. Pokud fallback ověří platnost hesla 
     * a je zadán první parametr konstruktoru - Closure pro ukládání přepočtených hashů, pak metoda verifyPassword() vygeneruje nový hash pomocí aktuální metody a cost 
     * a nový hash uloží pomocí Closure pro ukládání přepočtených hashů. Takto lze automaticky obnovit staré uložené hashe vytvořené některou ze zastaralých metod.
     * 
     * @param \Closure $rehashSaver Closure pro ukládání přepočtených hashů, musí přijímat jeden parametr a tím je nový hash pro uložení.
     * @param \Closure $fallbackHashVerifier Closure pro verifikaci hashe, musí přijímat dva parametry - prvním je heslo, druhým je hash.
     */
    public function __construct(\Closure $rehashSaver = NULL, \Closure $fallbackHashVerifier = NULL) {
        //The default algorithm to use for hashing. This may change in newer PHP releases when newer, stronger hashing algorithms are supported. 
        //Therefore you should be aware that the length of the resulting hash can change. 
        //Therefore, if you use PASSWORD_DEFAULT you should store the resulting hash in a way that can store more than 60 characters (255 is the recomended width).
        $this->algorithm = PASSWORD_DEFAULT;
        $this->cost = 12;
        $this->rehashSaver = $rehashSaver;
        $this->fallbackHashVerifier = $fallbackHashVerifier;
    }
    
    /**
     * Metoda vygeneruje hash pro zadané heslo. Použije metodu a cost nastavené v kódu konstruktoru.
     * 
     * @param string $userPassword
     */
    public function getPasswordHash($userPassword) {
        return password_hash($userPassword, $this->algorithm, ['cost' => $this->cost]);        
    }
    
    /**
     * Ověří zda heslo a hash si odpovídají, pokud ano, heslo je považováno za správné a metoda vrací TRUE. 
     * Pokud je v konstruktoru objektu zadána closure pro ukládání nových hashů, metoda automaticky aktualizuje uložený hash při pokusu o ověření hesla vždy, 
     * když je hash třeba přepočítat.
     * 
     * Pokud je heslo správné, metoda ověří zda není třeba hash přepočítat. To se může stát při interní změně algoritmu a jistě se to stane, 
     * pokud byla změněna hodnota algoritmu nebo cost v konstruktoru. Pak aktuálně používaný algoritmus nebo hodnota cost neodpovídají těm, se kterým byl vytvořen 
     * zadaný hash. Takový hash, jehož metoda nebo cost neodpovídají, je třeba přepočítat - rehash s použitím aktuální metody a cost.
     * Pokud metoda verifyPassword() zjistí, že hash je třeba přepočítat a v konstruktoru byl zadán parametr - Closure pro uložení přepočteného hashe, 
     * pak tato metoda přepočte hash a volá Closure pro uložení nového hashe. Pokud nebyla zadána Closure pro ukládání a hash je třeba přepočítat, metoda vytvoří
     * chybu E_USER_NOTICE.
     * 
     * @param string $passwordToVerify
     * @param string $hash
     */
    public function verifyPassword($passwordToVerify, $hash) {
        $success = FALSE;                
        // hash vracený password_hash() obsahuje informace o použitém algoritmu, salt a cost. Funkce password_verify() tak funguje pro všechny algoritmy, salt, cost.
        if (password_verify($passwordToVerify, $hash)) {
            // Login successful.
            if (password_needs_rehash($hash, $this->algorithm, ['cost' => $this->cost])) {
                $this->rehashAndSave($passwordToVerify);
            }
            $success = TRUE;
        } elseif ($this->fallbackHashVerifier) {  // 
            //fallback for old methods hashed hashes 
            $verifiedAsOldHash = ($this->fallbackHashVerifier)($passwordToVerify, $hash);
            if ($verifiedAsOldHash) {
                $this->rehashAndSave($passwordToVerify);
                $success = TRUE;
            }
        }
        return $success;
    }
    
    private function rehashAndSave($passwordToVerify) {
        // Recalculate a new password_hash() and overwrite the one we stored previously
        if ($this->rehashSaver) {
            ($this->rehashSaver)($this->getPasswordHash($passwordToVerify));
        } else {
            user_error('Nebyla zadána closure pro uložení nového hashe hesla. Došlo k ověření hesla, '
                    . 'jehož hash by měl být přepočítán a nový hash uložen. Nebyla zadána closure pro ukládání, k uložení nedošlo.', E_USER_NOTICE);
        }        
    }
    
}
