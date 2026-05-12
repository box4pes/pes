<?php
/**
 * Emuluje enum typ EnumEncoding.
 *
 * @author pes2704
 */
namespace Pes\Core\Security\Coder;

use Pes\Core\Type\Enum;

/**
 *
 */
class EnumEncoding extends Enum {
    const BASE64URL = 'Base64URL encoding - URL and Filename safe';
    const BASE64 = 'Base64 encoding';
    const HEX = 'Hex string, high nibble first';
}
