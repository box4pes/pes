<?php

namespace Pes\View\Renderer\ClassMap;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author pes2704
 */
interface ClassMapInterface {

    /**
     * Vrací defini atributu class. Pokud není atribut pro zadanou část a selektor definován, vrací řetězec "undefined in CLASS", kde CLASS je jméno třídy objektu classmap.
     * Tento řetězec slouží jen jako poznámka ve výsledném html.
     *
     * @param string $part Označení části html, obvykle renderované samostatnou metodou renderereru.
     * @param string $selector Selektor definice class atributu pro html elment-
     * @return type
     */
    public function getClass($index, $selector);

    /**
     * Vrací definici ze zadané části vybranou odle podmínky a selektorů (klíčů).
     * Testuje zadanou podmínku a Podle zadané hodnoty podmínky vrací
     * - při splnění podmínky definici atributu class zadanou se selektorem (klíčem) $selectorTrue,
     * - při nesplnění podmínky definici class zadanou se selektorem (klíčem) $selectorFalse.
     *
     * @param bool $condition Podmínka.
     * @param string $part Označení části, obvykle skupina definic pro jeden renderer.
     * @param string $selectorTrue Selektor definice vracené při splnění podmínky.
     * @param string $selectorFalse Selektor definice vracené při nesplnění podmínky.
     * @return string
     */
    public function resolveClass($condition, $index, $selectorTrue, $selectorFalse);
}
