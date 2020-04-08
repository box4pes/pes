<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Security\Coder;

/**
 * Description of Coder
 *
 * @author pes2704
 */
class Coder implements CoderInterface {

    private $encoding;

    /**
     * Kostruktor.
     *
     * Parametrem je typ kódování, což musí být hodnota, která je obsažena ve výčtovém typu EnumEncoding.
     * Výčtový typ EnumEncoding definuje možné typy kódování používané touto třídou.
     *
     * Implementované typy kódování:
     * EnumEncoding::BASE64 = Kóduje metodou Base64 encoding
     * EnumEncoding::HEX = Kóduje vstupní string jako hexadecimální string, horní byte jako první
     * EnumEncoding::RAW = Bez kódování - kopíruje vstupní text beze změny
     *
     * @param string $encoding Zvolené kódování. Hodnota výčtového typu EnumEncoding.
     */
    public function __construct($encoding) {
        $this->encoding = (new EnumEncoding())($encoding);
    }

    /**
     * Zakóduje vstupní text.
     *
     * @param type $plainText  Vstupní text.
     * @return string Kódovaný text.
     * @throws \LogicException Nerozpoznán typ kódování. Zadaný typ kódování není implementován a obsažen v typu EnumEncoding.
     */
    public function encode($plainText)
    {
        switch ($this->encoding) {
            case (EnumEncoding::BASE64URL):
                $tr = strtr(base64_encode($plainText), '+/', '-_');
                $res = rtrim(strtr(base64_encode($plainText), '+/', '-_'), '=');
                break;
            case (EnumEncoding::BASE64):
                $res = base64_encode($plainText);
                break;
            case (EnumEncoding::HEX):
                $res = unpack('H*', $plainText)[1];
                break;
            case (EnumEncoding::RAW):
                $res = $plainText;
                break;
            default:
                throw new \LogicException('Nerozpoznán typ kódování. Zadaný typ kódování není implementován a obsažen v typu EnumEncoding.');
                break;
        }
        return $res;
    }

    /**
     * Rozkóduje zakódovaný text.
     *
     * @param string $encodedText Kódovaný text.
     * @return string Dekódovaný text.
     * @throws \LogicException Nerozpoznán typ kódování. Zadaný typ kódování není implementován a obsažen v typu EnumEncoding.
     */
    public function decode($encodedText)
    {
        switch ($this->encoding) {
            case (EnumEncoding::BASE64URL):
                $tr = strtr($encodedText, '-_', '+/');
                $res = base64_decode(strtr($encodedText, '-_', '+/'));   //http://php.net/manual/en/function.base64-encode.php#121767
                break;
            case (EnumEncoding::BASE64):
                $res = base64_decode($encodedText);
                break;
            case (EnumEncoding::HEX):
                $res = pack('H*', $encodedText);
                break;
            case (EnumEncoding::RAW):
                $res = $encodedText;
                break;
            default:
                throw new \LogicException('Nerozpoznán typ kódování. Zadaný typ kódování není implementován a obsažen v typu EnumEncoding.');
                break;
        }
        return $res;
    }

}
