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


use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

use Pes\Http\Uri;
use Pes\Http\Environment;

/**
 * Description of UriFactory
 *
 * @author pes2704
 */
class UriFactory implements UriFactoryInterface, EnvironmentAcceptInterface {

    /**
     * Create new Uri from string.
     *
     * @param  string $uriString Complete Uri string
     *     (i.e., https://user:pass@host:443/path?query).
     *
     * @return Psr\Http\Message\UriInterface
     */
    public function createUri(string $uriString=''): UriInterface
    {
        if (!is_string($uriString) && !method_exists($uriString, '__toString')) {
            throw new InvalidArgumentException('Uri must be a string');
        }

        $parts = parse_url($uriString);
        $scheme = isset($parts['scheme']) ? $parts['scheme'] : '';
        $user = isset($parts['user']) ? $parts['user'] : '';
        $pass = isset($parts['pass']) ? $parts['pass'] : '';
        $host = isset($parts['host']) ? $parts['host'] : '';
        $port = isset($parts['port']) ? $parts['port'] : null;
        $path = isset($parts['path']) ? $parts['path'] : '';
        $query = isset($parts['query']) ? $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? $parts['fragment'] : '';

        return new Uri($scheme, $host, $port, $path, $query, $fragment, $user, $pass);
    }

    /**
     * Create new Uri from environment.
     *
     * @param Environment $environment
     *
     * @return Pes\Http\Uri
     */
    public function createFromEnvironment(Environment $environment)
    {
        // Scheme
        $isSecure = $environment->get('HTTPS');
        $scheme = (empty($isSecure) || $isSecure === 'off') ? 'http' : 'https';

        // Authority: Username and password
        $username = $environment->get('PHP_AUTH_USER', '');
        $password = $environment->get('PHP_AUTH_PW', '');

        // Authority: Host
        if ($environment->has('HTTP_HOST')) {
            $host = $environment->get('HTTP_HOST');
        } else {
            $host = $environment->get('SERVER_NAME');
        }

        // Authority: Port
        $port = (int)$environment->get('SERVER_PORT', 80);
        if (preg_match('/^(\[[a-fA-F0-9:.]+\])(:\d+)?\z/', $host, $matches)) {
            $host = $matches[1];

            if ($matches[2]) {
                $port = (int) substr($matches[2], 1);
            }
        } else {
            $pos = strpos($host, ':');
            if ($pos !== false) {
                $port = (int) substr($host, $pos + 1);
                $host = strstr($host, ':', true);
            }
        }

        // Path
//        $requestScriptName = parse_url($environment->get('SCRIPT_NAME'), PHP_URL_PATH);
//        $requestScriptDir = dirname($requestScriptName);

        // parse_url() requires a full URL. As we don't extract the domain name or scheme,
        // we use a stand-in.
        $rUFromRequestUri = parse_url('http://example.com' . $environment->get('REQUEST_URI'), PHP_URL_PATH);
        $requestUri = $rUFromRequestUri ?? '';// parse_url() pro neexistující komponentu url vrací null, $requestUri musí být string
//        $basePath = '';
//        $virtualPath = $requestUri;
//        if (stripos($requestUri, $requestScriptName) === 0) {
//            $basePath = $requestScriptName;
//        } elseif ($requestScriptDir !== '/' && stripos($requestUri, $requestScriptDir) === 0) {
//            $basePath = $requestScriptDir;
//        }
//
//        if ($basePath) {
//            $virtualPath = ltrim(substr($requestUri, strlen($basePath)), '/');
//        }

        // Query string
        $queryString = $environment->get('QUERY_STRING', '');
        if ($queryString === '') {
            $qSFromRequestUri = parse_url('http://example.com' . $environment->get('REQUEST_URI'), PHP_URL_QUERY);
            $queryString = $qSFromRequestUri ?? '';// parse_url() pro neexistující komponentu url vrací null, $queryString musí být string
        }

        // Fragment
        $fragment = '';

        // Build Uri
//        $uri = new Uri($scheme, $host, $port, $virtualPath, $queryString, $fragment, $username, $password);
        $uri = new Uri($scheme, $host, $port, $requestUri, $queryString, $fragment, $username, $password);
//        if ($basePath) {
//            $uri = $uri->withBasePath($basePath);
//        }

        return $uri;
    }
}
