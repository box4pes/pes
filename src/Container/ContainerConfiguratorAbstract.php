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

/**
 * Description of ContainerConfiguratorAbstract
 *
 * @author pes2704
 */
abstract class ContainerConfiguratorAbstract implements ContainerConfiguratorInterface {

    /**
     * (@inheritdoc)
     *
     * Abstraktní třída, očekává, že potomek implementuje metody rorhraní. Vytvořenému kontejneru jsou nastaveny aliasy, služby a továrny
     * na hodnoty získané z metod potomka - konfigurátoru.
     * Při shodě jména služby a továrny je definice služby přepsána definicí továrny.
     *
     * @return ContainerSettingsAwareInterface
     */
    public function configure(ContainerSettingsAwareInterface $container) : ContainerSettingsAwareInterface {

        $aliases = $this->getAliases();
        if (!(is_array($aliases) OR $aliases instanceof \Traversable)) {
            throw new \UnexpectedValueException("Metoda getAliases() konfigurátoru kontejneru ". get_called_class()." nevrátila iterovatelnou hodnotu.");
        }
        $services = $this->getServicesDefinitions();
        if (!(is_array($services) OR $services instanceof \Traversable)) {
            throw new \UnexpectedValueException("Metoda getServicesDefinitions() konfigurátoru kontejneru ". get_called_class()." nevrátila iterovatelnou hodnotu.");
        }
        $factories = $this->getFactoriesDefinitions();
        if (!(is_array($factories) OR $factories instanceof \Traversable)) {
            throw new \UnexpectedValueException("Metoda getFactoriesDefinitions() konfigurátoru kontejneru ". get_called_class()." nevrátila iterovatelnou hodnotu.");
        }

        $container->setContainerName('Configured by '.get_called_class());

        foreach ($aliases as $alias=>$realName) {
            $container->alias($alias, $realName);
        }
        foreach ($services as $key=>$definition) {
            if (array_key_exists($key, $aliases)) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno alias, služby nebo factory lze použít pouze jednou. Jméno služby $definition již bylo použito pro alias.");
            }
            $container->set($key, $definition);
        }
        foreach ($factories as $key=>$definition) {
            if (array_key_exists($key, $aliases) OR array_key_exists($key, $services)) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno alias, služby nebo factory lze použít pouze jednou. Jméno factory $definition již bylo použito pro alias nebo službu.");
            }
            $container->factory($key, $definition);
        }
        return $container;
    }
}
