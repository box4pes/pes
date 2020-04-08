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

use Pes\Container\Exception;

/**
 * Description of AutowiringContainer
 *
 * @author pes2704
 */
class AutowiringContainer extends Container implements AutowiringContainerInterface {

    private $throwExceptions;

    /**
     * Nastaví kontejner dorežimu vyhazování výjimky, pokud sse nepodaří autowiringem vytvořit službu. Výjimku pak vyhazuje při volání
     * metos has() a get(). Výchozí chování autowiring kontejnetu je stejné jako standartního kontejneru, pro neexistující služby pouze vrací FALSE.
     *
     * @param bool $throwExceptions
     */
    public function throwExceptions($throwExceptions=\FALSE) {
        $this->throwExceptions = $throwExceptions;
    }
    /**
     *
     * {@inheritdoc}
     *
     * Pokud služba není definovaná, metoda se pokusí vytvořit factory v autowiring kontejneru na základě jména třídy, které použije jako jméno služby.
     * Pokud uspěje vrací TRUE, jinak dojde k vyhození výjimky v metodě, která se pokouší vytvořit factory pomocí autowiringu.
     *
     */
    public function has($serviceName) {
        $realName = $this->realName($serviceName);
        if(parent::has($realName)) {
            return TRUE;
        } else {
            return $this->createFactory($realName, FALSE);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Pokud služba není definovaná, metoda se pokusí vytvořit factory v autowiring kontejneru na základě jména třídy, které použije jako jméno služby.
     * Pokud uspěje vrací hodnotu poskytnutou vyzvořenou službou, jinak dojde k vyhození výjimky.
     *
     * @param string $serviceName Jméno služby
     * @return mixed Hodnota vracená službou
     * @throws Exception\NotFoundException Služba není definována...
     */
    public function get($serviceName) {
        // je service/factory definovaná?
        $realName = $this->realName($serviceName);
        if(parent::has($realName)) {
            return parent::get($realName);
        } else {
            try {
                if ($this->createFactory($realName, TRUE)) {
                    return parent::get($realName);
                } else {
                    throw new \LogicException("Služba $realName není definována a metoda createFactory() nevytvořila factory ani nevyhoďila výjimku.");
                }
            } catch (Exception\NotFoundException $nfExc) {
                throw new Exception\NotFoundException($nfExc->getMessage());
            }
        }
    }

    /**
     * Pokud jméno není jméno třídy nebo objekt nelze z nějakého důvodu instancovat - vrací FALSE.
     * Jinak nalezne konstruktor. Pokud třída konstruktor nemá nastaví kontejneru factory, která vytváří instance objektu bez volání konstruktoru.
     * Pokud třída má konstruktor najde parametry konstruktoru a pokuší se přiřadit hodnoty parametrům konstruktoru.
     * Hodnoty parametrl získá metodou getConstructorDependencies() a nastaví kontejneru factory, která vytváří instance objektu s předáním
     * získaných hodnot parametrů (závislostí) konstruktoru.
     *
     * @param type $realName
     * @return boolean
     */
    private function createFactory($realName, $throwExceptions) {
        if ( ! \class_exists($realName)) {
            if ($throwExceptions) {
                throw new Exception\NotFoundException("Služba $realName není definována a třída se jménem $realName neexistuje.");
            } else {
                return FALSE;
            }
        }
        $reflector = new \ReflectionClass($realName);
        // check if class is instantiable
        if ( ! $reflector->isInstantiable()) {
            if ($throwExceptions) {
                throw new Exception\NotFoundException("Služba $realName není definována, definice třídy $realName existuje, ale objekt třídy $realName nelze instancovat.");
            } else {
                return FALSE;
            }
        }
        $constructor = $reflector->getConstructor();
//        $publicMehods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);  // příprava pro wiring metod
        if (is_null($constructor)) {
            // třída nemá konstruktor - factory, která instancuje bez parametrů
            $this->factory($realName, function () use($reflector) {
                return $reflector->newInstance();  // $reflector->newInstanceWithoutConstructor();
            });  //newInstanceWithoutConstructor()
        } else {
            // parametry konstruktoru
            $dependencies = $this->getConstructorDependencies($constructor);
            // factory, která vytváří instanci s použitím vytvořených parametrů
            $this->factory($realName, function () use($reflector, $dependencies) {
                return $reflector->newInstanceArgs($dependencies);
            });
        }
        return TRUE;
    }
    /**
     * Vrací pole jmen tříd (FQN)
     * @param \ReflectionMethod $contructor
     * @return type
     * @throws Exception\AutowireDependencyResolvingException
     */
    private function getConstructorDependencies(\ReflectionMethod $contructor) {
        $dependencies = [];
        /* @var $parameter ReflectionParameter */
        foreach ($contructor->getParameters() as $parameter) {
            // nová instance type hinted třídy
            $dependency = $parameter->getClass();  // vrací ReflectionClass závislosti (ta má vlastost ->name, která obsahuje typ uvedený u proměnné v parametru konstruktoru - obvykle interface
            if (isset($dependency)) {
                // get dependency resolved
                $dependencies[] = $this->get($dependency->name);  // poždáám službu kontejneru se jménem třídy závislosti
            } else {
                // nepodařilo se vytvořit instanci - typicky není type hint u proměnné nebo parametr je skalár nebo callback
                // pokus se získat hodnotu z kontejneru
                if ($this->has($parameter->name)) {
                    $dependencies[] = $this->get($parameter->name);
                // pokus se získat default hodnotu parametru
                } elseif ($parameter->isDefaultValueAvailable()) {
                    // hodnota závislosti je default hodnota parametru
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                        throw new Exception\AutowireDependencyResolvingException(
                            "Nepodařilo se vytvořit parametr, proměnnou \$$parameter->name při autowire vytváření objektu $contructor->class, metoda $contructor->name( ... )");
                }
            }
        }
        return $dependencies;
    }
}
