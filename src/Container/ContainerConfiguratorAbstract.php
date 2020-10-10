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

use Pes\Container\Exception\UnableToSetServiceException;

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

        $params = $this->getParams();
        if (!(is_array($params) OR $params instanceof \Traversable)) {
            throw new \UnexpectedValueException("Metoda getParams() konfigurátoru kontejneru ". get_called_class()." nevrátila iterovatelnou hodnotu.");
        }
        $aliases = $this->getAliases();
        if (!(is_array($aliases) OR $aliases instanceof \Traversable)) {
            throw new \UnexpectedValueException("Metoda getAliases() konfigurátoru kontejneru ". get_called_class()." nevrátila iterovatelnou hodnotu.");
        }
        $services = $this->getServicesDefinitions();
        if (!(is_array($services) OR $services instanceof \Traversable)) {
            throw new \UnexpectedValueException("Metoda getServicesDefinitions() konfigurátoru kontejneru ". get_called_class()." nevrátila iterovatelnou hodnotu.");
        }
        $servicesOverrides = $this->getServicesOverrideDefinitions();
        if (!(is_array($servicesOverrides) OR $servicesOverrides instanceof \Traversable)) {
            throw new \UnexpectedValueException("Metoda getServicesOverrideDefinitions() konfigurátoru kontejneru ". get_called_class()." nevrátila iterovatelnou hodnotu.");
        }
        $factories = $this->getFactoriesDefinitions();
        if (!(is_array($factories) OR $factories instanceof \Traversable)) {
            throw new \UnexpectedValueException("Metoda getFactoriesDefinitions() konfigurátoru kontejneru ". get_called_class()." nevrátila iterovatelnou hodnotu.");
        }

        $container->addContainerInfo('Configured by '.get_called_class());

        foreach ($params as $name=>$definition) {
            try {
                $container->set($name, $definition);
            } catch(UnableToSetServiceException $uExc) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno služby kontejneru lze použít pouze jednou. Jméno služby $name již bylo použito.", 0, $uExc);
            }
        }
        foreach ($aliases as $alias=>$realName) {
            $container->alias($alias, $realName);
        }
        foreach ($services as $name=>$definition) {
            try {
                $container->set($name, $definition);
            } catch(UnableToSetServiceException $uExc) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno služby kontejneru lze použít pouze jednou. Jméno služby $name již bylo použito.", 0, $uExc);
            }
        }
        foreach ($servicesOverrides as $name=>$definition) {
            $container->setOverride($name, $definition);
        }
        foreach ($factories as $name=>$definition) {
            try {
                $container->factory($name, $definition);
            } catch(UnableToSetServiceException $uExc) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno služby kontejneru lze použít pouze jednou. Jméno služby $name již bylo použito.", 0, $uExc);
            }
        }
        return $container;
    }
}
