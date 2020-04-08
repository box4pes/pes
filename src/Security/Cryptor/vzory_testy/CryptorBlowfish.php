<?php

namespace Pes\Security\Cryptor;
/*
 * Copyright (C) http://php.net/manual/en/class.sessionhandler.php
 * Kopie příkladu z http://php.net/manual/en/class.sessionhandler.php
 */

/**
 * CryptorBlowfish šifruje s užitím modulu mcrypt. Ten je dlouhodobě nevyvíjený a v PHP7.1 označen jako deprecated.
 * Tento cryptor obsahuje kód z původní projektorové AuthCookie. 
 *
 * @author pes2704
 */
class CryptorBlowfish implements CryptorInterface {
    
    //Parametry šifrování
    private $td;
    
    public function __construct($key) {
        $this->td = mcrypt_module_open (MCRYPT_BLOWFISH, '', MCRYPT_MODE_CFB, '');        
    }

    /**
     * decrypt AES 256
     *
     * @param data $edata
     * @return decrypted data
     */
    function decrypt($crypttext) {
        $ivsize = mcrypt_enc_get_iv_size($this->td);
        $iv = substr($crypttext, 0, $ivsize);
        $crypttext = substr($crypttext, $ivsize);
        mcrypt_generic_init ($this->td, self::KEY, $iv);
        $plaintext = mdecrypt_generic ($this->td, $crypttext);
        mcrypt_generic_deinit ($this->td);
        return $plaintext;
      }

    /**
     * crypt AES 256
     *
     * @param data $data
     * @return base64 encrypted data
     */
    public function encrypt($data) {
        // Set a random salt
        $salt = openssl_random_pseudo_bytes(16);

        $salted = '';
        $dx = '';
        // Salt the key(32) and iv(16) = 48
        while (strlen($salted) < 48) {
          $dx = hash('sha256', $dx.$this->key.$salt, true);
          $salted .= $dx;
        }

        $key = substr($salted, 0, 32);
        $iv  = substr($salted, 32,16);

        $encrypted_data = openssl_encrypt($data, 'AES-256-CBC', $key, true, $iv);
        return base64_encode($salt . $encrypted_data);
    }
}
