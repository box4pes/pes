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
     * Nastaví službu, která vrací hodnotu typu scalar nebo array.
     *
     * <b>"Přetížení služby" pro parametr:</b>
     * Kontejner umožňuje nastavit stejně pojmenovaný parametr jako má parametr již definovaný v delegátovi, nový parametr tak může
     * obsahovat jinou hodnotu než parametr již definovaný v delegátovi. Tímto způsobem lze simulovat "přetížení" parametru
     * definované v delegátovi parametrem definovaným v podřízeném kontejneru.
     *
     * @param string $parameterName
     * @param scalar|array $value
     * @return ContainerSettingsAwareInterface
     */
    public function param(string $parameterName, $value): ContainerSettingsAwareInterface;

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
    public function set(string $serviceName, $service) : ContainerSettingsAwareInterface;

    /**
     * Nastaví službu obdobně jako metoda set, rozdíl je pouze v možnostech opakované definice, "přetížení" služby.
     * Nastaví definici služby s daným jménem. Služba je volaná metodou get() kontejneru a vrací hodnotu.
     * Služba definovaná metodou set() generuje hodnotu pouze jednou, při prvním volání metody kontejneru get(), další volání metody get() vrací
     * tutéž hodnotu. Pokud služba generuje objekt, každé volání get() vrací stejnou instanci objektu.
     * Služba musí být Closure nebo přímo zadaná hodnota. Generování hodnoty zadanou službou probíhá až v okamžiku volání metody get().
     * Pokud je služba typu \Closure, provede se se až v okamžiku volání metody get() kontejneru, jde tedy o lazy load generování hodnoty.
     *
     * <b>Předefinování služby ("Přetížení služby"):</b>
     * Metoda umožňuje nastavit stejně pojmenovanou službu jako je již definovaná služna v delegátovi, nová služba tak může
     * vracet jiný objekt než služba definovaná v delegátovi. Tímto způsobem lze simulovat "přetížení" služby
     * definované v delegátovi službou definovanou v podřízeném kontejneru. Metoda kontejneru get() hledá vždy nejdříve v kontejneru, ve kterém je definovány a pokud nenalezne
     * volanou služby, pak postupně v jednotlivých delegátech (deleguje hledání služby).
     *
     * @param string $serviceName
     * @param type $service
     * @return ContainerSettingsAwareInterface
     */
    public function setOverride(string $serviceName, $service) : ContainerSettingsAwareInterface;

    /**
     * Nastaví factory. Factory při každém volání vytváří objekt znovu.
     * @param string $factoryName
     * @param mixed $service Closure nebo hodnota
     * @return ContainerSettingsAwareInterface
     */
    public function factory(string $factoryName, $service) : ContainerSettingsAwareInterface;

    /**
     * Nastaví alias. Alias je jméno, které je aliasem pro jméno služby ne jméno factory.
     * @param string $alias
     * @param string $name
     * @return ContainerSettingsAwareInterface
     */
    public function alias(string $alias,string $name) : ContainerSettingsAwareInterface;

    /**
     * Smaže instanci objektu vraceného službou. Příští volání služby tak vytvoří nový objekt. Dále se pak služba chová standartně, vrací stále stejný objekt.
     * @param string $serviceName
     * @return ContainerSettingsAwareInterface
     */
    public function reset(string $serviceName)  : ContainerSettingsAwareInterface;

}
