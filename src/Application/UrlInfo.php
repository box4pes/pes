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
    protected $subdomainUri;
    /**
     * @var string
     */
    protected $subdomainPath;

    /**
     * @var string
     */
    protected $restUri;

    /**
     * @var string
     */
    protected $rootAbsolutePath;

    /**
     * @var string
     */
    protected $workingPath;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSubdomainUri(): string {
        return $this->subdomainUri;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSubdomainPath(): string {
        return $this->subdomainPath;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getRootAbsolutePath(): string {
        return $this->rootAbsolutePath;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getWorkingPath(): string {
        return $this->workingPath;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getRestUri(): string {
        return $this->restUri;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getUri(): string {
        return $this->rootAbsolutePath.ltrim($this->restUri, "/");
    }

    /**
     * {@inheritdoc}
     *
     * @param string $basePath
     * @return UriInfoInterface
     */
    public function setSubdomainUri(string $basePath): UriInfoInterface {
        $this->subdomainUri = rawurldecode($basePath);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $subdomainPath
     * @return UriInfoInterface
     */
    public function setSubdomainPath(string $subdomainPath): UriInfoInterface {
        $this->subdomainPath = rawurldecode($subdomainPath);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $restUri
     * @return UrlInfoInterface
     */
    public function setRestUri(string $restUri): UriInfoInterface {
        $this->restUri = rawurldecode($restUri);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param type $rootAbsolutePath
     * @return $this
     */
    public function setRootAbsolutePath(string $rootAbsolutePath): UriInfoInterface {
        $this->rootAbsolutePath = rawurldecode($rootAbsolutePath);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param type $workingPath
     * @return $this
     */
    public function setWorkingPath(string $workingPath): UriInfoInterface {
        $this->workingPath = rawurldecode($workingPath);
        return $this;
    }


}
