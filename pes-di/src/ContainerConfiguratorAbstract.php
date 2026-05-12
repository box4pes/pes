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
use Pes\Container\Exception\UnableToSetAliasException;

/**
 * Description of ContainerConfiguratorAbstract
 *
 * @author pes2704
 */
abstract class ContainerConfiguratorAbstract implements ContainerConfiguratorInterface {

    /**
     * (@inheritdoc)
     *
     * Abstraktní třída, očekává, že potomek implementuje metody rozhraní. Vytvořenému kontejneru jsou nastaveny parametry, aliasy, služby a továrny získané z metod potomka - konfigurátoru.
     * Při shodě jména služby a továrny je definice služby přepsána definicí továrny.
     *
     * @return ContainerSettingsAwareInterface
     */
    public function configure(ContainerSettingsAwareInterface $container) : ContainerSettingsAwareInterface {

        $params = $this->getParams();
        $aliases = $this->getAliases();
        $services = $this->getServicesDefinitions();
        $overrides = $this->getServicesOverrideDefinitions();
        $factories = $this->getFactoriesDefinitions();

        $container->addContainerInfo('Configured by '.get_called_class());

        foreach ($params as $name=>$value) {
            try {
                $container->param($name, $value);
            } catch(UnableToSetServiceException $uExc) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno $name již bylo použito pro konfiguraci parametru v tomto kontejneru.", 0, $uExc);
            }
        }
        foreach ($aliases as $alias=>$realName) {
            try {
                $container->alias($alias, $realName);
            } catch(UnableToSetAliasException $uExc) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno $alias již bylo použito pro konfiguraci alias v tomto kontejneru.", 0, $uExc);
            }
        }
        foreach ($services as $name=>$definition) {
            try {
                $container->set($name, $definition);
            } catch(UnableToSetServiceException $uExc) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno $name již bylo použito pro konfiguraci service v tomto kontejneru nebo v delegátovi.", 0, $uExc);
            }
        }
        foreach ($factories as $name=>$definition) {
            try {
                $container->factory($name, $definition);
            } catch(UnableToSetServiceException $uExc) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno $name již bylo použito pro konfiguraci factory.", 0, $uExc);
            }
        }
        foreach ($overrides as $name=>$definition) {
            try {
                $container->setOverride($name, $definition);
            } catch(UnableToSetServiceException $uExc) {
                throw new Exception\ConfiguratorDuplicateServiceDefinionException("Jméno $name již bylo použito pro konfiguraci service v tomto kontejneru.", 0, $uExc);
            }
        }

        return $container;
    }

    public function getAliases(): iterable {
        return [];
    }

    public function getParams(): iterable {
        return [];
    }

    public function getServicesDefinitions(): iterable {
        return [];
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getServicesOverrideDefinitions(): iterable {
        return [];
    }
}
