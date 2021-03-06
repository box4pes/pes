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
    public function getParams();

    /**
     * Vrací pole aliasů ke skutečným jménům služeb.
     */
    public function getAliases();

    /**
     * Vrací pole definic služeb kontejneru. Služby kontejneru vracejí při opakovaném volání vždy stejnou instanci proměnné.
     */
    public function getServicesDefinitions();

    /**
     * Vrací pole definic služeb kontejneru. Služby kontejneru vracejí při opakovaném volání vždy stejnou instanci proměnné. a je možno je definovat
     * duplicitně v rámci celého kenteneru složeného z delegátů.
     */
    public function getServicesOverrideDefinitions();

    /**
     * Vrací pole definic továren kontejneru. Továrna kontegneru je zvláštní služba, továrny vracejí při opakovaném volání vždy novou instanci proměnné.
     */
    public function getFactoriesDefinitions();
}
