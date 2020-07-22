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

use Psr\Container\ContainerInterface;

/**
 *
 * @author pes2704
 */
interface ContainerSettingsAwareInterface extends ContainerInterface {

    /**
     * Nastaví kontejneru vlastnost jméno. Tato metoda slouží pouze pro ladění - umožňuje udžet si přehled, ve kterém konteneru se hledá služba
     * i v případě použití více zanořených delegete kontejnerů
     * @param string $containerName
     */
    public function setContainerName($containerName);

    /**
     * Volání metody MUSÍ znemožnit používání metody set().
     */
    public function lock();

    /**
     * Nastaví službu. Služba při prvním volání vytvoří instatnci objektu a při dalších voláních vrací vždy tuto instanci.
     * @param string $serviceName
     * @param mixed $service Closure nebo hodnota
     * @return ContainerSettingsAwareInterface
     */
    public function set($serviceName, $service) : ContainerSettingsAwareInterface;

    /**
     * Nastaví factory. Factory při každém volání vytváří objekt znovu.
     * @param string $factoryName
     * @param mixed $service Closure nebo hodnota
     * @return ContainerSettingsAwareInterface
     */
    public function factory($factoryName, $service) : ContainerSettingsAwareInterface;

    /**
     * Nastaví alias. Alias je jméno, které je aliasem pro jméno služby ne jméno factory.
     * @param string $alias
     * @param string $name
     * @return ContainerSettingsAwareInterface
     */
    public function alias($alias, $name) : ContainerSettingsAwareInterface;

    /**
     * Smaže instanci objektu vraceného službou. Příští volání služby tak vytvoří nový objekt. Dále se pak služba chová standartně, vrací stále stejný objekt.
     * @param type $serviceName
     * @return ContainerSettingsAwareInterface
     */
    public function reset($serviceName)  : ContainerSettingsAwareInterface;

}
