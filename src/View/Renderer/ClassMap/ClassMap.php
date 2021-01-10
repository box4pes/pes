<?php

namespace Pes\View\Renderer\ClassMap;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MenuClassMap
 *
 * @author pes2704
 */
class ClassMap implements ClassMapInterface {

    const NO_CLASS_SELECTED = 'no-class-selected-in-classmap';

    private $classMapArray;

    /**
     * Konstruktor, přijímá dvouúrovňové asociativní pole classmap s definicemi css class atributů.
     *
     * První úroveň odpovídá jednotlivým částem html struktury generované komponentem, lteré jsou renderované samostatným rendererem.
     *
     * Druhá úroveň pak obsahuje definece class atributů jednotlivých html elementů v dané čísti html, ty mohou být indexovány libovolně, ale doporučuje se
     * indexovat je systematicky obdobně jako selektory v css.
     *
     * Příklad:
     * <pre>
     * [
     *      'MenuWrap' =>   [
     *                         'ul' => 'ui tiny text menu edit',
     *                      ],
     *       'LevelWrap' => [
     *                         'ul' => 'menu'
     *                      ],
     *       'Item' =>      [
     *                         'li' => 'item',
     *                         'li.onpath' => 'item selected',
     *                         'li a' => '',
     *                       ],
     * ]
     * </pre>
     *
     * @param array $classMapArray
     */
    public function __construct(array $classMapArray) {
        $this->classMapArray = $classMapArray;
    }

    /**
     * Vrací defini atributu class.
     * @param string $part Označení části html, obvykle renderované samostatnou metodou renderereru.
     * @param string $selector Selektor definice class atributu pro html elment-
     * @return type
     */
    public function getClass($part, $selector) {
        return $this->classMapArray[$part][$selector] ?? '';
    }

    /**
     * Podle zadané hodnoty podmínky vrací při splnění podmínky definici atributu class zadanou selektorem $selectorTrue,
     * při nesplnění podmínky definici class zadanou selektorem $selectorFalse. Selektor $selectorFalse nemusí být zadan, pak při nesplnění podmínky metoda vrací
     * řetezec zadaný konstantou třídy NO_CLASS_SELECTED. Tento řetězec se pak objeví jako hodnota atributu class a vzhledem k neexistenci takto pojmenované css třídy slouží jen jako poznámka ve výsledném html.
     *
     * @param bool $condition Podmínka.
     * @param string $part Označení části html, obvykle renderované samostatnou metodou renderereru.
     * @param string $selectorTrue Selektor definice vracený při splnění podmínky.
     * @param string $selectorFalse Nepovinný. Selektor definice vracený při nesplnění podmínky.
     * @return string
     */
    public function resolveClass($condition, $part, $selectorTrue, $selectorFalse=NULL) {
        if ($condition) {
            return $this->getClass($part, $selectorTrue);
        } else {
            if (isset($selectorFalse)) {
                return $this->getClass($part, $selectorFalse);
            } else {
                return self::NO_CLASS_SELECTED;
            }
        }
    }
}
