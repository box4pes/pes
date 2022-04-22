<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Container;

use Pes\Container\ContainerSettingsAwareInterface;

/**
 *
 * @author pes2704
 */
interface ContainerConfiguratorInterface {

    /**
     * Konfiguruje objekt kontejneru zadaný v konstruktoru a případně přidá delegate kontejner.
     *
     */
    public function configure(ContainerSettingsAwareInterface $container) : ContainerSettingsAwareInterface;

    /**
     * Vrací pole parametrů - hodnot nezávislých na službách kontejneru
     */
    public function getParams(): iterable;

    /**
     * Vrací pole aliasů ke skutečným jménům služeb.
     */
    public function getAliases(): iterable;

    /**
     * Vrací pole definic služeb kontejneru. Služby kontejneru vracejí při opakovaném volání vždy stejnou instanci proměnné.
     */
    public function getServicesDefinitions(): iterable;

    /**
     * Vrací pole definic továren kontejneru. Továrna kontejneru je zvláštní služba, továrny vracejí při opakovaném volání vždy novou instanci proměnné.
     */
    public function getFactoriesDefinitions(): iterable;
}
