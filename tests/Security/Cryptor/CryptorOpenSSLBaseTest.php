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
        $c = $cryptor2->decrypt($cryptor1->encrypt($message));
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
        $corrupted = $this->corruptMessage($c);  // poznámka - změna v 1 bytu šifriovaného textu změní obykle je 1 byte v dešifrovaném textu!
        try {
            $decryptCorrupted = $cryptor2->decrypt($corrupted);
        } catch (\Exception $e) {
            $excMessage = $e->getMessage();
        }
        if (isset($e)) {
            $this->assertTrue(strpos($excMessage, "decrypt - decryption failed:")>0);   // obvykle nastane výjimka
        } else {
            $lev = levenshtein($decryptCorrupted, $message);  // nebo taky ne
            $this->assertNotEquals($decryptCorrupted, $message);
            $this->assertTrue(($lev % 16) == 1 );  // zjištěno experimentálně
        }
    }

    private function corruptMessage($message) {
        // Změní 1 bit
         $i = rand(0, mb_strlen($message, '8bit') - 1);
         $message[$i] = $message[$i] ^ chr(1);
         return $message;
    }
}
