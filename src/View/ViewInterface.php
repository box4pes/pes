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
     * Nastaví objekt renderer kontejner.
     *
     * @param ContainerInterface $rendererContainer
     * @return \Pes\View\ViewInterface
     */
    public function setRendererContainer(ContainerInterface $rendererContainer): ViewInterface;

    /**
     * Nastaví renderer. Pokud je nastaven, je použit při renderování přednostně před rendererem z kontejneru nebo default rendererem templaty.
     * @param RendererInterface $renderer
     * @return \Pes\View\ViewInterface
     */
    public function setRenderer(RendererInterface $renderer): ViewInterface;

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
     * Nastaví objekt - view model pro renderování. View model bude použit, pokud renderer získaný protected metodou resolveREndere() bude typu RendererModelAwareInterface.

     */
    public function setViewModel($viewModel): ViewInterface;

    /**
     * Metoda je prázdná a je připravena pro přetížení v objektu potomka. Slouží pro provedení operací, které je třeba provést vždy bezprostředně před zvolením rendereru
     * (provádí View metoodou resolveRenderer()) voláním metody render rendereru.
     *
     * @return void
     */
    public function beforeRenderingHook(): void;


    /**
     * Přijímá dvojici komponentní view (typu ViewInterface) nebo null a jméno proměnné.
     * Kompozitní view při renderování nahradí proměnou komponentním view převedeným na string (volá metodu __toString()).
     * Pokud předaná hodnota komponentního view je null, musí kompozitní view proměnnou nahrazovat prázdným řetězcem.
     *
     * @param ViewInterface $componentView
     * @param string $name
     * @return ViewInterface
     */
    public function appendComponentView(ViewInterface $componentView, $name): ViewInterface ;


    /**
     * Metoda pro přidání komponentních view jako pole nebo \Traversable objekt.
     * @param iterable $componentViews
     * @return ViewInterface
     */
    public function appendComponentViews(iterable $componentViews): ViewInterface ;
}

