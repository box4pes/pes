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
     * Část url path odpovídající subdoméně - relativní uri kořenového skriptu aplikace vzhledem ke keřeni dokumentů (document root) - odpovídá poadresáři adresáře document root. Je to absolutní cesta (začíná '/').
     *
     * @return string
     */
    public function getSubdomainUri();

    /**
     * Část url path odpovídající subdoméně - relativní uri kořenového skriptu aplikace vzhledem ke keřeni dokumentů (document root) - odpovídá poadresáři adresáře document root. Je to absolutní cesta (začíná '/').
     *
     * @param string $subdomainPath
     * @return UrlInfoInterface
     */
    public function setSubdomainUri($subdomainPath): UriInfoInterface;

    /**
     * Část url path odpovídající REST resource identifikátoru - část url, která již nemá předobraz v adresářové struktuře, následuje za subdomain path. Je to absolutní cesta (začíná '/').
     *
     * @return string
     */
    public function getRestUri();

    /**
     * Úplné URI zdroje (resource uri). Relativní adresa zdroje. Obsahuje spojenou relativní adresu subdomény (subdomain path) a část url path odpovídající REST resource identifikátoru. Je to absolutní cesta (začíná '/').
     *
     * @return string
     */
    public function getUri();

    /**
     * Část url path odpovídající REST resource identifikátoru - část url, která již nemá předobraz v adresářové struktuře, následuje za subdomain path. Je to absolutní cesta (začíná '/').
     *
     * @param string $restUri
     * @return UrlInfoInterface
     */
    public function setRestUri($restUri): UriInfoInterface;

    /**
     * Absolutní cesta ke kořenovému adresáři skriptu. Začíná i končí '/'.
     *
     * @return string
     */
    public function getRootAbsolutePath();

    /**
     * RelaTivní cesta k aktuálnímu pracovnímu adresáři.
     *
     * @return string
     */
    public function getWorkingPath();

    /**
     * Absolutní cesta ke kořenovému adresáři skriptu. Začíná i končí '/'.
     *
     */
    public function setRootAbsolutePath($rootRelativePath);

    /**
     * RelaTivní cesta k aktuálnímu pracovnímu adresáři.
     *
     */
    public function setWorkingPath($workingPath);
}
