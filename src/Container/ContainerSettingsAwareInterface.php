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
     * Nastaví kontejneru vlastnost jméno. Tato metoda slouží pouze pro ladění a generování textů výjimek - umožňuje udžet si přehled, ve kterém konteneru se hledá služba
     * i v případě použití více zanořených delegete kontejnerů
     * @param string $containerName
     */
    public function addContainerInfo($containerName): ContainerSettingsAwareInterface;

    /**
     * Volání metody MUSÍ znemožnit používání metody set().
     */
    public function lock(): ContainerSettingsAwareInterface;

    /**
     * Nastaví službu. Služba při prvním volání vytvoří instanci objektu a při dalších voláních vrací vždy tuto instanci.
     * Metoda set() nastavuje služby s unikátním jménem v rámci celého sestaveného kontejneru, tedy v rámci delegujícího kontejneru i všech delegátů.
     * Nelze definovat službu stejného jména v delegujícím kontejneru i v delegátovi, takové vlání set() vyhodí výjimku.
     *
     * Služby nastavené metodou set() je možno volat i z delegujících kontejnerů, teda jako služby delegáta.
     *
     * @param string $serviceName
     * @param mixed $service Closure nebo hodnota
     * @return ContainerSettingsAwareInterface
     */
    public function set($serviceName, $service) : ContainerSettingsAwareInterface;

    /**
     * Nastaví službu tak, že služba přetíží případnou službu stejného jména v kterémkoli delegátovi konfigurovaného kontejneru (ve vnořených kontejnerech).
     * Služba při prvním volání vytvoří instanci objektu a při dalších voláních vrací vždy tuto instanci.
     * Metoda setOverride() nastavuje služby s jménem, které bude použito v právě konfigurováném kontejneru a případně v dalších delegujících kontejnerech (obalujících),
     * přetíží tedy případnou služby stejného jména v kterémkoli delegátovi (ve vnořeném kontejneru).
     *
     * Služby nastavené metodou setOverride() je možno volat i z delegujících kontejnerů, teda jako služby delegáta.
     *
     * @param type $serviceName
     * @param type $service
     * @return \Pes\Container\ContainerSettingsAwareInterface
     */
    public function setOverride($serviceName, $service) : ContainerSettingsAwareInterface;

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
