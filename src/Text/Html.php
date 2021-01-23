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

}
