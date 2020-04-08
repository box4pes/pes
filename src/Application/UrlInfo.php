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
 * Description of App
 *
 * @author pes2704
 */
class UrlInfo implements UriInfoInterface {

    /**
     * @var string
     */
    protected $subdomainPath;

    /**
     * @var string
     */
    protected $restUri;

    protected $rootRelativePath;

    protected $workingPath;

    /**
     * Část url path odpovídající subdoméně - relativní adresa kořenového skriptu aplikace vzhledem ke keřeni dokumentů (document root) - odpovídá poadresáři adresáře document root
     * @return string
     */
    public function getSubdomainPath() {
        return $this->subdomainPath;
    }

    /**
     * Část url path odpovídající subdoméně - relativní cesta kořenového skriptu aplikace vzhledem ke keřeni dokumentů (document root) - odpovídá poadresáři adresáře document root
     * @param string $subdomainPath
     * @return UrlInfoInterface
     */
    public function setSubdomainPath($basePath): UriInfoInterface {
        $this->subdomainPath = $basePath;
        return $this;
    }

    /**
     * Část url path odpovídající REST resource identifikátoru - část url, která již nemá předobraz v adresářové struktuře, následuje za subdomain path
     * @return string
     */
    public function getRestUri() {
        return $this->restUri;
    }

    /**
     * Část url path odpovídající REST resource identifikátoru - část url, která již nemá předobraz v adresářové struktuře, následuje za subdomain path
     * @param string $restUri
     * @return UrlInfoInterface
     */
    public function setRestUri($restUri): UriInfoInterface {
        $this->restUri = $restUri;
        return $this;
    }

    public function getRootRelativePath() {
        return $this->rootRelativePath;
    }

    public function getWorkingPath() {
        return $this->workingPath;
    }

    public function setRootRelativePath($rootAbsolutePath) {
        $this->rootRelativePath = $rootAbsolutePath;
        return $this;
    }

    public function setWorkingPath($workingPath) {
        $this->workingPath = $workingPath;
        return $this;
    }


}
