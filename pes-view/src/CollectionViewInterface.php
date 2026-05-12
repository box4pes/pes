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
     * Interface je určeno pro kompozitní view, které renderuje kolekci pořazených kompozitních view. 
     * Přijímá iterable kolekci komponentních view, která jsou všechna stejného typu (položky kolekce jsou typu ViewInterface).
     * Kompozitní view před renderováním nahradí proměnou daného jména výsledkem renderování kolekce komponentních view. 
     * 
     * Jednotlivá view z kolekce převede na string voláním metodu __toString().
     *
     * @param iterable $componentViewCollection
     * @return ViewInterface
     */
    public function appendComponentViewCollection(iterable $componentViewCollection): ViewInterface ;
}
