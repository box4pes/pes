<?php
/**
 * Emuluje enum typ EnumCipherMethod.
 * 
 * @author pes2704
 */
namespace Pes\Security\Cryptor;

use Pes\Type\Enum;

/**
 *  * Zřejmě nejvhodnější metody jsou AES-256-GCM (až od PHP 7.1), AES-256-CTR nebo AES-256-CBC, utčitě nebezpečná je varianta ECB.
 */
class EnumCipherMethod extends Enum {    
    const AES256GCM = 'aes-256-gcm';   //TODO: přidej tag - návratová hodnota encrypt, nutno uschovat, pak hodnota pro decrypt - viz https://www.php.net/manual/en/function.openssl-encrypt.php
    const AES256CTR = 'aes-256-ctr';
    const AES256CBC = 'aes-256-cbc';
}
