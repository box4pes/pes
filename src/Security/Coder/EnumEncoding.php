<?php
/**
 * Emuluje enum typ EnumEncoding.
 *
 * @author pes2704
 */
namespace Pes\Security\Coder;

use Pes\Type\Enum;

/**
 *
 */
class EnumEncoding extends Enum {
    const BASE64URL = 'Base64URL encoding - URL and Filename safe';
    const BASE64 = 'Base64 encoding';
    const HEX = 'Hex string, high nibble first';
}
