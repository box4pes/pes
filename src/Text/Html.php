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
 * Obsahuje statické metody, které generují html string.
 *
 * Jedná se o klon PhpTemplateTrait.
 *
 * @author pes2704
 */
class Html implements HtmlInterface {

    const EOL = PHP_EOL;

    /**
     * Metoda generuje textovou reprezentaci atributů html tagu z dat zadaných jako asociativní pole.
     *
     * Podle typu hodnoty atributu:
     * <ul>
     * <li>Atributy s logickou hodnotou uvede jen jako jméno parametru (standard html nikoli xml)</li>
     * <li>Atributy s hodnotou typu array jako dvojici jméno="řetězec hodnot oddělených mezerou", řetězec hodnot vytvoří zřetězením hodnot v poli oddělených mezerou a obalí uvozovkami</li>
     * <li>Ostatní atributy jako dvojici jméno="hodnota" s tím, že hodnotu prvku přčevede na string a obalí uvozovkami.</li>
     * </ul>
     * Pokud je hodnota atributu řetězec, který obsahuje uvozovky, výsledné html bude chybné. Hodnota atributu je vždy obalena uvozovkami.
     * Výsledný navrácený řetězec začíná mezerou a atributy v řetězci jsou odděleny mezerami.
     *
     * Příklad:
     * ['id'=>'alert', 'class'=>['ui', 'item', 'alert'], 'readonly'=>TRUE, data-info=>'a neni b'] převede na: id="alert" class="ui item alert" readonly data-info="a neni b".
     * Víceslovné řetězce (typicky class) lze tedy zadávat jako pole nebo víceslovný řetězec.
     * @param array $attributesArray Asocitivní pole
     * @return string
     */
    public static function attributes($attributesArray=[]) {
        foreach ($attributesArray as $type => $value) {
            if (is_bool($value)) {
                $attr[] = $type;
            } elseif (is_array($value)) {
                $attr[] = $type.'="'.implode(' ', $value).'"';
            } else {
                $attr[] = $type.'="'.trim((string) $value).'"';
            }
        }
        return isset($attr) ? implode(' ',$attr) : '';
    }

    /**
     * Generuje html kód párového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param array $attributes Asociativní pole. Viz metoda attributes().
     * @param string $innerHtml Text, bude bez úprav vložen jako textový obsah tagu
     * @return string
     */
    public static function tag($name, array $attributes=[], $innerHtml='') {
        if ($name) {
            if (is_array($innerHtml)) {
                $html = "<$name ".self::attributes($attributes).">".self::EOL.implode(self::EOL, $innerHtml).self::EOL."</$name>";
            } else {
                $html = "<$name ".self::attributes($attributes).">".self::EOL.$innerHtml.self::EOL."</$name>";
            }
        }
        return $html ?? '';
    }

    /**
     * Generuje html kód nepárového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param array $attributes Asociativní pole. Viz metoda attributes().
     * @return string
     */
    public static function tagNopair($name, array $attributes=[]) {
        if ($name) {
            $html = "<$name ".self::attributes($attributes)." />";
        }
        return $html ?? '';
    }

    /**
     * Převede text s dvakrát odřádkovanými odstavci na html paragrafy (tagy p). Umožňuje zadat atributy vytvážených tagů p (např. class pro styly).
     *
     * Ze všech úseků textu vytvoří html paragrafy, vloží tyto úseky jako obsah tagu p.
     * Výskyty dvou odřádkování uvnitř textu chápe jako konec úseku a z každého takto odděleného úseku textu vytvoří paragraf.
     * Jednoho odřádkování v textu si nijak nevšímá, váš vstupní text můžete jedním odřádkováním zalamovat libovolně, např. proto, aby byl vidět ve vašem editoru.
     * Chcete-li skutečně vytvořit odstavec, použijte v textu dvojí odřádkování.
     *
     * Vytvářené tagy p mohu být vytvářeny s atributy. Atributy jsou zadány nepoviným parametrem $attributes ve formě asociativního pole. Viz metoda attributes().
     *
     * Metoda nijak nemění jakékoli html značky (tagy) ani žádné viditelné znaky v textu, naopak mění odřádkování (CR, LF alias \r, \n) a whitespaces (mezery, tabelátory ad.).
     * @param string $text Vstupní text
     * @param array $attributes Nepoviný parametr. Atributy vytvářených tagů p zadané jako asociativní pole. Viz metoda attributes().
     * @return string
     */
    public static function p($text='', $attributes=[]) {
        // kopie z https://core.trac.wordpress.org/browser/trunk/src/wp-includes/formatting.php
        //
        if ( trim($text) === '' )
        return '';

        // Just to make things a little easier, pad the end.
//        $text = $text . "\n";
        // Standardizuje odřádkování na \n
        $text = str_replace(array("\r\n", "\r"), "\n", $text);
        // Odstraní více než dvě odřádkování za sebou
        $text = preg_replace("/\n\n+/", "\n\n", $text);
        // Rozdělí na kousky, separátor jsou dvě odřádkování (mezi může být libovolný počet whitespaces)
        $chunks = preg_split('/\n\s*\n/', $text, -1, PREG_SPLIT_NO_EMPTY);  //PREG_SPLIT_NO_EMPTY - preg_split vrací jen neprázné kousky

        // Složí text z kousků obalených počátečním a koncovým <p>
        $text = '';
        foreach ( $chunks as $chunk ) {
            $text .= self::tag("p", $attributes, trim($chunk)).self::EOL;  // původně bylo $text .= '<p>' . trim($chunk, "\n") . "</p>\n";
        }
        // Under certain strange conditions it could create a P of entirely whitespace.
        $text = preg_replace('|<p>\s*</p>|', '', $text);
        return $text;
    }
}
