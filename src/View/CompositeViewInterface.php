<?php

namespace Pes\View;

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View;

/**
 *
 * @author pes2704
 */
interface CompositeViewInterface extends ViewInterface {

    /**
     * Přijímá dvojici komponentní view (typu ViewInterface) nebo null a jméno proměnné.
     * Kompozitní view při renderování nahradí proměnou komponentním view převedeným na string (volá metodu __toString()).
     * Pokud předaná hodnota komponentního view je null, musí kompozitní view proměnnou nahrazovat prázdným řetězcem.
     *
     * @param ViewInterface $componentView
     * @param string $name
     * @return CompositeViewInterface
     */
    public function appendComponentView(ViewInterface $componentView=null, $name): CompositeViewInterface ;


    /**
     * Metoda pro přidání komponentních view jako pole nebo \Traversable objekt.
     * @param iterable $componentViews
     * @return \Pes\View\CompositeViewInterface
     */
    public function appendComponentViews(iterable $componentViews): CompositeViewInterface ;
}
