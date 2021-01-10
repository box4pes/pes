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
     * Vrací definici atributu class.
     * @param string $part Označení části html, obvykle renderované samostatnou metodou renderereru.
     * @param string $selector Selektor definice class atributu pro html elment-
     * @return type
     */
    public function getClass($index, $selector);

    /**
     * Podle zadané hodnoty podmínky vrací při splnění podmínky definici atributu class zadanou selektorem $selectorTrue,
     * při nesplnění podmínky definici class zadanou selektorem $selectorFalse. Selektor $selectorFalse nemusí být zadan, pak při nesplnění podmínky metoda vrací
     * řetezec zadaný konstantou třídy NO_CLASS_SELECTED. Tento řetězec se pak objeví jako hodnota atributu class a slouží jen jako poznámka vr výsledném html.
     *
     * @param bool $condition Podmínka.
     * @param string $part Označení části html, obvykle renderované samostatnou metodou renderereru.
     * @param string $selectorTrue Selektor definice vracený při splnění podmínky.
     * @param string $selectorFalse Nepovinný. Selektor definice vracený při nesplnění podmínky.
     * @return string
     */
    public function resolveClass($condition, $index, $selectorTrue, $selectorFalse=NULL);
}
