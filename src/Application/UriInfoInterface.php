<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Application;


/**
 *
 * @author pes2704
 */
interface UriInfoInterface {

    /**
     * Část uri odpovídající subdoméně - relativní uri kořenového skriptu aplikace vzhledem ke kořeni dokumentů (document root). Pokud request path obsahuje i jméno souboru, toto uri obsahuje i toto jméno souboru.
     * Je to absolutní cesta (začíná '/').
     *
     * @return string
     */
    public function getSubdomainUri(): string;

    /**
     * Část url path odpovídající subdoméně - relativní uri kořenového skriptu aplikace vzhledem ke keřeni dokumentů (document root) - odpovídá poadresáři adresáře document root.
     * Je to absolutní cesta (začíná '/').
     *
     * @return string
     */
    public function getSubdomainPath(): string;
    /**
     * Část url path odpovídající REST resource identifikátoru - část url, která již nemá předobraz v adresářové struktuře, následuje za subdomain path.
     * Je to absolutní cesta (začíná '/').
     *
     * @return string
     */
    public function getRestUri(): string;

    /**
     * Úplné URI zdroje (resource uri). Relativní adresa zdroje. Obsahuje spojenou relativní adresu subdomény (subdomain path) a část url path odpovídající REST resource identifikátoru.
     * Je to absolutní cesta (začíná '/').
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * Absolutní cesta ke kořenovému adresáři skriptu. Začíná i končí '/'.
     *
     * @return string
     */
    public function getRootAbsolutePath(): string;

    /**
     * RelaTivní cesta k aktuálnímu pracovnímu adresáři.
     *
     * @return string
     */
    public function getWorkingPath(): string;

    /**
     * Část uri odpovídající subdoméně - relativní uri kořenového skriptu aplikace vzhledem ke kořeni dokumentů (document root). Pokud request path obsahuje i jméno souboru, toto uri obsahuje i toto jméno souboru.
     * Je to absolutní cesta (začíná '/').     *
     * @param string $subdomainPath
     * @return UrlInfoInterface
     */
    public function setSubdomainUri(string $subdomainPath): UriInfoInterface;

    /**
     * Část url path odpovídající subdoméně - relativní uri kořenového skriptu aplikace vzhledem ke keřeni dokumentů (document root) - odpovídá poadresáři adresáře document root.
     * Je to absolutní cesta (začíná '/').
     *
     * @param string $subdomainPath
     * @return UriInfoInterface
     */
    public function setSubdomainPath(string $subdomainPath): UriInfoInterface;



    /**
     * Část url path odpovídající REST resource identifikátoru - část url, která již nemá předobraz v adresářové struktuře, následuje za subdomain path.
     * Je to absolutní cesta (začíná '/').
     *
     * @param string $restUri
     * @return UrlInfoInterface
     */
    public function setRestUri(string $restUri): UriInfoInterface;

    /**
     * Absolutní cesta ke kořenovému adresáři skriptu. Začíná i končí '/'.
     *
     */
    public function setRootAbsolutePath(string $rootRelativePath): UriInfoInterface;

    /**
     * RelaTivní cesta k aktuálnímu pracovnímu adresáři.
     *
     */
    public function setWorkingPath(string $workingPath): UriInfoInterface;
}
