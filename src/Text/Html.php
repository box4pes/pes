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
 * Obsahuje statické metody, které generují html nebo tzv. filtry, které jsou určeny
 * jako pomocné metody pro transformování textu. Obvykle se takovým metodám v template systémech
 * říká filtry.
 *
 * Jedná se o klon PhpTemplateTrait.
 *
 * @author pes2704
 */
class Html {

    const FILTER_DELIMITER = "|";

    /**
     * Metoda použije postupně zleva jednotlivé filtry uvedené jako posloupnost názvů filtrů oddělených znakem FILTER_DELIMITER.
     *
     * Příklad:
     * Pro FILTER_DELIMITER = '|' volání self::filter(e|mono|p, 'Text, který bude filtrován.') způsobí postupné volání filtru (metody)
     * e(), mono(), p(). Ekvivalentně zápisu:
     * <pre>
     * self::p(self::mono(self::e('Text, který bude filtrován.')));
     * </pre>
     *
     * @param type $filters
     * @param type $text
     * @return type
     */
    public static function filter($filters='', $text='') {
        $names = explode(self::FILTER_DELIMITER, $filters);
        foreach ($names as $name) {
            if (array_key_exists($name, get_class_methods(self::class))) {
                $text = self::$name($text);
            }
        }
        return $text;
    }

    /**
     * Alias k metodě esc().
     *
     * @param type $text
     * @return type
     */
    public static function e($text='') {
        return self::esc($text);
    }

    /**
     * Metodu použijte na ošetření textů, které obsahují text zadaný uživatelem (např. ve formuláři). Jde o základní ochranu proti XSS útokům.
     * Metoda provede tzv escapování. Všechny znaky, které mohou v HTML mít význam, tzv. rezervované znaky HTML, převede na HTML entity.
     * Např. znak < na &lt; apod.
     * Metoda nestačí na ošetření textů vkládaných kamkoli jinam než jen do textového obsahu tagu určeného k zobrazení.
     * Nikdy nevkládejte uživatelem zadaný text do obsahu tagu <script>, do html komentáře <!-- -->, do názvu atributu, do jména tagu, do css.
     * Tato místa nelze nikdy dokonale ošetřit.
     *
     * Tato metoda escapuje i html entity, které byly v opravovaném textu, např. pokud text obsahuje "mluví o&nbsp;všem" vznikne "mluví o&amp;nbsp;všem".
     * pozor tedy také na pořadí filtrovacích metod: self::filter('e|mono', 'v neděli'); je v pořádku, zatímco self::filter('mono|e', 'v neděli');
     * oescapuje i &nbsp; vytvořené filtrem "mono".
     *
     * @param type $text
     * @return type
     */
    public static function esc($text='') {
        return htmlspecialchars($text);
    }

    /**
     * Jednoznakové předložky a spojky pro český text. Metoda vloží mezi jednoznakové předložky nebo spojky a následující slovo nedělitelnou mezeru.
     * Jednoznakové předložky a spojky jsou: k, s, v, z, o, u, i, a.
     *
     * @param type $text
     * @return type
     */
    public static function mono($text='') {
        return preg_replace('|(\s[ksvzouiaKSVZOUIA])\s|', '$1&nbsp;', trim($text));
    }

    /**
     * Převede text s dvakrát odřádkovanými odstavci na html paragrafy (<p></p>)
     * Vstupní text obalí na začátku a na konci otevíracím tagem <p> a koncovým tagem </p>,
     * výskyty dvou odřádkování uvnitř textu chápe jako konec odstavce a z každého takto odděleného úseku textu vytvoří paragraf.
     * Jednoho odřádkování v textu si nijak nevšímá, váš vstupní text můžete jedním odřádkováním zalamovat libovolně, např. proto, aby byl vidět ve vašem editoru.
     * Chcete-li skutečně vytvořit odstavec, použijte dvojí odřádkování.
     *
     * Metoda nijak nemění jakékoli html značky (tagy) ani žádné viditelné znaky v textu, naopak mění odřádkování (CR, LF alias \r, \n) a whitespaces (mezery, tabelátory ad.).
     * @param type $text
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
            $text .= self::tag("p", $attributes, trim($chunk));  // původně bylo $text .= '<p>' . trim($chunk, "\n") . "</p>\n";
        }
        // Under certain strange conditions it could create a P of entirely whitespace.
        $text = preg_replace('|<p>\s*</p>|', '', $text);
        return $text;
    }

    /**
     * Převede odřádkování v textu na značku (tag) <br />. Převádí každé odřádkování, vícenásobné odřádkování způsobí vícenásobné vložení značky.
     *
     * @param type $text
     * @return type
     */
    public static function nl2br($text='') {
        return str_replace(array("\r\n", "\r", "\n"), "<br />", $text);
    }

