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

/**
 *
 * @author pes2704
 */
interface RouteInterface {

    /**
     * @return string Description
     */
    public function getMethod();

    /**
     * @return string Vrací zadaný urlPattern
     */
    public function getUrlPattern();

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
     * @param string $method
     */
    public function setMethod($method): RouteInterface;

    /**
     *
     * @param string $urlPattern
     */
    public function setUrlPattern($urlPattern): RouteInterface;

    /**
     *
     * @param callable $action
     */
    public function setAction(callable $action): RouteInterface;

}
