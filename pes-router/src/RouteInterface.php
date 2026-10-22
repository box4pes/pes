<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Router;

use Pes\Router\Resource\ResourceInterface;

/**
 *
 * @author pes2704
 */
interface RouteInterface {

    /**
     * @return ResourceInterface
     */
    public function getResource(): ResourceInterface;

    /**
     * @return string Vrací regulární výraz vytvořený z parametru urlPattern
     */
    public function getPatternPreg();

    /**
     * @return callable Vrací spustitelnou akci routy.
     */
    public function getAction();

    /**
     *
     * @param ResourceInterface $resource
     * @return \Pes\Router\RouteInterface
     */
    public function setResource(ResourceInterface $resource): RouteInterface;

    /**
     *
     * @param callable $action
     * @return \Pes\Router\RouteInterface
     */
    public function setAction(callable $action): RouteInterface;

}
