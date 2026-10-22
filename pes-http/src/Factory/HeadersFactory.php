<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Factory;

use Pes\Http\Environment;
use Pes\Http\Headers;

/**
 * Description of HeadersFactory
 *
 * @author pes2704
 */
class HeadersFactory implements EnvironmentAcceptInterface {

    /**
     * Speciální HTTP hlavičky. Jedná se o ty proměnné získané z pole $_SERVER, které obsahují hodnoty hlaviček a jejich klíče nemají prefix "HTTP_".
     * Proměnné s klíčem s prefixem "HTTP_" jsou zpracovány jako HTTP hlavičky vždy. Speciální hlavičky jsou automaticky načítány
     * z běhového prostředí PHP např. metodou createFromEnvironment().
     *
     * @var array
     */
    protected static $special = [
        'CONTENT_TYPE' => 1,
        'CONTENT_LENGTH' => 1,
        'PHP_AUTH_USER' => 1,
        'PHP_AUTH_PW' => 1,
        'PHP_AUTH_DIGEST' => 1,
        'AUTH_TYPE' => 1,
    ];

    /**
     * Vytvoří novou kolekci hlaviček - objekt Headers. Použije objekt Environment, ze kterého získává globální proměnné PHP.
     * <p>Hlavičky v objektu Headers tvoří z key/value párů nalezených v hlavičkách přijaatého HTTP requstu:
     * <ul>
     * <li>s klíči uvedenými v poli self::$special </li>
     * <li>s klíči začínajícími řetězcem "HTTP_" s výjimkou proměnné s klíčem HTTP_CONTENT_LENGTH, která neobsahuje hodnoty hlavičky.
     *  </ul></p>
     *
     * @param Environment $environment Objekt Environment
     *
     * @return Pes\Http\Headers
     */
    public function createFromEnvironment(Environment $environment)
    {
        $data = [];
//        $environment = self::determineAuthorization($environment);
        foreach ($environment as $key => $value) {
            $key = strtoupper($key);
            if (isset(static::$special[$key]) OR (strpos($key, 'HTTP_') === 0 AND $key !== 'HTTP_CONTENT_LENGTH')) {
                $data[$key] =  $value;
            }
        }

        return new Headers($data);
    }

}
