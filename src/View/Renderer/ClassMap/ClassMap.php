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

    private $classMapArray;

    /**
     * Konstruktor, přijímá dvouúrovňové asociativní pole classmap s definicemi css class atributů.
     *
     * První úroveň odpovídá jednotlivým částem html struktury generované komponentem, které jsou renderované samostatným rendererem.
     *
     * Druhá úroveň pak obsahuje definice class atributů jednotlivých html elementů v dané čísti html, ty mohou být indexovány libovolně, ale doporučuje se
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
     * Vrací defini atributu class. Pokud není atribut pro zadanou část a selektor definován, vrací řetězec "undefined in CLASS", kde CLASS je jméno třídy objektu classmap.
     * Tento řetězec slouží jen jako poznámka ve výsledném html.
     *
     * @param string $part Označení části html, obvykle renderované samostatnou metodou renderereru.
     * @param string $selector Selektor definice class atributu pro html elment-
     * @return type
     */
    public function getClass($part, $selector) {
        return $this->classMapArray[$part][$selector] ?? "undefined_in_classmap";
    }

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
    public function resolveClass($condition, $part, $selectorTrue, $selectorFalse) {
        if ($condition) {
            return $this->getClass($part, $selectorTrue);
        } else {
            if (isset($selectorFalse)) {
                return $this->getClass($part, $selectorFalse);
            }
        }
    }
}
