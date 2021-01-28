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

use Pes\View\Renderer\RendererInterface;
use Psr\Container\ContainerInterface;
use Pes\View\Template\TemplateInterface;

/**
 *
 * @author pes2704
 */
interface ViewInterface {

    /**
     * Vytvoří textový obsah.
     * @return string
     */
    public function getString();

    public function __toString();

    /**
     * Nastaví renderer. Pokud je nastaven, je použit při renderování přednostně před rendererem z kontejneru nebo default rendererem templaty.
     * @param RendererInterface $renderer
     * @return \Pes\View\ViewInterface
     */
    public function setRenderer(RendererInterface $renderer): ViewInterface;

    /**
     * Nastaví objekt renderer kontejner.
     *
     * @param ContainerInterface $rendererContainer
     * @return \Pes\View\ViewInterface
     */
    public function setRendererContainer(ContainerInterface $rendererContainer): ViewInterface;

    /**
     * Nastaví jméno služby renderer kontejneru, která musí vracet renderer.
     * Pokud je nastaveno toto jméno služby, použije se renderer vrácená tenderer kontejnerem pro renderování.
     *
     * @param $rendererName
     * @return ViewInterface
     */
    public function setRendererName($rendererName): ViewInterface;

    public function setFallbackRenderer(RendererInterface $renderer): ViewInterface;

    public function setFallbackRendererName($fallbackRendererName): ViewInterface;

    /**
     * Nastaví template objekt pro renderování. Tato template bude použita metodou render().
     *
     * @param TemplateInterface $template
     * @return \Pes\View\ViewInterface
     */
    public function setTemplate(TemplateInterface $template): ViewInterface;

    /**
     * Lze nastavit iterable data pro renderování. Tato data budou použita metodou render().
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function setData(iterable $data): ViewInterface;

    /**
     * Nastaví objekt - view model. View model bude použit, pokud renderer získaný protected metodou resolveREndere() bude typu
     */
    public function setViewModel($viewModel) {


}
}

