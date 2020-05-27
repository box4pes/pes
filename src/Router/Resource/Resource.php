<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Router\Resource;

use Pes\Router\Resource\Exception\ResourceHttpMethodNotValid;
use Pes\Router\Resource\Exception\ResourceUrlPatternNotValid;
use Pes\Router\Resource\Exception\ResourcePathParameterDoesNotMatch;

use Pes\Router\MethodEnum;
use Pes\Type\Exception\TypeExceptionInterface;
use Pes\Router\UrlPatternValidator;
use Pes\Router\Exception\WrongPatternFormatException;

/**
 * Description of Resource
 *
 * @author pes2704
 */
class Resource implements ResourceInterface {

    private $methodsEnum;
    private $urlPatternValidator;

    private $httpMethod;
    private $urlPattern;

    public function __construct(MethodEnum $methodEnum, UrlPatternValidator $urlPatternValidator) {
        $this->methodsEnum = $methodEnum;
        $this->urlPatternValidator = $urlPatternValidator;
    }

    public function withHttpMethod($httpMethod): ResourceInterface {
        try {
            $httpMethodValue = ($this->methodsEnum)($httpMethod);
        } catch (TypeExceptionInterface $e) {
            throw new ResourceHttpMethodNotValid("Passed HTTP method {$httpMethod} is not valid.", 0, $e);
        }
        $cloned = clone $this;
        $cloned->httpMethod = $httpMethodValue;
        return $cloned;
    }

    public function withUrlPattern($urlPattern): ResourceInterface {
        try {
            $this->urlPatternValidator->validate($urlPattern);
        } catch (WrongPatternFormatException $e) {
            throw new ResourceUrlPatternNotValid("Passed URL pattern $urlPattern is not valid.", 0, $e);
        }
        $cloned = clone $this;
        $cloned->urlPattern = $urlPattern;
        return $cloned;
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
                throw new ResourcePathParameterDoesNotMatch("Parameter not found in route pattern. Parameter: '$key'. Replaced pattern: '$pattern'.");
            } elseif ($replaced>1) {
                throw new ResourcePathParameterDoesNotMatch("Duplicate parameter in route pattern. Parameter: '$key'. Replaced pattern: '$pattern'.");
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
