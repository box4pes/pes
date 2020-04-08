<?php

namespace Pes\Action;

use Pes\Router\MethodEnum;
use Pes\Action\Exception\ActionPathParameterDoesNotMatch;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Action
 *
 * @author pes2704
 */
class Action implements ActionInterface {

    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var callable
     */
    private $actionCallable;

    public function __construct(ResourceInterface $resource, callable $actionCallable) {
        $this->resource = $resource;
        $this->actionCallable = $actionCallable;
    }

    public function getResource(): ResourceInterface {
        return $this->resource;
    }

    public function getActionCallable() {
        return $this->actionCallable;
    }

}
