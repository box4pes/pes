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
use Pes\View\Renderer\Container\Exception\RendererNotExistsException;
use Pes\View\Renderer\Container\Exception\ClassIsNotARendererException;
/**
 * Description of RenderersContainer
 *
 * @author pes2704
 */
class TemplateRendererContainer implements TemplateRendererContainerInterface {

    private static $renderers;

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
        if (!isset(self::$renderers[$className])) {
            return $this->existRendererClass($className);
        }
        return true;
    }

    private function existRendererClass($className) {
        if ($className AND class_exists($className, TRUE)) {
            if (is_subclass_of($className, RendererInterface::class)) {   //proběhne autoload - pro neexistující třídu chyba
                    return true;
            } else {
                throw new ClassIsNotARendererException("požadovaná třída '$className' není typu ".RendererInterface::class.".");
            }
        }
        return false;
    }

    private function create($className) {
        if ($this->existRendererClass($className)) {
            $renderer = new $className();
            if ($renderer instanceof RendererRecordableInterface AND isset(self::$recorderProvider)) {
                $renderer->setRecorderProvider(self::$recorderProvider);
            }
        } else {
            throw new RendererNotExistsException("Neexistuje požadovaná třída default rendereru: $className v kontejneru ".__CLASS__.".");
        }
        return $renderer;
    }

    /**
     * Zadaný recorder provider bude nastaven všem rendererům vraceným kontejnerem metodou get(), které implementují interface RendererRecordableInterface.
     * @param RecorderProviderInterface $recorderProvider
     */
    public function setRecorderProvider(RecorderProviderInterface $recorderProvider) {
        self::$recorderProvider = $recorderProvider;
    }

}
