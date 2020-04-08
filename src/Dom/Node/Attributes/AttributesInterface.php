<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Dom\Node\Attributes;

/**
 *
 * @author pes2704
 */
interface AttributesInterface extends \IteratorAggregate, \Countable {

    /**
     * Metoda vrací hodnoty a názvy atributů elementu jako asociativní pole.
     * @return array
     */
    public function getAttributesArray();

    /**
     * Přidá nebo přepíše hodnoty atributů podle asociativního pole zadaného jako parametr.
     * @param array $attributesArray
     */
    public function addAttributesArray(): self;

    /**
     * Přidá nebo přepíše hodnotu pojmenovaného atributu.
     * @param string $name
     * @param string $value
     */
    public function setAttribute($name, $value): self;

    public function getAttribute($name);

    public function hasAttribute($name);

    /**
     * Metoda vrací hodnoty a názvy atributů elementu jako string ve tvaru vhodném pro vypsání html elementu.
     *
     * Podle typu hodnoty atributu:
     * <ul>
     * <li>Atributy s logickou hodnotou uvede jen jako jméno parametru</li>
     * <li>Atributy s hodnotou typu array jako dvojici jméno="řetězec hodnot oddělených mezerou", řetězec hodnot vytvoří zřetězením hodnot v poli oddělených mezerou a obalí uvozovkami</li>
     * <li>Ostatní atributy jako dvojici jméno="hodnota" s tím, že hodnotu prvku obalí uvozovkami.</li>
     * </ul>
     * Výsledná návrácený řetězec začíná mezerou a atributy v řetězci jsou odděleny mezerami.
     *
     * Příklad:
     * ['id'=>'alert', 'class'=>['ui', 'item', 'alert'], 'readonly'=>TRUE, data-info=>'a neni b'] převede na: id="alert" class="ui item alert" readonly data-info="a neni b".
     * Víceslovné řetězce (typicky class) lze tedy zadávat jako pole nebo víceslovný řetězec.
     *
     * @return string
     */
    public function getString();

    /**
     * Magická metoda, alias pro getString.
     */
    public function __toString() ;
    }
