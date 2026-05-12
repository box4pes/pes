<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Factory;

/**
 * Description of CookiesArrayFactory
 *
 * @author pes2704
 */
class CookiesArrayFactory {

    /**
     * Metoda přijímá hlavičku 'Cookie' ve tvaru pole. Je určena pro použití ve fázi, kdy ještě není vytvořen objekt Request.
     * Typicky je volána v RequestFactory, kde jsou takto vytvořené cookies instančním parametrem objektu Request.
     * @param array|string $header Pole párů klíč/hodnota hlavičky. Případně řetězec hlavičky.
     * @return array
     * @throws \InvalidArgumentException Nelze záskat cookies z hlavičky Cookie
     */
    public function extractFromCookieHeader($header) {
        if (is_array($header)) {
            $header = isset($header[0]) ? $header[0] : '';
        }

        if ($header==='') {
            return [];
        }

        if (!is_string($header)) {
            throw new \InvalidArgumentException('Nelze záskat cookies z hlavičky Cookie. Hodnota Cookie hlavičky není string.');
        }

        $header = rtrim($header, "\r\n");
        $nameValuePairs = preg_split('@\s*[;,]\s*@', $header);
        $requestCookies = [];
        foreach ($nameValuePairs as $nameValuePair) {
            list($name, $value) = explode('=', $nameValuePair, 2);

            if (isset($name) and isset($value)) {
                if (!isset($requestCookies[$name])) {
                    $requestCookies[$name] = $value;
                }
                // K výskytu více cookies se stejným jménem může dojít, pokud cookie byly odeslány se stejným jméne, ale s různým path.
                // V prohlížeči mohou být duplicitní cookie také, protože byla posláný s různou doménou. takové by se podle mě neměly objevit
                // v jednou requestu.  RFC 6265 říká, že prohlížeče by měly upřednostnit cookie s delší path (viz https://stackoverflow.com/questions/4056306/how-to-handle-multiple-cookies-with-the-same-name/24214538#24214538)
                // což nové prohlížeče dělají, ale současně také platí, že pošlou všechny cookie. Např PHP7 do global pole $_COOKIE dává jen první cookie s daným jménem, další zahodí.
                // Dělám to tedy také tak.
//                else {
//                    throw new \InvalidArgumentException("Nelze záskat cookies z hlavičky Cookie. Duplikátní jméno cookie: $name.");
//                }
            } else {
                throw new \InvalidArgumentException("Nelze záskat cookies z hlavičky Cookie. Hodnota hlavičky Cookie má chybný formát: $nameValuePair.");
            }
        }

        return $requestCookies;
    }
}
