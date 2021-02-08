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
    
    /**
     * Převede text s dvakrát odřádkovanými odstavci na html paragrafy (<p></p>)
     * Vstupní text obalí na začátku a na konci otevíracím tagem <p> a koncovým tagem </p>,
     * Výskyty dvou odřádkování uvnitř textu chápe jako konec odstavce a z každého takto odděleného úseku textu vytvoří paragraf.
     * Jednoho odřádkování v textu si nijak nevšímá, váš vstupní text můžete jedním odřádkováním zalamovat libovolně, např. proto, aby byl vidět ve vašem editoru.
     * Chcete-li skutečně vytvořit odstavec, použijte dvojí odřádkování.
     *
     * Metoda nijak nemění jakékoli html značky (tagy) ani žádné viditelné znaky v textu, naopak mění odřádkování (CR, LF alias \r, \n) a whitespaces (mezery, tabelátory ad.).
     * @param type $text
     * @return string
     */
    public static function p($text);
}