    /**
     * Escapuje retězec, který obsahuje javascript, který má být vložen jako inline javascript do atributu onclick, onblur atd.
     *
     * @param string $text
     */
    public static function esc_js($text='') {
        $safe_text = htmlspecialchars( $text, ENT_COMPAT );   // konvertuje &"<> na &xxx kódy (ENT_COMPAT Will convert double-quotes and leave single-quotes alone.)
        $safe_text = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $safe_text ) );  // stripslashes odstraní escapovací zpětná lomítka (vždy jedno), preg vymění &#x27; a &#039; (oboje apostrofy) za apostrof

        return $safe_text;
    }

    public static function removeDiacritics($text) {
        // i pro multi-byte (napr. UTF-8)
        $prevodni_tabulka = Array(
          'ä'=>'a',  'Ä'=>'A',  'á'=>'a',  'Á'=>'A',
          'à'=>'a',  'À'=>'A',  'ã'=>'a',  'Ã'=>'A',
          'â'=>'a',  'Â'=>'A',  'č'=>'c',  'Č'=>'C',
          'ć'=>'c',  'Ć'=>'C',  'ď'=>'d',  'Ď'=>'D',
          'ě'=>'e',  'Ě'=>'E',  'é'=>'e',  'É'=>'E',
          'ë'=>'e',  'Ë'=>'E',  'è'=>'e',  'È'=>'E',
          'ê'=>'e',  'Ê'=>'E',  'í'=>'i',  'Í'=>'I',
          'ï'=>'i',  'Ï'=>'I',  'ì'=>'i',  'Ì'=>'I',
          'î'=>'i',  'Î'=>'I',  'ľ'=>'l',  'Ľ'=>'L',
          'ĺ'=>'l',  'Ĺ'=>'L',  'ń'=>'n',  'Ń'=>'N',
          'ň'=>'n',  'Ň'=>'N',  'ñ'=>'n',  'Ñ'=>'N',
          'ó'=>'o',  'Ó'=>'O',  'ö'=>'o',  'Ö'=>'O',
          'ô'=>'o',  'Ô'=>'O',  'ò'=>'o',  'Ò'=>'O',
          'õ'=>'o',  'Õ'=>'O',  'ő'=>'o',  'Ő'=>'O',
          'ř'=>'r',  'Ř'=>'R',  'ŕ'=>'r',  'Ŕ'=>'R',
          'š'=>'s',  'Š'=>'S',  'ś'=>'s',  'Ś'=>'S',
          'ť'=>'t',  'Ť'=>'T',  'ú'=>'u',  'Ú'=>'U',
          'ů'=>'u',  'Ů'=>'U',  'ü'=>'u',  'Ü'=>'U',
          'ù'=>'u',  'Ù'=>'U',  'ũ'=>'u',  'Ũ'=>'U',
          'û'=>'u',  'Û'=>'U',  'ý'=>'y',  'Ý'=>'Y',
          'ž'=>'z',  'Ž'=>'Z',  'ź'=>'z',  'Ź'=>'Z'
        );

        return strtr($text, $prevodni_tabulka);
    }

    /**
     * Metoda generuje textovou reprezentaci atributů html tagu z dataných jako asocitivní pole.
     * Podle typu hodnoty atributu:
     * <ul>
     * <li>Atributy s logickou hodnotou uvede jen jako jméno parametru</li>
     * <li>Atributy s hodnotou typu array jako dvojici jméno="řetězec hodnot oddělených mezerou", řetězec hodnot vytvoří zřetězením hodnot v poli oddělených mezerou a obalí uvozovkami</li>
     * <li>Ostatní atributy jako dvojici jméno="hodnota" s tím, že hodnotup rvku obalí uvozovkami.</li>
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
     * Genuruje html kód párového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param array $attributes Asociativní pole. Viz metoda attributes().
     * @param string $innerHtml Text, bude bez úprav vložen jako textový obsah tagu
     * @return string
     */
    public static function tag($name, array $attributes=[], $innerHtml='') {
        if ($name) {
            $html = "<$name ".self::attributes($attributes).">".PHP_EOL.$innerHtml.PHP_EOL."</$name>";
        }
        return $html ?? '';
    }

    /**
     * Genuruje html kód párového tagu.
     *
     * @param string $name
     * @param array $attributes
     */
    public static function tagNopair($name, array $attributes=[]) {
        if ($name) {
            $html = "<$name ".self::attributes($attributes)." />";
        }
        return $html ?? '';
    }

}
