<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use PHPUnit\Framework\TestCase;

use Pes\Security\Cryptor\CryptorOpenSSLBase;
use Pes\Security\Exception\DecryptionFailedException;
/**
 * Description of CryptorReversingTestTTT
 *
 * @author pes2704
 */
class CryptorOpenSSLBaseTest extends TestCase {

    const KEY = 'ěščřžýáíé';

    public function testEncryptAndDecrypt() {
        $cryptor1 = new CryptorOpenSSLBase(self::KEY);
        $cryptor2 = new CryptorOpenSSLBase(self::KEY);
        $message = 'asdfghjklůkjhgfdsaSDFGHJKLŮKJHGFDSA';
        $enc = $cryptor1->encrypt($message);
        $c = $cryptor2->decrypt($enc);
        $this->assertTrue(hash_equals($message, $c));
    }

    public function testRepeatedEncrypt() {
        $cryptor1 = new CryptorOpenSSLBase(self::KEY);
        $cryptor2 = new CryptorOpenSSLBase(self::KEY);
        $message = 'asdfghjklůkjhgfdsaSDFGHJKLŮKJHGFDSA';
        $c11 = $cryptor1->encrypt($message);
        $c12 = $cryptor1->encrypt($message);
        $c2 = $cryptor1->encrypt($message);
        $this->assertFalse(hash_equals($c11, $c12));
        $this->assertFalse(hash_equals($c11, $c2));
        $this->assertFalse(hash_equals($c12, $c2));
    }

    public function testChangededMessage() {
        $cryptor1 = new CryptorOpenSSLBase(self::KEY);
        $cryptor2 = new CryptorOpenSSLBase(self::KEY);
        $message = 'asdfghjklůkjhgfdsaSDFGHJKLŮKJHGFDSA';
        $changed = $this->corruptMessage($message);  // poznámka - změna v 1 bytu původního textu zcela změní šifrovaný text!
        $levMessage = levenshtein($message, $changed);

        $encryptedMessage = $cryptor1->encrypt($message);
        $encryptedChanged = $cryptor1->encrypt($changed);
        $levEncrypted = levenshtein($encryptedMessage, $encryptedChanged);

        $decryptedMessage = $cryptor2->decrypt($encryptedMessage);
        $decryptedChanged = $cryptor2->decrypt($encryptedChanged);
        $levDecrypted = levenshtein($decryptedMessage, $decryptedChanged);
        $this->assertEquals($message, $decryptedMessage);
        $this->assertEquals($changed, $decryptedChanged);
        $this->assertEquals($levMessage, 1);
        $this->assertTrue($levEncrypted > 60); // obvykle 64, někdy 63,62
        $this->assertEquals($levDecrypted, 1);




    }

    public function testCorruptedMessage() {
        $cryptor1 = new CryptorOpenSSLBase(self::KEY);
        $cryptor2 = new CryptorOpenSSLBase(self::KEY);
        $message = 'asdfghjklůkjhgfdsaSDFGHJKLŮKJHGFDSA';
        $c = $cryptor1->encrypt($message);
        $corrupted = $this->corruptMessage($c);  // poznámka - změna v 1 bytu šifrovaného textu změní obykle jen 1 byte v dešifrovaném textu!
        $this->expectException(DecryptionFailedException::class);
        $decryptCorrupted = $cryptor2->decrypt($corrupted);

//            $lev = levenshtein($decryptCorrupted, $message);  // nebo taky ne
//            $this->assertNotEquals($decryptCorrupted, $message);
//            $this->assertTrue(($lev % 16) == 1 );  // zjištěno experimentálně

    }

    /**
     * Metoda zamění 1byte v řetězci. Lze použít pro detekci chybně zakódovaného řetězce. Je určena pro 8 bitové kódování.
     * 
     * 
     * @param array $message
     * @return type
     */
    private function corruptMessage($message) {
//  POZOR! Nelze použít pro detekci chybně zakódovaného stringu v kódování Base64 ani po změně mb_strlen($message, '8bit') na mb_strlen($message, 'BASE64')
//  - v kódování Base64 je mnoho bytů doplněno a pokud metoda corruptMessage() zamění některý doplněná byte, 
//  dekódování proběhně v pořádku.
//  - navíc v PHP 8.2 je použití metod jako je mb_strlen pro BASE deprecated 
        if ($this->isBase64Encoded($message)) {
            throw new UnexpectedValueException("Metodu nelze použít pro řetězce kódované Base64");
        }
         // Změní 1 bit
         $i = rand(0, mb_strlen($message, '8bit') - 1);
         $message[$i] = $message[$i] ^ chr(1);
         return $message;
    }
    
    /**
     * Nedokonalý test na Base64 - https://stackoverflow.com/questions/4278106/how-to-check-if-a-string-is-base64-valid-in-php
     * 
     * Check if the given string is valid base 64 encoded.
     *
     * @param string $string The string to check.
     * @return bool Return `true` if valid, `false` for otherwise.
     */
    private function isBase64Encoded($string): bool
    {
        if (!is_string($string)) {
            // if check value is not string.
            // base64_decode require this argument to be string, if not then just return `false`.
            // don't use type hint because `false` value will be converted to empty string.
            return false;
        }

        $decoded = base64_decode($string, true);
        if (false === $decoded) {
            return false;
        }

        if (json_encode([$decoded]) === false) {
            return false;
        }

        return true;
    }// isBase64Encoded
    
}
