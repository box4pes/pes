<?php

/* 
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

//Inspirace! Dodělat!

// AES - používá bloky 128 bitů (stav při kryptování je matice 4x4 byty) (původní návrh, tedy Rijndael používal násobky 32bitů, 127 bylo minimum)
// Klíč pro AES je stanoven jen na 128 nebo 192 nebo 256 bitů
// 
$algo  = 'aes-256-gcm';   
$algo  = 'aes-256-ccm';   
//$algo  = 'aes-256-ctr';

// inicializační vektor
// je binární, tedy v případě PHP, kdy proměnná je typu string, to jsou "raw" 8mi bitové byty
// pokud je iv přenášen zakódovaný Base64, musí se před decryptem rozkódovat base64_decode()
// iv musí být přesně tak dlouhý jako šifrované bloky (používané vnitřně), AES vždy šifruje 128bitové bloky (16 bytů) takže pro AES vždy musí být iv dlouhé 16 bytů
// pokud se opakované šifruje se stejným klíčem - je nutné vždy použít jiný iv - 
$ivLength = openssl_cipher_iv_length($algo);   // integer
$iv = openssl_random_pseudo_bytes($ivLength, $isStrongCrypto);   // $isStrongCrypto je návratová hodnota - TRUE -> použitý algoritmus je kryptograficky silný, vhodný pro GPG, hesla apod.
if (!$isStrongCrypto) {
    throw new \RuntimeException(__METHOD__." - Not a strong key");
}

// key (alias password)
// je binární, tedy v případě PHP, kdy proměnná je typu string, to jsou "raw" 8mi bitové byty
// AES definuje velikost klíče jako 128 nebo 192 nebo 256bitů (nejdelší úspěný útok je na šifru s klíčem 64bitů, na 72bitech se pracuje, časová náročnost
// roste s mocninou 2 (2x pro přidání 1 bitu), 128 bitů je považováno obecně za váce než dostatečné, existují předpoklady, že 256 ani není bezpečnější.
// pro 128 bitů klíč použije AES 10 průchodů při šifrování, pro 256 14 průchodů - možná to znamená čas 14/10, možná 28/10??) 
$key   = random_bytes(16); // 128 bit

$message = 'This is the secret message!';
$messageLength = strlen($message);

// autentizační tag
// - jen pro AEAD metody, což jsou GCM a CCD
// - GCM encryption - čtyři vstupy: message, key, iv, aad -> dva výstupy: šifrovaná message, authentication tag
// - GCM decryption - pět vstupů: šifrovaná message, key, iv, aad, tag -> jeden výstup: dešifrovaná zpráva nebo FALSE
// proměnná je předána funkci encrypt referencí - návratová hodnota je tedy předávána v $tag, obsah proměnné před voláním encrypt je přepsán
$tag = '';    // GCM vrací 16 bytů

// Additional authentication data
// - jen pro AEAD metody, což jsou GCM a CCD - akceptují athentication tag, u ostatních metod vznikne Warning: openssl_encrypt(): The authenticated tag cannot be provided for cipher that doesn not support AEAD
// - správně by mělo být Additional authenticated data - přidaná data, která jsou autentifikována, ale nejsou šifrována - třeba adresy, porty, veze protokolu apod., které jsou tedy nezašifrované a jsou tak čitelné pro např. routery po cestě, které pochopitelně neumí zprávu dešifrovat
// - mohou být dlouhá 0 až 2^64 bitů 
$aad   = 'From: foo@domain.com, To: bar@domain.com';

//$options = 0 -> encrypt zašifrovaná data zakóduje Base64, OPENSSL_RAW_DATA -> bez kódování
//$options = 0 -> decrypt předpokládá, že vstupní data jsou zakódovaná Base64, OPENSSL_RAW_DATA -> bez kódování
$options = 0; 
$options = OPENSSL_RAW_DATA; 

$ciphertext = openssl_encrypt(
    $message,
    $algo,
    $key,
    $options,
    $iv,
    $tag,
    $aad
);

$ciphertextLength = strlen($ciphertext);
$tagLength = strlen($tag);

$store['key'] = $keyX;
$store['iv'] = $iv;
$store['tag'] = $tag;
$store['aad'] = $aad;
$store['options'] = $options;
 
$decrypt = openssl_decrypt(
    $ciphertext,
    $algo,
    $store['key'],
    $store['options'],
    $store['iv'],
    $store['tag'],
    $store['aad']
);

echo "<pre>";
if (false === $decrypt) {
//    throw new Exception(sprintf(
//        "OpenSSL error: %s", openssl_error_string()
//    ));
    printf(
        "OpenSSL error: %s".PHP_EOL, openssl_error_string()
    );    
}
printf ("Decryption %s".PHP_EOL, $message === $decrypt ? 'Ok' : 'Failed');
while ($msg = openssl_error_string())
    echo $msg . "<br />\n";

//corrupted message
// Change 1 bit in crypted data
$ciphertextCorrupted = $ciphertext;
$i = rand(0, mb_strlen($ciphertextCorrupted, '8bit') - 1);
$ciphertextCorrupted[$i] = $ciphertextCorrupted[$i] ^ chr(1);

$decryptC = openssl_decrypt(
    $ciphertextCorrupted,
    $algo,
    $store['key'],
    $store['options'],
    $store['iv'],
    $store['tag'],
    $store['aad']
);

if (false === $decryptC) {
//    throw new Exception(sprintf(
//        "OpenSSL error on corrupted message: %s", openssl_error_string()
//    ));
    printf(
        "OpenSSL error on corrupted message: %s".PHP_EOL, openssl_error_string()
    );    
}
printf ("Decryption of corrupted message %s\n", $message === $decryptC ? 'Ok' : 'Failed');
while ($msg = openssl_error_string())
    echo $msg .PHP_EOL;
echo "</pre>";


$breakpint = 'je tady';
