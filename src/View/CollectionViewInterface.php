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
interface CollectionViewInterface extends ViewInterface {

    /**
     * Přijímá iterable kolekci view (položky typu ViewInterface).
     * Kompozitní view při renderování nahradí proměnou daného jména zřetězenými komponentními view z kolekce. Jednotlivá view z kolekce převede na string voláním metodu __toString().
     * Pokud předaná hodnota komponentního view je null, musí kompozitní view proměnnou nahrazovat prázdným řetězcem.
     *
     * @param iterable $componentViewCollection
     * @return ViewInterface
     */
    public function appendComponentViewCollection(iterable $componentViewCollection): ViewInterface ;
}
