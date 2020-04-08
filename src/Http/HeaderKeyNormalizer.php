<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http;

use Pes\Collection\Normalizer\KeyNormalizerInterface;

/**
 * Description of HeaderKeyNormalizer
 *
 * @author pes2704
 */
class HeaderKeyNormalizer implements KeyNormalizerInterface {

    private $originalKeys = [];

    /**
     * Normalizuje jména hlaviček. Normalizované jméno obsahuje jen malá písmena a pomlčky.
     * Změní písmena na malá, zamění pomlčky za podtržítka a speciální hlavičky s prefixem "http-" přejmenuje tak, že odstraní prefix.
     *
     * @param  string $key Jméno hlavičky
     *
     * @return string Normalizované jméno hlavičky
     */
    public function normalizeKey($key)
    {
        $keyLowercased = strtr(strtolower($key), '_', '-');
        $keyNormalized = (strpos($keyLowercased, 'http-') === 0) ? substr($keyLowercased, 5) : $keyLowercased;
        $this->originalKeys[$keyNormalized] = $key;

        return $keyNormalized;
    }

    public function getOriginalKey($normalizedKey) {
        return $this->originalKeys[$normalizedKey];
    }
}
