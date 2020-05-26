<?php

namespace Pes\Action;

use Pes\Action\Exception\ActionHttpMethodNotValid;
use Pes\Action\Exception\ActionUrlPatternNotValid;
use Pes\Action\Exception\ActionUrlPatternDuplicate;
use Pes\Action\Exception\DuplicateName;

use Pes\Router\MethodEnum;
use Pes\Type\Exception\TypeExceptionInterface;
use Pes\Router\UrlPatternValidator;
use Pes\Router\Exception\WrongPatternFormatException;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Registry
 *
 * @author pes2704
 */
class Registry implements RegistryInterface {

    private $methodsEnum;
    private $urlPatternValidator;

    /**
     * @var Action array of
     */
    private $actions=[];

    public function __construct(MethodEnum $methodEnum, UrlPatternValidator $urlPatternValidator) {
        $this->methodsEnum = $methodEnum;
        $this->urlPatternValidator = $urlPatternValidator;
    }

    const SEPARATOR = '->';

    /**
     *
     * @param \Pes\Action\ActionInterface $action
     * @return void
     * @throws WrongName
     * @throws DuplicateName
     * @throws ActionHttpMethodNotValid
     * @throws ActionUrlPatternNotValid
     */
    public function register($prefix, ActionInterface $action): void {

        try {
            $httpMethod = ($this->methodsEnum)($action->getResource()->getHttpMethod());
        } catch (TypeExceptionInterface $e) {
            throw new ActionHttpMethodNotValid("Passed action HTTP method {$action->getResource()->getHttpMethod()} is not valid.", 0, $e);
        }

        $urlPattern = $action->getResource()->getUrlPattern();
        try {
            $this->urlPatternValidator->validate($urlPattern);
        } catch (WrongPatternFormatException $e) {
            throw new ActionUrlPatternNotValid("Passed action URL pattern $urlPattern is not valid.", 0, $e);
        }

        if (array_key_exists($urlPattern, $this->actions)) {
            throw new ActionUrlPatternDuplicate("Duplicitní url pattern '$urlPattern'.");
        } else {
            $this->actions[$prefix][$httpMethod][$urlPattern] = $action;
        }
    }

    public function getAction($prefix, $httpMethod, $urlPattern): \Pes\Action\ActionInterface {
        $this->actions[$prefix][$httpMethod][$urlPattern];
    }

    public function getRoutedSegment($prefix, $httpMethod): \Traversable {
        return new \ArrayIterator($this->actions[$prefix][$httpMethod]);
    }

}
