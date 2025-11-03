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

use Exception;

use Pes\Security\Exception\EncryptionFailedException;
use Pes\Security\Exception\DecryptionFailedException;
use Pes\Security\Exception\InvalidParameterValueException;

/**
 * Upraveno z:
 * https://github.com/ioncube/php-openssl-cryptor/blob/master/cryptor.php
 *
 * Třída pro šifrování a dešifrování. Používá openssl encrypt/decrypt funkce.
 *
 * https://en.wikipedia.org/wiki/Authenticated_encryption
 *
 * https://en.wikipedia.org/wiki/Block_cipher_mode_of_operation
 * https://en.wikipedia.org/wiki/CCM_mode
 * https://crypto.stackexchange.com/questions/25249/where-is-the-authentication-tag-stored-in-file-encrypted-using-aes-gcm
 *
 * Pro AES -128, -192, -256
 * Zdá se, že na délce moc nezáleží, 256 asi není bezpečnější než kratší díky "špatnému uspořádání bitů" ve variantě 256 (??)
 * Zřejmě nejvhodnější metody jsou AES-256-GCM (až od PHP 7.1), AES-256-CTR nebo AES-256-CBC, utčitě nebezpečná je varianta ECB.
 * Musí být náhodně generován iv pro každou zprávu!
 * Musí být ověřena integrita zprávy - mohlo dojít k manipulaci man in the middle - zprávu je třaba tzv. autentizovat. Přidává se
 * ke zprávě Message Authentication Code (MAC) - řetězec, který závisí na zprávě a na klíči. MAC je třeba přidat takto:
 * Encrypt then MAC: encrypt the plaintext, compute the MAC of the ciphertext, and append the MAC of the ciphertext to the ciphertext.
 * Klíč - možná: you want binary strings, not human-readabale strings, for your keys - např. hex2bin('000102030405060708090a0b0c0d0e0f1011121314151617181‌​91a1b1c1d1e1f')
 *
 * https://stackoverflow.com/questions/9262109/simplest-two-way-encryption-using-php (Scott Arciszewski)
 * https://tonyarcieri.com/all-the-crypto-code-youve-ever-written-is-probably-broken
 * https://crypto.stackexchange.com/questions/6523/what-is-the-difference-between-mac-and-hmac
 * knihovna: https://github.com/defuse/php-encryption
 * Available under the MIT License
 *
 * The MIT License (MIT)
 * Copyright (c) 2016 ionCube Ltd.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of
 * the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT
 * OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class CryptorOpenSSLBase implements CryptorInterface {
    private $key;

    private $cipherMethod;
    private $hashMethod;
    private $options;
    private $ivLength;

    private $iv;
    private $macTag;

    /**
     * Konstruktor bázové třídy - defaultně používá aes256 šifrování - mód AES256CBC, sha256 pro hash klíče, base64 kódování výstupního řetězce.
     *
     * @param type $key Šifrovací klíč
     * @param type $options Default hodnota 0. Parametr musí být bitovou disjunkcí příznaků OPENSSL_RAW_DATA a OPENSSL_ZERO_PADDING nebo OPENSSL_DONT_ZERO_PAD_KEY.
     *      Pokud je nastaveno OPENSSL_RAW_DATA, jsou zašifrovaná data vrácena tak, jak jsou. Pokud není zadána (default), jsou vrácena data v kódování Base64. 
     * @param type $cipherMethod Šifrovací metoda. Hodnota EnumCipherMethod.
     * @param type $hashMethod Metoda pro hashování klíče. Hodnota EnumKeyHashMethod
     * @throws InvalidParameterValueException Chybná hodnota options, Neexistující hodnota parametru v příslušném typu Enum (Neznámá metoda šifrování nebo hashování)
     */
    public function __construct($key, $options = 0, $cipherMethod = EnumCipherMethod::AES256CBC, $hashMethod = EnumKeyHashMethod::SHA256) {
        throw new Exception("Cryptor je v procesu úprav - nelze ho používat!");
        $this->key = $key;
        $this->cipherMethod = (new EnumCipherMethod())($cipherMethod);
        $this->hashMethod = (new EnumKeyHashMethod())($hashMethod);
        // options is a bitwise disjunction of the flags OPENSSL_RAW_DATA, and OPENSSL_ZERO_PADDING or OPENSSL_DONT_ZERO_PAD_KEY. 
        //OPENSSL_RAW_DATA = 1
        //OPENSSL_ZERO_PADDING = 2
        //OPENSSL_DONT_ZERO_PAD_KEY = 4
        if ($options<0 || $options>8) {
            throw new InvalidParameterValueException("Parameter options must be a bitwise disjunction of the flags OPENSSL_RAW_DATA, and OPENSSL_ZERO_PADDING or OPENSSL_DONT_ZERO_PAD_KEY. ");
        }
        $this->options = 3;//$options;
        if (!in_array($cipherMethod, openssl_get_cipher_methods(true))) {
            throw new InvalidParameterValueException("Cipher method {$this->cipherMethod} is not OpenSSL available cipher method.");
        }
        if (!in_array($hashMethod, openssl_get_md_methods(true))) {
            throw new InvalidParameterValueException("Hash method {$this->hashMethod} is not OpenSSL available digest (hash) method.");
        }

        $this->ivLength = openssl_cipher_iv_length($this->cipherMethod);
    }

    /**
     * Šifruje vstupní text.
     * @param  string $plainText  Vstupní text.
     * @return string      The encrypted string.
     */
    public function encrypt($plainText) {
        // Generování initializačního vektoru. Musí být náhodně generován iv pro každou zprávu!
        // $isStrongCrypto je návratová hodnota - TRUE -> použitý algoritmus je kryptograficky silný, vhodný pro GPG, hesla apod.
        // 7.4.0 	The function no longer returns false on failure, but throws an Exception instead. 
        try {
            $iv = openssl_random_pseudo_bytes($this->ivLength, $isStrongCrypto);
            if (!$isStrongCrypto) {
                throw new EncryptionFailedException("Encyption failed  - the encryption algorithm used is not cryptographically strong.");
            }            
        } catch (Exception $exc) {
            throw new EncryptionFailedException("Encyption failed  - the encryption algorithm used is not cryptographically strong. Message: {$exc->getMessage()}");
        }

        // Hash klíče
        $keyhash = openssl_digest($this->key, $this->hashMethod, true);
        // and encrypt
        $encrypted = openssl_encrypt($plainText, $this->cipherMethod, $keyhash, $this->options, $iv);
        if ($encrypted === false) {
            throw new EncryptionFailedException('Encryption failed. OpenSSL error: ' . openssl_error_string());
        }
        // jen zřetězení
        return $iv.$encrypted;
    }
    /**
     * Decrypt a string.
     * @param  string $cipherText  Text pro dešifrování.
     * @param  int $fmt Optional override for the input encoding. One of FORMAT_RAW, FORMAT_B64 or FORMAT_HEX.
     * @return string Dešifrovaný text.
     */
    public function decrypt($cipherText) {
        // and do an integrity check on the size.
        if (strlen($cipherText) < $this->ivLength) {
            throw new DecryptionFailedException('Decryption failed due to data length '.strlen($cipherText)." is less than initialization vector length {$this->ivLength}");
        }
        // Rozložení řetězce na initialisation vector a encrypted data - rozloží podle ivLength znovu vypočtené v konstruktoru podle $this->cipherMethod
        $iv = substr($cipherText, 0, $this->ivLength);
        $cleanCipherText = substr($cipherText, $this->ivLength);
        // Hash the key
        $keyhash = openssl_digest($this->key, $this->hashMethod, true);
        // and decrypt.
        // options can be one of OPENSSL_RAW_DATA, OPENSSL_ZERO_PADDING or OPENSSL_DONT_ZERO_PAD_KEY. 
        $decrypted = openssl_decrypt($cleanCipherText, $this->cipherMethod, $keyhash, $this->options, $iv);  // return string|false
        if ($decrypted === false) {
            throw new DecryptionFailedException('Decryption failed. OpenSSL error: ' . openssl_error_string());
        }
        return $decrypted;
    }
    
    private function linkInitialisationVector($iv, $encrypted) {
        
    }
    
    private function unlinkInitialisationVector($param) {
        
    }
}


