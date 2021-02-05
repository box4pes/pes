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

    protected $rootAbsolutePath;

    protected $workingPath;

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getSubdomainUri() {
        return $this->subdomainPath;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getRootAbsolutePath() {
        return $this->rootAbsolutePath;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getWorkingPath() {
        return $this->workingPath;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getRestUri() {
        return $this->restUri;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getUri() {
        return $this->getSubdomainUri().ltrim($this->restUri);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $basePath
     * @return UriInfoInterface
     */
    public function setSubdomainUri($basePath): UriInfoInterface {
        $this->subdomainPath = $basePath;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $restUri
     * @return UrlInfoInterface
     */
    public function setRestUri($restUri): UriInfoInterface {
        $this->restUri = $restUri;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param type $rootAbsolutePath
     * @return $this
     */
    public function setRootAbsolutePath($rootAbsolutePath) {
        $this->rootAbsolutePath = $rootAbsolutePath;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param type $workingPath
     * @return $this
     */
    public function setWorkingPath($workingPath) {
        $this->workingPath = $workingPath;
        return $this;
    }


}
