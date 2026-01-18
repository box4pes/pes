<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View;

use Pes\View\View;
use Pes\View\CompositeView;
use Pes\View\Renderer\ImplodeRenderer;
use Psr\Container\ContainerInterface;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\NodeTemplate;
use Pes\Dom\Node\NodeInterface;

/**
 * Description of ViewFactory
 *
 * Vytvoří nový view.
 *
 * @author pes2704
 */
class ViewFactory implements ViewFactoryInterface {

    private $rendererContainer;

    public function setRendererContainer(ContainerInterface $rendererContainer): ViewFactoryInterface {
        $this->rendererContainer = $rendererContainer;
        return $this;
    }
    
    public function view(?iterable $data = null) {
        $view = new View();
        $this->setDataAndRendererContainer($view, $data);
    }
    
    public function compositeView(?iterable $data = null) {
        $view = new CompositeView();
        $this->setDataAndRendererContainer($view, $data);
    }
    
    public function implodeView(?iterable $data = null) {
        $view = new View();
        if (isset($data)) {
            $view->setData($data);
        }
        $view->setRenderer(new ImplodeRenderer());        
    }
    
    /**
     * Vytvoří nový view, přímo přetypovatelný na text. Pokud jsou zadána data, nastaví tomuto view i data, to je třeba, pokud zadaná šablona obsahuje proměnné.
     * Vytvořený objekt view je vhodný jako proměnná do šablony nebo jako view pro node typu TextView.
     *
     * Podrobně:
     * Vytvoří nový objekt view, nastaví mu objekt template. Metoda vytvořenému view nastaví
     * data potřebná pro renderování a případně i záznamový objekt pro záznam o užití dat při renderování.
     * Výsledný view obsahuje vše potřebné pro renderování a lze ho kdykoli přetypovat na text.
     *
     * @param type $templateFilename
     * @param type $data
     * @return View
     */
    public function phpTemplateView($templateFilename, ?iterable $data=null): View {
        $template = new PhpTemplate($templateFilename);  // NoTemplateFileException
        $view = (new View())->setTemplate($template);
        $this->setDataAndRendererContainer($view, $data);
        return $view;
    }

    public function phpTemplateCompositeView($templateFilename, ?iterable $data=null): View {
        $template = new PhpTemplate($templateFilename);
        $view = (new CompositeView())->setTemplate($template);
        $this->setDataAndRendererContainer($view, $data);
        return $view;
    }

    /**
     * Vytvoří nový view a nastaví mu rendererer a zadaný tag.
     * @param TagInterface $node
     * @return View
     */
    public function nodeTemplateView(NodeInterface $node): View {
        $template = new NodeTemplate($node);
        $view = (new View())->setTemplate($template);
        if ($this->rendererContainer) {
            $view->setRendererContainer($this->rendererContainer);
        }
        return $view;
    }
    
    private function setDataAndRendererContainer($view, ?iterable $data=null) {
        if(isset($data)) {
            $view->setData($data);
        }
        if (isset($this->rendererContainer)) {
            $view->setRendererContainer($this->rendererContainer);
        }        
    }
}
