<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Renderer\Container;

use Pes\View\Recorder\RecorderProviderInterface;
use Pes\View\Renderer\RendererInterface;
use Pes\View\Renderer\RendererRecordableInterface;
use Pes\View\Renderer\Container\Exception\RendererClassNotExistsException;
use Pes\View\Renderer\Container\Exception\ClassIsNotARendererInterfaceException;
/**
 * Description of RenderersContainer
 *
 * @author pes2704
 */
class TemplateRendererContainer implements TemplateRendererContainerInterface {

    private static $renderers = [];

    private static $failed = [];

    /**
     * @var RecorderProviderInterface
     */
    private static $recorderProvider;

    /**
     * Vrací renderer třídy zadané jako parametr.
     * Vrací vždy stejnou instanci rendereru se zadaným jménem třídy (singleton).
     * Umožňuje nastavit automaticky všem rendererům (pokud jsou typu RendererRecordableInterface) objekt recorder provider.
     *
     * Při prvním volání je instancován daný typ rendereru a je mu nastaven recorder provider, pokud byl zadán. Metoda tak vrací stejnou instanci rendereru
     * včetně nastaveného recorder provideru.
     *
     * @return type
     */
    public function get($className) {
        if (!isset(self::$renderers[$className])) {
            self::$renderers[$className] = self::create($className);
        }
        return self::$renderers[$className];
    }

    public function has($className): bool {
        return isset(self::$renderers[$className]);
    }

    private function create($className) {
        if ($this->existRendererClass($className)) {
            $renderer = new $className();
            if ($renderer instanceof RendererRecordableInterface AND isset(self::$recorderProvider)) {
                $renderer->setRecorderProvider(self::$recorderProvider);
            }
        }
        return $renderer;
    }
    
    private function existRendererClass($className) {
        if (!$className) {
            throw new RendererClassNotExistsException("Zadán prázdný název třády rendereru v kontejneru ".__CLASS__.".");
        }
        if (array_key_exists($className, self::$failed)) {
            return false;
        }
        if (!class_exists($className, true)) {   //proběhne autoload - pro neexistující třídu chyba
            self::$failed[$className] = true;
            return false;
        }
        if (!is_subclass_of($className, RendererInterface::class)) {   //proběhne autoload - pro neexistující třídu chyba
            throw new ClassIsNotARendererInterfaceException("požadovaná třída '$className' není typu ".RendererInterface::class.".");
        }
        return true;
    }


    /**
     * Zadaný recorder provider bude nastaven všem rendererům vraceným kontejnerem metodou get(), které implementují interface RendererRecordableInterface.
     * @param RecorderProviderInterface $recorderProvider
     */
    public function setRecorderProvider(RecorderProviderInterface $recorderProvider) {
        self::$recorderProvider = $recorderProvider;
    }

}
