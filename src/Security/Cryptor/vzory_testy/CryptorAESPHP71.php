<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Security\Cryptor;

/**
 * Description of CryptorAESPHP71
 *
 * @author pes2704
 */
class CryptorAESPHP71 implements CryptorInterface {

    private $key;
    private $cipher;
    private $iv;
    private $tag;

    /**
     * $key Klíč - měl by být generován Kryptograficky bezpečně (dostatečně náhodně), například pomocí openssl_random_pseudo_bytes
     * @param string $key
     */
    public function __construct($key) {
        $this->key = $key;
        $this->cipher = "aes-128-gcm";
    }

    public function encrypt($plaintext) {
        if (in_array($this->cipher, openssl_get_cipher_methods())) {
            $ivlen = openssl_cipher_iv_length($this->cipher);
            $this->iv = openssl_random_pseudo_bytes($ivlen);
            $options = OPENSSL_RAW_DATA;
            //store $cipher, $iv, and $tag for decryption later

            return openssl_encrypt($plaintext, $this->cipher, $this->key, $options, $this->iv, $this->tag);
        } else {
            throw new \LogicException('Není podporována metoda '.$this->cipher.'.');
        }
    }

    public function decrypt($ciphertext) {
        $options = OPENSSL_RAW_DATA;
        return openssl_decrypt($ciphertext, $this->cipher, $this->key, $options, $this->iv, $this->tag);
    }
}
