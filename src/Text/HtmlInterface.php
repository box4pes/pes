<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Text;

/**
 *
 * @author pes2704
 */
interface HtmlInterface {

    /**
     * Metoda generuje textovou reprezentaci atributů html tagu z dat zadaných jako asociativní pole.
     * - Atributy s logickou hodnotou: generuje pro hodnotu TRUE jen jméno parametru, pro hodnotu FALSE nic
     * - ostatní atributy: generuje dvojici jméno="hodnota" s tím, že hodnotu prvku obalí uvozovkami. Výsledný
     * řetězec začíná mezerou a atributy v řetězci jsou odděleny mezerami.
     *
     * Příklad:
     * ['class'=>'ui item alert', 'readonly'=>TRUE, ] převede na: class="ui item alert" readonly
     * ['class'=>'ui item ok', 'readonly'=>FALSE, ] převede na: class="ui item ok"
     *
     * @param array $attributesArray Asocitivní pole
     * @return string
     */
    public static function attributes($array);

    /**
     * Generuje html kód párového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param array $attributes Asociativní pole. Viz metoda attributes().
     * @param string $innerHtml Text, bude bez úprav vložen jako textový obsah tagu
     * @return string
     */
    public static function tag($name, array $attributes=[], $innerHtml='');

    /**
     * Generuje html kód nepárového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param array $attributes Asociativní pole. Viz metoda attributes().
     * @return string
     */
    public static function tagNopair($name, array $attributes=[]);
}
