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
     * Část url path odpovídající subdoméně - relativní adresa kořenového skriptu aplikace vzhledem ke keřeni dokumentů (document root) - odpovídá poadresáři adresáře document root
     * @return string
     */
    public function getSubdomainPath();

    /**
     * Část url path odpovídající subdoméně - relativní adresa kořenového skriptu aplikace vzhledem ke keřeni dokumentů (document root) - odpovídá poadresáři adresáře document root
     * @param string $subdomainPath
     * @return UrlInfoInterface
     */
    public function setSubdomainPath($subdomainPath): UriInfoInterface;

    /**
     * Část url path odpovídající REST resource identifikátoru - část url, která již nemá předobraz v adresářové struktuře, následuje za subdomain path
     * @return string
     */
    public function getRestUri();

    /**
     * Úplná Relativní adresa zdroje. Obsahuje spojenou relativní adresu subdomény (subdomain path) a část url path odpovídající REST resource identifikátoru.
     * @return string
     */
    public function getUri();

    /**
     * Část url path odpovídající REST resource identifikátoru - část url, která již nemá předobraz v adresářové struktuře, následuje za subdomain path
     * @param string $restUri
     * @return UrlInfoInterface
     */
    public function setRestUri($restUri): UriInfoInterface;

    public function getRootAbsolutePath();

    public function getWorkingPath();

    public function setRootAbsolutePath($rootRelativePath);

    public function setWorkingPath($workingPath);
}
