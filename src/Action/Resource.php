<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Action;

/**
 * Description of Resource
 *
 * @author pes2704
 */
class Resource implements ResourceInterface {

    private $httpMethod;
    private $urlPattern;

    public function __construct($httpMethod, $urlPattern) {
        $this->httpMethod = $httpMethod;
        $this->urlPattern =$urlPattern;
    }

    public function getHttpMethod() {
        return $this->httpMethod;
    }

    public function getUrlPattern() {
        return $this->urlPattern;
    }

    /**
     * Vrací REST path vytvořenou s použitím pattern routy a zadaných parametrů path. Parametry jsou vloženy na místa proměnných v pattern.
     *
     * @param array $pathParams
     * @return string
     * @throws UnexpectedValueException
     */
    public function getPathFor(array $pathParams) {
        $replaced = 0;
        $pattern = $this->urlPattern;
        foreach ($pathParams as $key => $value) {
            $pattern = str_replace(':'.$key, $value, $pattern, $replaced);
            if ($replaced==0) {
                throw new ActionPathParameterDoesNotMatch("Parameter not found in route pattern. Parameter: '$key'. Pattern: '$pattern'.");
            } elseif ($replaced>1) {
                throw new ActionPathParameterDoesNotMatch("Duplicate parameter in route pattern. Parameter: '$key'. Pattern: '$pattern'.");
            }
        }
        return $this->filterPath($pattern);
    }


    /**
     * Filter Uri path.
     *
     * This method percent-encodes all reserved
     * characters in the provided path string. This method
     * will NOT double-encode characters that are already
     * percent-encoded.
     *
     * @param  string $path The raw uri path.
     * @return string       The RFC 3986 percent-encoded uri path.
     * @link   http://www.faqs.org/rfcs/rfc3986.html
     */
    private function filterPath($path)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }
}
