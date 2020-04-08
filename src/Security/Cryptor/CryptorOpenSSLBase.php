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

    private $ivLength;

    private $iv;
    private $macTag;

    /**
     * Konstruktor bázové třídy - defaultně používá aes256 šifrování - mód AES256CBC, sha256 pro hash klíče, base64 kódování výstupního řetězce.
     *
     * @param string $cipherMethod Šifrovací metoda.
     * @param string $hashMethod   Metoda pro hashování klíče.
     * @throws \UnexpectedValueException  Neexistující hodnota parametru v příslušném typu Enum
     * @throws \UnexpectedValueException  Neznámá metoda šifrování nebo hashování
     *
     */
    public function __construct($key, $cipherMethod = EnumCipherMethod::AES256CBC, $hashMethod = EnumKeyHashMethod::SHA256) {
        $this->key = $key;
        $this->cipherMethod = (new EnumCipherMethod())($cipherMethod);
        $this->hashMethod = (new EnumKeyHashMethod())($hashMethod);

        if (!in_array($cipherMethod, openssl_get_cipher_methods(true))) {
            throw new \UnexpectedValueException(get_called_class()." - openssl nepodporuje zadanou metodu šifrování {$this->cipherMethod}");
        }
        if (!in_array($hashMethod, openssl_get_md_methods(true))) {
            throw new \UnexpectedValueException(get_called_class()." - openssl nepodporuje zadanou metodu hashování {$this->hashMethod}");
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
        $iv = openssl_random_pseudo_bytes($this->ivLength, $isStrongCrypto);   // $isStrongCrypto je návratová hodnota - TRUE -> použitý algoritmus je kryptograficky silný, vhodný pro GPG, hesla apod.
        if (!$isStrongCrypto) {
            throw new \RuntimeException(__METHOD__." - Not a strong key");
        }
        // Hash klíče
        $keyhash = openssl_digest($this->key, $this->hashMethod, true);
        // and encrypt
        $opts =  OPENSSL_RAW_DATA;
        $encrypted = openssl_encrypt($plainText, $this->cipherMethod, $keyhash, $opts, $iv);
        if ($encrypted === false) {
            throw new \RuntimeException(__METHOD__.' - Encryption failed: ' . openssl_error_string());
        }
        // jen zřetězení
        return $iv.$encrypted;
    }
    /**
     * Decrypt a string.
     * @param  string $cipherText  String to decrypt.
     * @param  int $fmt Optional override for the input encoding. One of FORMAT_RAW, FORMAT_B64 or FORMAT_HEX.
     * @return string      The decrypted string.
     */
    public function decrypt($cipherText) {
        // and do an integrity check on the size.
        if (strlen($cipherText) < $this->ivLength) {
            throw new \Exception(__METHOD__.' - '.'data length '.strlen($cipherText)." is less than initialization vector length {$this->ivLength}");
        }
        // Rozložení řetězce na initialisation vector a encrypted data - rozloží podle ivLength znovu vypočtené v konstruktoru podle $this->cipherMethod
        $iv = substr($cipherText, 0, $this->ivLength);
        $cipherText = substr($cipherText, $this->ivLength);
        // Hash the key
        $keyhash = openssl_digest($this->key, $this->hashMethod, true);
        // and decrypt.
        $opts = OPENSSL_RAW_DATA;
        $decrypted = openssl_decrypt($cipherText, $this->cipherMethod, $keyhash, $opts, $iv);
        if ($decrypted === false) {
            throw new \Exception(__METHOD__.' - decryption failed: ' . openssl_error_string());
        }
        return $decrypted;
    }
}


